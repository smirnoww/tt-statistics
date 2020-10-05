<?php
Class Controller_TourGroups Extends Controller_Base {


    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      		= anonym_AR;
        $AccessMatrix['ShowGroup']  		= anonym_AR;
        $AccessMatrix['GetGroupIcon']     	= anonym_AR;
        $AccessMatrix['TourGroupMeetings']  = anonym_AR;
        $AccessMatrix['AdminGroupConsist']  = admin_AR;
        $AccessMatrix['SaveGroupConsist'] 	= admin_AR;
        $AccessMatrix['AdminList']			= admin_AR;
        $AccessMatrix['SaveTourGroupsList']	= admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
 
 
 
	// Выводит закладки с группами для вывода в профиле турнира
	function Index($r) {
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else
			die('Не задан id турнира');

        $GroupsList = Model_TourGroup::GetTourGroups($TourId);
        $tour = Model_Tour::GetOne($TourId);

	    $smarty = $r['smarty'];
		$smarty->assign('Tour', $tour);	
		$smarty->assign('GroupsList', $GroupsList);	
		$smarty->display('TourGroups/TourGroupsTabs.tpl');        
	}
	
	// Выводит информацию для одной группы (участники+встречи) для вывода в профиле турнира
	function ShowGroup($r) {
        // Определим id группы
		if (isset($r['Params']['GroupId']))
			$GroupId = $r['Params']['GroupId'];
		else
			die('Не задан id группы');

        $Group = Model_TourGroup::GetOne($GroupId);
        $GroupPlayers = Model_TourGroupPlayer::GetTourGroupPlayers($GroupId);

	    $smarty = $r['smarty'];
$Meetings = Model_Meeting::getMeetings(-1, $GroupId);
$smarty->assign('Meetings', $Meetings);	
// var_dump($Meetings);
// die();
		$smarty->assign('Group', $Group);	
		$smarty->assign('GroupPlayers', $GroupPlayers);	
		$smarty->display('TourGroups/TourGroup.tpl');        
	}


	// Возвращаем табличку со встречами в группе
	function TourGroupMeetings($r) {	
		// Проверим, что передан id игрока
		if (isset($r['Params']['GroupId']))
			$GroupId = $r['Params']['GroupId'];
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
    		$Meetings = Model_Meeting::getMeetings($TourId, $GroupId);
//die(json_encode($Meetings));
    	    $smarty = $r['smarty'];

    		$smarty->assign('Meetings', $Meetings);	
    		$smarty->assign('Player', new Model_Player());	// Передадим пустого игрока он нужен в профиле игрока, что бы раскрасить победы и поражения

    		$smarty->display('Profile/MeetingsList.tpl');        
        }
        catch (Exception $e) {
            header ("Ошибка при построении списка встреч $e", true, 500);
            echo "Ошибка при построении списка встреч $e";
			//http_response_code(500);
        }
	}   //  TourGroupMeetings($r)
	
	
    // Состав группы
    function AdminGroupConsist($r) {
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else
			die('Не задан id турнира');

        // Определим id группы
		if (isset($r['Params']['GroupId']))
			$GroupId = $r['Params']['GroupId'];
		else
			die('Не задан id группы');

		$Tour = Model_Tour::GetOne($TourId);
		$Group = Model_TourGroup::GetOne($GroupId);
		$CallsForTour = Model_CallForTour::GetTourCalls($TourId);
        $GroupPlayers = Model_TourGroupPlayer::GetTourGroupPlayers($GroupId);

		$smarty = $r['smarty'];
		$smarty->assign('Tour', $Tour);	
		$smarty->assign('Group', $Group);	
		$smarty->assign('CallsForTour', $CallsForTour);	
		$smarty->assign('GroupPlayers', $GroupPlayers);	
		$smarty->display('TourGroups/GroupConsist.tpl');

    }
    

    // Сохраним состав группы
    function SaveGroupConsist($r) {
        // Очистим кэш
        $r['router']->clearCache();

		// Определим Id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else {
			echo "Для сохранения заявок должен быть определён идентификатор турнира! Передайте, пожалуйста, администрации сайта скриншот этого сообщения для исправления этой ошибки.";
			return;
    	}   	    
    	
		// Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=TourProfile&t_Id=$TourId"; // Профиль турнира

        $Acts = $r['Params']['gp_Act'];

        foreach ($Acts as $gp_Id => $act) {

            if ($act!='none' && !empty($act) ) {
                $gp_model = new Model_TourGroupPlayer(null, $r['Params']['gp_Data'][$gp_Id]);
                

                if ($act=='del')
                    $gp_model->Delete();
                else {
                    $gp_model->Save();
                }
            } 
        }
//echo "<pre>".json_encode($r['QueriesLog'],JSON_PRETTY_PRINT)."<pre>";
//die();
		//Вернёмся на исходную страницу
		header("Location: $BackURL");
    }
    
	// Возвратим групп турнира для администрирования
	function AdminList($r) {	
        // Определим id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else
			die('Не задан id турнира');
	    
        $GroupsList = Model_TourGroup::GetTourGroups($TourId);
        $tour = Model_Tour::GetOne($TourId);
        
	    $smarty = $r['smarty'];
		$smarty->assign('Tour', $tour);	
		$smarty->assign('GroupsList', $GroupsList);	
		$smarty->display('TourGroups/AdminGroupsList.tpl');        
	}


	// Сохраним список групп турнира
	function SaveTourGroupsList($r) {
        // Очистим кэш
        $r['router']->clearCache();

		// Определим Id турнира
		if (isset($r['Params']['TourId']))
			$TourId = $r['Params']['TourId'];
		else {
			echo "Для сохранения групп должен быть определён идентификатор турнира! Передайте, пожалуйста, администрации сайта скриншот этого сообщения для исправления этой ошибки.";
			return;
    	}   	    
    	
		// Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=TourProfile&t_Id=$TourId"; // Профиль турнира

        $Acts = $r['Params']['g_Act'];

        foreach ($Acts as $g_Id => $act) {

            if ($act!='none') {
                $TourGroup_model = new Model_TourGroup(null, $r['Params']['g_Data'][$g_Id]);
                

                if ($act=='del')
                    $TourGroup_model->Delete();
                else {
                    $TourGroup_model->Save();
                }
            } 
        }
//echo "<pre>".json_encode($r['QueriesLog'],JSON_PRETTY_PRINT)."<pre>";
//die();
		//Вернёмся на исходную страницу
		header("Location: $BackURL");
	}


    // выведем svg картинку соответствующую группе
    function GetGroupIcon ($r) {
		// Определим Id группы
		if (isset($r['Params']['GroupId'])) {
			$GroupId = $r['Params']['GroupId'];
			if (!empty($GroupId)) {
			    $g = Model_TourGroup::GetOne($GroupId);
			    $GroupColor = $g->g_Color;
			}
		}
		if (!isset($GroupColor))
		    $GroupColor = '#ffffff';
        
        header('Content-type: image/svg+xml');
	    $smarty = $r['smarty'];
		$smarty->assign('GroupColor', $GroupColor);	
		$smarty->display('TourGroups/GroupIcon.tpl');        
    }
}
?>