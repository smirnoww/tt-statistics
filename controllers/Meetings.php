<?php
Class Controller_Meetings Extends Controller_Base {


    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      		= anonym_AR;
        $AccessMatrix['AdminMeetingsList']  = admin_AR;
        $AccessMatrix['MeetingTR']  		= admin_AR;
        $AccessMatrix['SaveMeeting'] 		= admin_AR;
        $AccessMatrix['ShowMeetingTR']		= admin_AR;
        $AccessMatrix['DeleteMeeting']		= admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
 

	function Index($r) {
		echo "Meetings.Index";
	}
	
	
	// Выводит список встреч для редактирования
	function AdminMeetingsList($r) {
        $smarty = $r['smarty'];

        // Определим id группы
		if (isset($r['Params']['GroupId']))
			$GroupId = $r['Params']['GroupId'];
		else
			die('Не задан id группы турнира');
			
        $Group = Model_TourGroup::GetOne($GroupId);
        $Tour = Model_Tour::GetOne($Group->g_TourId);
        $Meetings = Model_Meeting::getMeetings($Group->g_TourId, $GroupId);

		$smarty->assign('Tour', $Tour);	
		$smarty->assign('Group', $Group);	
		$smarty->assign('Meetings', $Meetings);	
		$smarty->display('Meetings/AdminMeetingList.tpl');        
	}

	
    // Возвращает <TR> для редактирования одной встречи
    // для теста: http://tt-saratov.ru/statistics/mvc_dev/?ctrl=Meetings&act=MeetingTR&MeetingId=40577
	function MeetingTR($r) {
		try {
    		// Проверим, что передан id встречи
    		if (isset($r['Params']['MeetingId']))
    			$m_Id = $r['Params']['MeetingId'];
    		else 
    			throw new Exception("Необходим идентификатор встречи!");

            if ($m_Id>0) {
    	        $M = Model_Meeting::GetOne($m_Id);
			}
    	    else { // Если шаблон для новой встречи
                $m_Data = array('m_Id' => $m_Id, 'm_AffectRating' => 1);

        	    // Если есть предустановленные данные, то заполним шаблон ими
        	    if (isset($r['Params']['m_Data']))
        	        $m_Data = array_merge($m_Data, $r['Params']['m_Data']);

    	        $M = Model_Meeting::GetOne(null, $m_Data);
    	        $M->m_Act = 'new';
    	    }
    	    
            
			$this->ShowMeetingTR($M);
		}
		catch (Exception $e) {
		    echo "<tr><td colspan=5>{$e->getMessage()}";
		    echo json_encode($r['Params']);
		    echo "</td></tr>";
		}
	}
	
	
	function SaveMeeting($r) {
        // Очистим кэш
        $r['router']->clearCache();

		try {
    	    //echo "youhooo:".json_encode($r['Params']);
    	    $act = $r['Params']['m_Act'];
            
            if ($act=='new' || $act=='edit') {
                $m_model = Model_Meeting::GetOne(null, $r['Params']['m_Data']);

				if (isset($r['Params']['TourId']))
					$m_model->m_TourId = $r['Params']['TourId'];
				if (isset($r['Params']['GroupId']))
					$m_model->m_GroupId = $r['Params']['GroupId'];
				
                $m_model->Save();
            }
            
            $this->ShowMeetingTR($m_model);

		}
		catch (Exception $e) {
		    header('HTTP/1.0 400 Bad Request'); 
		    echo "Ошибка: ".$e->getMessage();
		}		

 // echo "<pre>".json_encode($r['QueriesLog'],JSON_PRETTY_PRINT)."<pre>";
 // die();
	} //SaveMeeting()
	

	// Выводит строку <TR> для редактирования одной встречи
	private function ShowMeetingTR($m_model) {
		try {
			$m_model->WinnerName    = $m_model->m_WinnerPlayerId('Model_Player')->p_Name;
			$m_model->Winner2Name   = $m_model->m_Winner2PlayerId('Model_Player')->p_Name;
			$m_model->LoserName     = $m_model->m_LoserPlayerId('Model_Player')->p_Name;
			$m_model->Loser2Name    = $m_model->m_Loser2PlayerId('Model_Player')->p_Name;
    	        
		    $r = Registry::getInstance();
    	    $smarty = $r['smarty'];

    		$smarty->assign( 'Meeting', $m_model );
    		$smarty->display('Meetings/AdminMeetingTR.tpl');
		}
		catch (Exception $e) {
		    echo "<tr><td colspan=12>{$e->getMessage()}";
		    echo json_encode($r['Params']);
		    echo "</td></tr>";
		}
	}


    // Удаляет запись об одной встрече
	function DeleteMeeting($r) {
        // Очистим кэш
        $r['router']->clearCache();

		try {
    		// Проверим, что передан id игрока
    		if (isset($r['Params']['MeetingId']))
    			$m_Id = $r['Params']['MeetingId'];
    		else 
    			throw new Exception("Необходим идентификатор встречи!");
			
			$m = Model_Meeting::GetOne($m_Id);
			if ($m->Delete()===true)
				echo "Ok";
		}
		catch (Exception $e) {
		    echo "Fail: ".$e->getMessage();
		}
	}

	
}
?>