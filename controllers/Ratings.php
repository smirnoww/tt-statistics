<?php

Class Controller_Ratings Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']              = anonym_AR;
        $AccessMatrix['Calculator']         = anonym_AR;
        $AccessMatrix['CalcTR']             = anonym_AR;
        $AccessMatrix['AdminRatingsList']   = admin_AR;
        $AccessMatrix['SaveRatingsList']    = admin_AR;
        $AccessMatrix['Calculation']        = admin_AR;
        $AccessMatrix['Calculate']          = admin_AR;

        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
    
    
	function Index($r) {
	    $smarty = $r['smarty'];

	    $Ratings = Model_Rating::GetList();

		$smarty->assign('Ratings', $Ratings);	
		$smarty->display('Ratings/Index.tpl');
	}

	function AdminRatingsList($r) {
	    $smarty = $r['smarty'];

	    $Ratings = Model_Rating::GetList();

		$smarty->assign('Ratings', $Ratings);	
		$smarty->display('Ratings/AdminRatingsList.tpl');
	}

	function SaveRatingsList($r) {
        // Очистим кэш
        $r['router']->clearCache();

		$r = Registry::getInstance();
	    
		// Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Ratings&act=AdminRatingsList"; // список рейтингов

        $Acts = $r['Params']['r_Act'];
// echo "params in head: ".json_encode($r['Params'])."<br>";

        foreach ($Acts as $r_Id => $act) {
// echo "<hr>$r_Id:$act<br>array1:";
// print_r($r['Params']['r_Data'][$r_Id]);

            if ($act!='none') {
// echo "<br>$act!='none'<br>array2:";               
                $r_model = new Model_Rating(null, $r['Params']['r_Data'][$r_Id]);
// echo "<br>param in cycle: ".json_encode($r['Params'])."<br>";
// print_r($r['Params']['r_Data'][$r_Id]);                
// echo "<br>r:".json_encode($r);                
                
                if ($act=='del') {
// echo "<br>$act=='del' - delete" ;               
                    $r_model->Delete();
                }
                else {
// echo "<br>$act!='del' - save" ;               
                    $r_model->Save();
                }
            } 
        }
// die();
		//Вернёмся на исходную страницу
		header("Location: $BackURL");
	}   //  function SaveRatingsList($r)


    // форма для выбора турниров в обсчёт рейтинга
	function Calculation($r) {
		// Проверим, что передан id рейтинга
		if (isset($r['Params']['RatingId']))
			$r_Id = $r['Params']['RatingId'];
		else {
			throw new Exception('Необходимо передать Id рейтинга для выбора турниров к обсчёту');
		}

		if (isset($r['Params']['TourCount']))
			$TourCount = $r['Params']['TourCount'];
		else
			$TourCount = 10;

	    $smarty = $r['smarty'];

	    $Dates = Model_Rating::RatingToursList($r_Id, $TourCount);
        $Rating = Model_Rating::GetOne($r_Id);

		$smarty->assign('Dates',        $Dates);	
		$smarty->assign('Rating',       $Rating);	
		$smarty->assign('TourCount',    $TourCount);	
		
		$smarty->display('Ratings/ToursListForCalc.tpl');
	}   //  Calculation($r)


    // Рассчитать рейтинг за день
	function Calculate($r) {
        // Очистим кэш
        $r['router']->clearCache();

		// Проверим, что передан id рейтинга
		if (isset($r['Params']['RatingId']))
			$r_Id = $r['Params']['RatingId'];
		else 
		    $r_Id = null;
		   
		if (isset($r['Params']['Date']))
			$date = $r['Params']['Date'];
		else 
		    $date = null;
		    
        if (!(isset($r_Id) && isset($date)))
		    throw new Exception('Для расчёта рейтинга необходимо передать Id рейтинга и дату');
		    
		    
        $R = Model_Rating::GetOne($r_Id);
        
		$CalcResult = $R->CalculateRate($date);

        $smarty = $r['smarty'];
    
        // Скомпонуем лог обсчёта 
        $smarty->assign('CalcLog', $CalcResult);
		$TextCalcLog = $smarty->fetch('Ratings/CalcLog.tpl');							

        // вставим лог обсчёта в сообщение
		$smarty->append('Message',Array('Type'=>'INFO',
										'Title'=>'Результат обсчёта рейтинга',
										'Body'=>$TextCalcLog
										// ,'TechInfo'=>$TechInfo
									    )
					    );	
					    

		$this->Calculation($r);
		
		return $TextCalcLog;
	}   //  Calculate($r)
	
 
    // Показывает пустую строку калькулятора рейтинга
	function CalcTR($r) {
	    
		// Проверим, что передан id игрока
		if (isset($r['Params']['MeetingNumber']))
			$MeetingNumber = $r['Params']['MeetingNumber'];
		else 
		    throw new Exception('Ошибка: Необходим MeetingNumber');

        $smarty = $r['smarty'];
        $Meeting = Model_Meeting::GetOne(null);
        $Meeting->N = $MeetingNumber;
        $Meeting->IWon = 1;
        $Meeting->OpponentRate = 300;
        
		$smarty->assign('Meeting', $Meeting);	
        
		$smarty->display('Ratings/CalculatorTR.tpl');
	}   //  CalcTR($r)
 
    // Показывает калькулятор рейтинга
	function Calculator($r) {
	    
		// Проверим, что передан id игрока
		if (isset($r['Params']['PlayerId']))
			$p_Id = $r['Params']['PlayerId'];
		else {
			$p_Id = -1;
		}

		// Проверим, что передан id Турнира
		if (isset($r['Params']['TourId']))
			$t_Id = $r['Params']['TourId'];
		else {
			$t_Id = -1;
		}

        $smarty = $r['smarty'];

        // Если задан игрок и турнир, то заполним кальклятор встречами
        if ($p_Id>0 && $t_Id>0) {

            $Tour = Model_Tour::GetOne($t_Id);
            $TourDate = $Tour->t_DateTime;
            $TourDate->setTime(0, 0);
			
            $Rate = Model_PlayerRateHistory::GetPlayerRateBefore($p_Id, $TourDate);
            $Meetings = Model_Meeting::getMeetings($t_Id, -1, $p_Id);
            
            // Укажем в каждой встрече выиграл ли игрок, 
            // для которого формируется калькулятор, рейтинг соперника
			$mCounter=1;
            foreach ($Meetings as $key => $m) {
				if (!$m->m_AffectRating)	{
					unset($Meetings[$key]);
					continue;
				}
					
                $m->IWon = ($m->m_WinnerPlayerId == $p_Id);
                $Opponent = $m->m_WinnerPlayerId == $p_Id 
                                    ? $m->m_LoserPlayerId('Model_Player')
                                    : $m->m_WinnerPlayerId('Model_Player');
                
                $m->OpponentRate = Model_PlayerRateHistory::GetPlayerRateBefore($Opponent->p_Id, $TourDate);
                $m->OpponentName = $Opponent->p_Name;
				$m->N = $mCounter++;
            }
			
    		$smarty->assign('Tour', $Tour);	
    		$smarty->assign('Rate', $Rate);	
    		$smarty->assign('Meetings', $Meetings);	
        }
        
		$smarty->display('Ratings/Calculator.tpl');
	}   //  function Calculator($r)


    // Расчитывает опорный рейтинг игроку
	function CalcBaseRating($r) {
	    
	    try {
    		// Проверим, что передан id игрока
    		if (isset($r['Params']['PlayerId']))
    			$p_Id = $r['Params']['PlayerId'];
    		else {
    			$p_Id = -1;
    		}
    
    		// Проверим, что передан id Турнира
    		if (isset($r['Params']['TourId']))
    			$t_Id = $r['Params']['TourId'];
    		else {
    			$t_Id = -1;
    		}
    
            
            // Если не задан игрок или турнир, то вернём сообщение об ошибке
            if ($p_Id<0 || $t_Id<0)
                throw new Exception('Для расчёта опорного рейтинга необходимо передать идентификатор игрока (PlayerId) и идентификатор турнира (TourId)');
                
            
            $Meetings = Model_Meeting::getMeetings($t_Id, -1, $p_Id);
            $TourModel = Model_Tour::getOne($t_Id);
            
            $mCount=0;
            $maxWinRating=-1000000;
            $minLoseRating=1000000;
            foreach ($Meetings as $m) {
                if (!$m['m_AffectRating'])
                    continue;
                
                if ($m['m_WinnerPlayerId']==$p_Id) {
                    $opRating = Model_PlayerRateHistory::GetPlayerRate($p_Id, $TourModel->t_DateTime);
                    $maxWinRating = max($maxWinRating, $opRating);
                }

                if ($m['m_LoserPlayerId']==$p_Id) {
                    $opRating = Model_PlayerRateHistory::GetPlayerRate($p_Id, $TourModel->t_DateTime);
                    $maxWinRating = min($minLoseRating, $opRating);
                }
            }
            $Tour = Model_Tour::GetOne($t_Id);
            $TourDate = $Tour->t_DateTime;
            $TourDate->setTime(0, 0);
			
            $Rate = Model_PlayerRateHistory::GetPlayerRateBefore($p_Id, $TourDate);
            
            // Укажем в каждой встрече выиграл ли игрок, 
            // для которого формируется калькулятор, рейтинг соперника
			$mCounter=1;
            foreach ($Meetings as $key => $m) {
				if (!$m->m_AffectRating)	{
					unset($Meetings[$key]);
					continue;
				}
					
                $m->IWon = ($m->m_WinnerPlayerId == $p_Id);
                $Opponent = $m->m_WinnerPlayerId == $p_Id 
                                    ? $m->m_LoserPlayerId('Model_Player')
                                    : $m->m_WinnerPlayerId('Model_Player');
                
                $m->OpponentRate = Model_PlayerRateHistory::GetPlayerRateBefore($Opponent->p_Id, $TourDate);
                $m->OpponentName = $Opponent->p_Name;
				$m->N = $mCounter++;
            }
			
    		$smarty->assign('Tour', $Tour);	
    		$smarty->assign('Rate', $Rate);	
    		$smarty->assign('Meetings', $Meetings);	
            
	    }
        catch (Exception $e) {
            echo json_encode( array("error"=>$e->getMessage()) );
        }
            
        
	}   //  function CalcBaseRating($r)
}


?>