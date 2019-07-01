<?php
Class Controller_Profile Extends Controller_Base {

    //Покажем профиль игрока
	function Index($r) {
		// Проверим, что передан id игрока или username
		if (isset($r['Params']['username']))
    		$username = strtolower($r['Params']['username']);
    	else	
    		$username = '';

		
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		elseif (isset($r['Params']['p_Id']))
			$p_Id = $r['Params']['p_Id'];
		else
		    $p_Id = -1;
		 
		if (!is_numeric ($p_Id)) 
		    $p_Id=-2;
		    
    	// Передан игрок?
    	if (($p_Id < 0) && ($username==''))
		    throw new Exception('Для отображения профиля необходимо указать передать Id игрока или username форума',-1);

		if ($p_Id>0)
            $PlayerModel = new Model_Player($p_Id);
        elseif ($username>'')
            $PlayerModel = Model_Player::GetPlayerByUsername($username);

	    $smarty = $r['smarty'];

        $RatingHistory = $PlayerModel->GetRatingHistory();

        $ExpiredPenaltiesList = Model_Penalty::GetPenaltiesList($PlayerModel->p_Id, 1);
        $NonExpiredPenaltiesList = Model_Penalty::GetPenaltiesList($PlayerModel->p_Id, 0);

		$smarty->assign('title', $PlayerModel->p_Name.' - профиль игрока. Саратовская любительская лига по настольному теннису');	
		$smarty->assign('Player', $PlayerModel);	
		$smarty->assign('RatingHistory', $RatingHistory);	
		$smarty->assign('BBCodedpInfo', $this->DoBBCode($PlayerModel->p_Info));	
		$smarty->assign('ExpiredPenaltiesList', $ExpiredPenaltiesList);	
		$smarty->assign('NonExpiredPenaltiesList', $NonExpiredPenaltiesList);	
		
		$smarty->display('Profile/Profile.tpl');        
	}


    // TODO: удалить после перехода на базу знаний по 
    function DoBBCode($txt)
    {
    	$imgpath = 'http://tt-saratov.ru/statistics/mvc/images/';
    	$res = $txt;
    
    	$res = str_ireplace('[blade]','<img src="'.$imgpath.'blade.png">',$res);	
    	$res = str_ireplace('[red]','<img src="'.$imgpath.'red.png">',$res);	
    	$res = str_ireplace('[black]','<img src="'.$imgpath.'black.png">',$res);
    
    	$res = str_ireplace('[b]','<b>',$res);
    	$res = str_ireplace('[/b]','</b>',$res);
    	$res = str_ireplace('[i]','<i>',$res);
    	$res = str_ireplace('[/i]','</i>',$res);
    	$res = str_ireplace('[u]','<u>',$res);
    	$res = str_ireplace('[/u]','</u>',$res);
    
    	$res = str_ireplace('[video]','<iframe width="300" height="225" src="',$res);
    	$res = str_ireplace('[/video]','" frameborder="0" allowfullscreen></iframe>',$res);
    
    
    	// простая ссылка
    	$urlpos = stripos($res, '[url]');
    	$closeurlpos = stripos($res, '[/url]', $urlpos);
    
    	while ($urlpos!==false && $closeurlpos)
    	{
    		$url = substr($res, $urlpos+5, $closeurlpos-$urlpos-5);
    
    		if ( stripos($url,"http://") === FALSE )
    			$url = "http://".$url;
    
    		$res = substr_replace($res, '<a target="_blank" href="'.$url.'">'.$url.'</a>', $urlpos, $closeurlpos-$urlpos+6);
    
    		$urlpos = stripos($res, '[url]');
    		$closeurlpos = stripos($res, '[/url]', $urlpos);
    	}	
    
    
    	// ссылка с подписью
    	$urlpos = stripos($res, '[url=');
    	$labelpos = stripos($res, ']', $urlpos);
    	$closeurlpos = stripos($res, '[/url]', $labelpos);
    
    	while ($urlpos!==false && $closeurlpos && $labelpos)
    	{
    		
    		$url = substr($res, $urlpos+5, $labelpos-$urlpos-5);
    
    		if ( stripos($url,'http://') === FALSE )
    			$url = 'http://'.$url;
    
    		$label = substr($res, $labelpos+1, $closeurlpos-$labelpos-1);
    		$res = substr_replace($res, '<a target="_blank" href="'.$url.'">'.$label.'</a>', $urlpos, $closeurlpos-$urlpos+6);
    
    		$urlpos = stripos($res, '[url=');
    		$labelpos = stripos($res, ']', $urlpos);
    		$closeurlpos = stripos($res, '[/url]', $labelpos);
    
    	}	
    
    
    	
    	return $res;
    }    


    // Закладки с годами, когда игрок играл
    function PlayerActiveYears($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else 
			return;

        $m_player = new Model_Player($p_Id);
    	$Years = $m_player->getPlayerActiveYears();
        krsort($Years); // отсортируем в обратном порядке
        
	    $smarty = $r['smarty'];

		$smarty->assign('Years', $Years);	
		$smarty->assign('p_Id', $p_Id);	

		$smarty->display('Profile/YearsList.tpl');        
    }


	// Возвращаем табличку с турнирами игрока
	function PlayerTournaments($r) {	
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else {
			return;
		}

		// Проверим, что передан год
		if (isset($r['Params']['Year']))
			$Year = $r['Params']['Year'];
		else {
			$Year = -1;
		}

        try {


    		$m_player = new Model_Player($p_Id);

            //Получим статистику по годам
        	$Years = $m_player->getPlayerActiveYears();

            // Получим турниры за выбранный год
    		$Tournaments = $m_player->GetPlayerTournaments($Year);

			// Отсортируем турниры в обратном порядке
			// set Date as array key
			foreach ($Tournaments as $key => $tour) {
				$Tournaments[$tour['t_DateTime']] = $tour;
				unset($Tournaments[$key]);
			}
			krsort($Tournaments); // отсортируем в обратном порядке
			
			
    	    $smarty = $r['smarty'];

    		$smarty->assign('Tournaments', $Tournaments);	
    		$smarty->assign('YearStatistics', $Years[$Year]);	
    		$smarty->assign('Player', $m_player);	

    		$smarty->display('Profile/TournamentList.tpl');        
        }
        catch (Exception $e) {
    		header('HTTP/1.0 500 Internal Server Error', true, 500);
    		echo "<div>Action PlayerTournaments exception: ".$e->getMessage()."</div>";
    		exit;
        }
	}


	// Возвращаем табличку со встречами игрока с фильтром по турниру
	function PlayerTourMeetings($r) {	
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$PlayerId = $r['Params']['PlayerId'];
		else {
			return;
		}

		// Проверим, что передан id Турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else {
			$TourId = -1;
		}

        try {
    		$Meetings = Model_Meeting::getMeetings($TourId, -1, $PlayerId);
//die(json_encode($Meetings));
    	    $smarty = $r['smarty'];

    		$smarty->assign('Meetings', $Meetings);	
    		$smarty->assign('Player', new Model_Player($PlayerId));	

    		$smarty->display('Profile/MeetingsList.tpl');        
        }
        catch (Exception $e) {
            header ("Ошибка при построении списка встреч $e", true, 500);
            echo "Ошибка при построении списка встреч $e";
			//http_response_code(500);
        }
	}


   // Показывает график рейтинга
    function RatingChart($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['p_Id']))
			$p_Id = $r['Params']['p_Id'];
		else {
			return;
		}
		
        $p = Model_Player::GetOne($p_Id);
		$rh = $p->GetRatingHistory($from, $to);

		$maxrh = (count($rh) ? $rh[0] : null);
		if (isset($maxrh)) {
		    foreach ($rh as $rateRow) {
		        if ($rateRow->pr_Rate > $maxrh->pr_Rate)
		            $maxrh = $rateRow;
		    }
		}

		$smarty = $r['smarty'];
    	$smarty->assign('RatingHistory', $rh);   
    	$smarty->assign('MaxRatingHistoryRow', $maxrh);   

		$smarty->display('Profile/RatingChart.tpl');
    }


   // Показывает разряды игрока
    function Ranks($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else {
			echo 'Надо передать Id игрока';
		}
		
        $PlayerRanks = Model_PlayerRank::getPlayerRanks($p_Id);
        $VictoriesCount = Model_Meeting::getVictoryOverRankedPlayersCount($p_Id);

// die(json_encode($PlayerRanks));
		$smarty = $r['smarty'];
    	$smarty->assign('p_Id', $p_Id);   
    	$smarty->assign('PlayerRanks', $PlayerRanks);   
    	$smarty->assign('VictoriesCount', $VictoriesCount);   

		$smarty->display('Profile/PlayerRanks.tpl');
    }


    // Показыватет победы, влияющие на разряды
    function EarningRankWins ($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else {
			echo 'Надо передать Id игрока';
			die();
		}

		// Проверим, что передано начало временного интервала
		if (isset($r['Params']['from']))
			$from = $r['Params']['from'];
		else 
		    $from = null;

		// Проверим, что передано начало временного интервала
		if (isset($r['Params']['to']))
			$to = $r['Params']['to'];
		else 
		    $to = null;

        $Wins = Model_Meeting::getVictoryOverRankedPlayers($p_Id, $from, $to);
        
		$smarty = $r['smarty'];
    	$smarty->assign('Meetings', $Wins);   

		$smarty->display('Statistics/MeetingsList.tpl');
    } // function EarningRankWins


    // отправляет сведения о разряде
    function reportRank($r) {
        $body = json_encode( $_POST, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
        $fileInfo = "Подтверждающий документ не предоставлен";
		if (isset($_FILES['file']))
			if(is_uploaded_file($_FILES["file"]["tmp_name"])) {
				if($_FILES["file"]["size"] > 1024*10*1024)
					$fileInfo ='Размер файла превышает десять мегабайт. Можно загружать только .jpg .jpeg .png .pdf файлы размером не более 10 Mb.';

				$extension = strtolower(pathinfo($photofn, PATHINFO_EXTENSION));
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "pdf"))	
					$fileInfo = 'Неизвестное расширение файла. В качестве подтверждения можно загружать только .jpg .jpeg .png .pdf файлы размером не более 10 Mb.';

				// Сохраним файл в папке Ranks
				$toFile = 'Ranks'.DIRSEP.$_POST['p_Id'].'-'.$_FILES["file"]["name"];
                copy($_FILES["file"]["tmp_name"] , site_path.'..'.DIRSEP.$toFile);
                
                $fileInfo = "Подтверждающий документ можно скачать по ссылке http://tt-saratov.ru/statistics/$toFile";
			}
        $body .= "\n$fileInfo";
			
        Registry::sendmail( 'ttsaratov@gmail.com', 'Сведения о квалификации', $body);
        
        
        Registry::sendmail( $_POST['senderEMail'], 'Сведения о квалификации', 'Ваше сообщение о квалификации игрока принято. 
        После проверки сведения будут опубликованы. 
        О результатах Вы будете уведомлены по электронной почте. 
        
        С уважением, команда саратовской любительской лиги по настольному теннису.');
        header("Location: ?ctrl=Profile&PlayerId={$_POST['p_Id']}");
    }   //  function reportRank($r)


    // проверяет наличие игрока по форумному имени и если есть, то выводит кнопку для перехода к профилю
    function checkprofilebyusername($r) {
		// Проверим, что передан username игрока
		if (isset($r['Params']['username']))
			$username = strtolower($r['Params']['username']);
		else {
			return;
		}
    
    	header('Content-Disposition: attachment; filename=icon_player_profile.gif');
    	header("Content-type: image/gif");
    
    	// получим игрока из БД
    	$player = Model_Player::GetPlayerByUsername($username);
        if (isset($player)) 
    		echo file_get_contents('images/icon_player_profile.gif');
        else
            echo file_get_contents('images/pixel.gif');
    }
    
}
?>