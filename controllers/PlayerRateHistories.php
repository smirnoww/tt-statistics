<?php

Class Controller_PlayerRateHistories Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']              = anonym_AR;
        $AccessMatrix['SaveRate']           = admin_AR;
        $AccessMatrix['DeleteRate']         = admin_AR;
        $AccessMatrix['AdminRateHistory']   = admin_AR;
        $AccessMatrix['RateTR']             = admin_AR;
        
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
    
    
	function Index($r) {
        echo 'not implemented';
	}


    // История рейтинга по игроку для редактирования
	function AdminRateHistory($r) {
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else 
			throw new Exception("Для получения истории рейтинга игрока должен быть передан идентификатор игрока и идентификатор рейтинга!");

		// Проверим, что передан id рейтинга
		if (isset($r['Params']['RatingId']))
			$r_Id = $r['Params']['RatingId'];
		else 
			throw new Exception("Для получения истории рейтинга игрока должен быть передан идентификатор игрока и идентификатор рейтинга!");

	    $smarty = $r['smarty'];

	    $rh = Model_PlayerRateHistory::GetList(
	            "pr_PlayerId = :PlayerId and pr_RatingId = :RatingId Order By pr_Date",
	            "*",
	            array(':PlayerId'=>$p_Id, ':RatingId'=>$r_Id));

        $Player_model = Model_Player::GetOne($p_Id);
		$smarty->assign( 'Player',    $Player_model );
		$smarty->assign( 'RatingId',    $r_Id );
		$smarty->assign( 'RateHistory', $rh );
		
		$smarty->display('Ratings/AdminRateHistory.tpl');
	}   //  AdminRateHistory($r)


    // Возвращает <TR> для редактирования одного значения рейтинга
    // для теста: http://tt-saratov.ru/statistics/mvc_dev/?ctrl=PlayerRateHistories&act=RateTR&pr_Id=777
	function RateTR($r) {
		try {
    		// Проверим, что передан id рейтинга игрока
    		if (isset($r['Params']['PlayerRateId']))
    			$pr_Id = $r['Params']['PlayerRateId'];
    		else 
    			throw new Exception("Необходим идентификатор значения рейтинга!");
            
			$this->ShowRateTR($pr_Id);
		}
		catch (Exception $e) {
		    echo "<tr><td colspan=5>{$e->getMessage()}";
		    echo json_encode($r['Params']);
		    echo "</td></tr>";
		}
	}


    // Сохраняет запись об одном значении рейтинга
	function SaveRate($r) {
        // Очистим кэш
        $r['router']->clearCache();

		try {
    	    //echo "youhooo:".json_encode($r['Params']);
    	    $act = $r['Params']['pr_Act'];
            
            if ($act=='new' || $act=='edit') {
                $pr_model = Model_PlayerRateHistory::GetOne(null, $r['Params']['pr_Data']);
				if (isset($r['Params']['RatingId']))
					$pr_model->pr_RatingId = $r['Params']['RatingId'];
				if (isset($r['Params']['PlayerId']))
					$pr_model->pr_PlayerId = $r['Params']['PlayerId'];
                $pr_model->Save();
            }
            
            $this->ShowRateTR($pr_model->pr_Id);

		}
		catch (Exception $e) {
		    header('HTTP/1.0 400 Bad Request'); 
		    echo "Ошибка: ".$e->getMessage();
		}
	}

    // Удаляет запись об одном значении рейтинга
	function DeleteRate($r) {
        // Очистим кэш
        $r['router']->clearCache();

		try {
    		// Проверим, что передан id игрока
    		if (isset($r['Params']['PlayerRateId']))
    			$pr_Id = $r['Params']['PlayerRateId'];
    		else 
    			throw new Exception("Необходим идентификатор значения рейтинга!");
			
			$pr = Model_PlayerRateHistory::GetOne($pr_Id);
			if ($pr->Delete()===true)
				echo "Ok";
		}
		catch (Exception $e) {
		    echo "Fail: ".$e->getMessage();
		}
	}



	// Выводит строку <TR> для редактирования одного значения рейтинга
	private function ShowRateTR($pr_Id) {
		try {
		    $r = Registry::getInstance();
    	    $smarty = $r['smarty'];
            if ($pr_Id>0)
    	        $Rate = Model_PlayerRateHistory::GetOne($pr_Id);
    	    else {
    	        $Rate = Model_PlayerRateHistory::GetOne(null, array('pr_Id' => $pr_Id));
    	        $Rate->pr_Act = 'new';
    	    }
    	        
    		$smarty->assign( 'Rate', $Rate );
    		$smarty->display('Ratings/AdminRateTR.tpl');
		}
		catch (Exception $e) {
		    echo "<tr><td colspan=5>{$e->getMessage()}";
		    echo json_encode($r['Params']);
		    echo "</td></tr>";
		}
	}
}


?>