<?php
Class Controller_Players Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      		= anonym_AR;
        $AccessMatrix['GetAvatar']  		= anonym_AR;
        $AccessMatrix['GetPhoto']  			= anonym_AR;
        $AccessMatrix['GetOpponents']  		= anonym_AR;
        $AccessMatrix['FilteredPlayers']	= anonym_AR;
        $AccessMatrix['GetBirthdays']  		= anonym_AR;

		$AccessMatrix['xml']				= admin_AR;
		$AccessMatrix['GetPlayerFormRankTR']= admin_AR;
		$AccessMatrix['AdminList']  		= admin_AR;
        $AccessMatrix['AdminPlayers']   	= admin_AR;
        $AccessMatrix['New']  				= admin_AR;
        $AccessMatrix['Edit']  				= admin_AR;
        $AccessMatrix['Save']       		= admin_AR;
        $AccessMatrix['Delete']     		= admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
	
	
	function Index($r) {
        echo 'Not defined.';
    }

	
	function xml($r) {
	    $query = "select 
					p.p_Id,
					p.p_Name,
					DATE_FORMAT(p.p_Birthdate,'%d.%m.%Y') p_Birthdate,
					z1.pr_Rate 
				from 
					x_Players p
					left join
					x_PlayerRateHistory z1 on z1.pr_PlayerId = p.p_Id
					left join 
					x_PlayerRateHistory z2 on	z1.pr_PlayerId = z2.pr_PlayerId and 
												z2.pr_Date >= z1.pr_Date 
				group by 
					p.p_Id,
					p.p_Name,
					p.p_Birthdate,
					z1.pr_PlayerId, 
					z1.pr_Date, 
					z1.pr_Rate 
				having 
					count(2) = 1 
				order by 
					p.p_Name; ";
	    $Players = Model_Base::ExecArraySQL($query);
		
		$xml = '';
		
		$xml .= '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
	<Players>';
		foreach ($Players as $player) {
			$xml .= "\r\n".'	<Player bNew="true" id="'.$player['p_Id'].'">
			<name>'.$player['p_Name'].'</name>
			<shortname>'.$this->FIOshortener($player['p_Name']).'</shortname>
			<location/>
			<birthdate>'.$player['p_Birthdate'].'</birthdate>
			<sex/>
			<rating>'.$player['pr_Rate'].'</rating>
			<deltaR>0.00</deltaR>
			<stats>0;0;0</stats>
			<stats2>0;0;0</stats2>
		</Player>';
		}
		$xml .= '</Players>';

		header("Content-Disposition: attachment; filename=Players.xml");
		header("Content-type: text/xml");
		echo $xml;
				
	}
	
	private function FIOshortener($fullFIO){
		$compressed = preg_replace("/\\s+/iu"," ",trim($fullFIO));
		$parts = explode(' ', $compressed);
		switch (count($parts)) {
			case 0:
				$short = "!пустое фио";
				break;
			case 1:
				$short = $compressed;
				break;	
			case 2:
				$short = $parts[0]." ".$parts[1];
				break;
			default:
				$short = $parts[0]." ".substr($parts[1], 0, 2).".".substr($parts[2], 0, 2).".";
		}
		 
		return $short;		
	}
	
	function AdminPlayers($r) {
	    $smarty = $r['smarty'];
    
		$smarty->display('Players/AdminPlayers.tpl');        
	}

	
	function AdminList($r) {
        $needle = $r['Params']['needle'];
	    setcookie ('AdminPlayerSearch',$needle);

	    $smarty = $r['smarty'];

        
        $pList = Model_Player::GetList("p_Name like '%$needle%' order by p_Name", '*');

        $smarty->assign('PlayersList', $pList);

		$smarty->display('Players/AdminList.tpl');        
	}

	
	// форма редактирования игрока
	function Edit($r) {
        $p_Id = $r['Params']['PlayerId'];
		if (!$p_Id)
			throw new Exception('Parameter PlayerId needed');
		
	    $smarty = $r['smarty'];

		$forumUsers = Model_ForumUser::GetList('true order by username',array('user_id','username','username_clean','user_birthday'));

		// TODO:указать список полей для загрузки , чтобы получать все данные за один запрос к БД
		$m_player = new Model_Player($p_Id, '*');
		$m_player_rank = Model_PlayerRank::GetList( 'pr_PlayerId = :p_Id ORDER BY pr_DateFrom', '*', array('p_Id' => $p_Id) );
		$ranks = Model_Rank::GetList();

		$smarty->assign('Player', $m_player);
		$smarty->assign('PlayerRanks', $m_player_rank);
		$smarty->assign('ForumUsers', $forumUsers);
		$smarty->assign('Ranks', $ranks);

		$smarty->display('Players/PlayerFormEdit.tpl');        
	}
	
	
	// строка с квалификацией в форме редактирования игрока
	function GetPlayerFormRankTR($r) {
        $pr_Id = $r['Params']['pr_Id'];
        $pr_PlayerId = $r['Params']['pr_PlayerId'];
		
		if ( !$pr_Id || !$pr_PlayerId )
			die('Parameter pr_Id and pr_PlayerId needed');
		
	    $smarty = $r['smarty'];


		if ($pr_Id>0)
			$rank = Model_PlayerRank::GetOne( $pr_Id , '*' );
		else
			$rank = Model_PlayerRank::GetOne( null , array(
														'pr_Id' 		=> $pr_Id,
														'pr_PlayerId'	=> $pr_PlayerId
													) 
											);
		$Ranks = Model_Rank::GetList();

		$smarty->assign('rank', $rank);
		$smarty->assign('Ranks', $Ranks);

		$smarty->display('Players/PlayerFormEditRankTR.tpl');        
	}	//	function GetPlayerFormRankTR($r)
	
	
	// сохранить сведения об игроке
	function Save($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Players&act=AdminPlayers"; // список Форма со списком игроков

		$playerData = $r['Params']['playerData'];
		
		// Преобразуем ON в checkbox'ах в 0 и 1
		if ($playerData['p_ActivatedLogin'])
			$playerData['p_ActivatedLogin'] = 1;
		else
			$playerData['p_ActivatedLogin'] = 0;

		if ($playerData['p_EMailConfirmed'])
			$playerData['p_EMailConfirmed'] = 1;
		else
			$playerData['p_EMailConfirmed'] = 0;
		
		// Фото
		if (isset($_FILES['p_Photo']))
			if(is_uploaded_file($_FILES["p_Photo"]["tmp_name"])) {
				if($_FILES["p_Photo"]["size"] > 1024*1*1024)
					throw Exception('Размер файла превышает один мегабайт. В качестве фото можно загружать только .jpg .jpeg .png файлы размером не более 1 Mb.');

				// из временной директории в конечную
				$photofn = $_FILES["p_Photo"]["name"];

				// get the extension of the file in a lower case format
				$extension = strtolower(pathinfo($photofn, PATHINFO_EXTENSION));
				// if it is not a known extension, we will suppose it is an error, print an error message 
				//and will not upload the file, otherwise we continue
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png"))	
					throw Exception('Неизвестное расширение файла. В качестве фото можно загружать только .jpg .jpeg .png файлы размером не более 1 Mb.');

				// Загружаем его
				$tmpfn = $_FILES["p_Photo"]["tmp_name"];

				$f=fopen($tmpfn, "rb"); 				
				
				// имя файла или картинки -- открыли файл на чтение
				$photo=fread($f,filesize($tmpfn)); // считали файл в переменную
				fclose($f); // закрыли файл, можно опустить
				//защитим переменную от опасных символов
				//$photo=mysql_escape_string($photo);
				
				$playerData['p_PhotoFN']	= $photofn;
				$playerData['p_Photo']		= $photo;
			}	

		// Аватар
		if (isset($_FILES['p_Avatar']))
			if(is_uploaded_file($_FILES["p_Avatar"]["tmp_name"])) {
				if($_FILES["p_Avatar"]["size"] > 1024*1*1024)
					throw Exception('Размер файла превышает один мегабайт. В качестве аватара можно загружать только .jpg .jpeg .png файлы размером не более 1 Mb.');

				// из временной директории в конечную
				$avatarfn = $_FILES["p_Avatar"]["name"];

				// get the extension of the file in a lower case format
				$extension = strtolower(pathinfo($avatarfn, PATHINFO_EXTENSION));
				// if it is not a known extension, we will suppose it is an error, print an error message 
				//and will not upload the file, otherwise we continue
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png"))	
					throw Exception('Неизвестное расширение файла. В качестве аватара можно загружать только .jpg .jpeg .png файлы размером не более 1 Mb.');

				// Загружаем его
				$tmpfn = $_FILES["p_Avatar"]["tmp_name"];

				$f=fopen($tmpfn, "rb"); 				
				
				// имя файла или картинки -- открыли файл на чтение
				$avatar=fread($f,filesize($tmpfn)); // считали файл в переменную
				fclose($f); // закрыли файл, можно опустить
				//защитим переменную от опасных символов
				// $avatar=mysql_escape_string($avatar);
				
				$playerData['p_AvatarFN']	= $avatarfn;
				$playerData['p_Avatar']		= $avatar;
			}	

		$p = new Model_Player(null,$playerData);
// die(json_encode($p));
        $p->Save();
		
		

		// СОХРАНИМ РАЗРЯДЫ
		if (isset($r['Params']['playerRankData'])) {
			
			foreach ($r['Params']['playerRankData'] as $pr_Id => $prData) {
				
				$pr = new Model_PlayerRank(null,$prData);
			
//				$prs[]=$pr;
				if ($pr->Delete)
					$pr->Delete();
				else
					$pr->Save();
			}
//			die(json_encode($prs));
		}
		
/*
		$smarty = $r['smarty'];
		$forumUsers = Model_ForumUser::GetList('true order by username',array('user_id','username','username_clean','user_birthday'));
		$m_player = new Model_Player($p_Id);

		$smarty->assign('Player', $m_player);
		$smarty->assign('ForumUsers', $forumUsers);

		$smarty->display('Players/PlayerFormEdit.tpl');        
*/
		//Вернёмся на исходную страницу
		header("Location: $BackURL");	
	}
	
	
	
	// Возвращает аватар игрока
	function GetAvatar($r) {	
		// Проверим, что передан один параметр
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else 
			throw new Exception("Для получения аватара игрока должен быть передан один целочисленный параметр - идентификатор игрока!");

		$m_player = new Model_Player($p_Id);

		header('Content-Disposition: attachment; filename='.$m_player->p_AvatarFN);
		header("Content-type: image/png");
		echo $m_player->p_Avatar;
	}


	// Возвращает фото игрока
	function GetPhoto($r) {
		// Проверим, что передан один параметр
		if (isset($r['Params']['PlayerId']))	
			$p_Id = $r['Params']['PlayerId'];
		elseif (isset($r['Params']['p_Id']))	//TODO: убрать p_Id
			$p_Id = $r['Params']['p_Id'];
		else 
			throw new Exception("Для получения фото игрока должен быть передан один целочисленный параметр - идентификатор игрока!");
		
		// TODO:указать список полей для загрузки , чтобы получать фото за один запрос 
		$m_player = new Model_Player($p_Id);

	//	header('Content-Disposition: attachment; filename='.$m_player->p_PhotoFN);
		header("Content-type: image/png");
		echo $m_player->p_Photo;
	}
	
	
	// Возвращает соперников игрока в JSON
	function GetOpponents($r) {	
		// Проверим, что передан один параметр
		if (isset($r['Params']['p_Id']))
			$p_Id = $r['Params']['p_Id'];
		else {
			return;
		}

        try {
    		$m_player = new Model_Player($p_Id);
    		
			header('Content-Type: application/json');
    		echo json_encode($m_player->GetPlayerOpponents());
    		
        }
        catch (Exception $e) {
			http_response_code(500);
        }
	}


 	// возвращает список игроков по части имени для autocomplete
	function FilteredPlayers($r) {	
		// Проверим, что передана строка для фильтрации по имени
		if (isset($r['Params']['term'])) {
			$term = $r['Params']['term'];
			$alternateTerm = Model_Player::correctKeyboard($term);
		}
		else {
			return;
		}

		// Проверим, что передана группа турнира игрока 
		if (isset($r['Params']['GroupId']))
			$GroupId = $r['Params']['GroupId'];
		else 
			$GroupId = 0;


        try {
            $players = array();
            if ($GroupId) { // Если задана группа то сначала отберём игроков по группе
                $GroupPlayers = Model_TourGroupPlayer::GetTourGroupPlayers($GroupId, $term);
                foreach ($GroupPlayers as $gplayer)
                    $players[] = $gplayer->gp_PlayerId('Model_Player');

            }
            else // Если группа не задана, то получим игроков по имени
    		    $players = Model_Player::GetList("p_Name like '%$term%' or p_Name like '%$alternateTerm%' order by p_Name", array("p_Id","p_Name","p_Birthdate"));
    	
    	    $result = array();
    	    foreach ($players as $player)
        	    $result[] = array(
        	                        "p_Id"=>$player->p_Id, 
        	                        "label"=>$player->p_Id." - ".$player->p_Name."(".( is_object($player->p_Birthdate) ? $player->p_Birthdate->format('Y').'г.р.':'-').")", 
        	                        "value"=>$player->p_Name,
        	                        "p_Rate"=>$player->GetRate()->pr_Rate
        	                       );
    		
    		//header('Content-Disposition: attachment; filename=FilteredPlayers.json');
			header('Content-Type: application/json');
    		echo json_encode($result);
        }
        catch (Exception $e) {
			http_response_code(500);
        }
	}   //  function FilteredPlayers($r)


   // Покажем ближайшие дни рождения
    function GetBirthdays($r) {
		// Проверим, что передано количество дней, за которое надо информировать о дне рождения
		if (isset($r['Params']['FutureDays']))
			$FutureDays = $r['Params']['FutureDays'];
		else {
			$FutureDays = 0; // если не передано, то покажем только сегодняшних именинников
		}

		// Проверим, что передан день, на который надо показать
		if (isset($r['Params']['Today']))
			$Today = $r['Params']['Today'];
		else {
			$Today = date("Y-m-d"); // если не передано, то покажем на сегодня
		}
		
        $PlayersArray = Model_Player::GetNearestPlayersBirthday($FutureDays, $Today);
        
	    $smarty = $r['smarty'];

		$smarty->assign('Players', $PlayersArray);	
		
		$smarty->display('Players/NearestBirthdays.tpl');        
        
    }


    // Возвращает информацию об одном игроке в JSON
/* сделал, не понадобилась. закоментировал
    function PlayerInfo($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$PlayerId = $r['Params']['PlayerId'];
		else
			return;
		
		$p = Model_Player::GetOne($PlayerId,'*');

		header("Content-Disposition: attachment; filename=PlayerInfo$PlayerId.json");
		header('Content-Type: application/json');
		echo json_encode($p);
    }
*/
  
}
?>