<?php
Class Controller_Statistics Extends Controller_Base {

	function Index($r) {
	    echo "not defined<br>\nЗдесь будет главная страница статистики";
	}

    //модуль с призёрами
    function PrizeWinners($r) {
		try {
			$winnersList = Model_Tour::GetPrizewinners();

			$smarty = $r['smarty'];

			$smarty->assign('PrizeWinners', $winnersList);	
			
			$smarty->display('Statistics/PrizeWinners.tpl');
		}
		catch(Exception $e) {
            echo $e->getMessage();	
		}
    }

	
    //модуль с рейтингом
    function RatingModule($r) {
		try {
		
    		if (isset($r['Params']['ChangesOnly'])) 
	    	    $ChangesOnly = 1;
            else
                $ChangesOnly = 0;
                
			$Players = Model_Base::ExecArraySQLWithParams(
                                        "CALL x_RatingModule(:ChangesOnly)", 
			                            array('ChangesOnly' => $ChangesOnly)
			                        );

			$smarty = $r['smarty'];

			$smarty->assign('Players', $Players);	
            if ($ChangesOnly)
    			$smarty->assign('RatingModuleHeader', 'изменение рейтинга');	

			$smarty->display('Statistics/RatingModule.tpl');
		}
		catch(Exception $e) {
            echo $e->getMessage();	
		}
    }   //   RatingModule($r)

	
    //Игроки по организатору
    function PlayersByTourOrg($r) {
		try {
		
    		if (isset($r['Params']['TourOrgId'])) 
				$TourOrgId = $r['Params']['TourOrgId'];
			else
				die('Не задан id организатора турниров');
                
            $TourOrg = Model_TourOrganizer::GetOne($TourOrgId);
			$Players = Model_Base::ExecArraySQLWithParams(
                                        "CALL x_PlayersByTourOrg(:TourOrgId)", 
			                            array('TourOrgId' => $TourOrgId)
			                        );

			$smarty = $r['smarty'];

			$smarty->assign('TourOrg', $TourOrg);	
			$smarty->assign('Players', $Players);	

			$smarty->display('Statistics/PlayersStat.tpl');
		}
		catch(Exception $e) {
            echo $e->getMessage();	
		}
    }   //  PlayersByTourOrg($r)


	function HeadToHeadStat($r) {
		$smarty = $r['smarty'];
		
		if (isset($r['Params']['FirstPlayerId']) && isset($r['Params']['SecondPlayerId'])) {
		    $firstPlayerId = $r['Params']['FirstPlayerId'];
		    $secondPlayerId = $r['Params']['SecondPlayerId'];
		}
		else
		    throw new Exception("Отсутствуют параметр FirstPlayerId или SecondPlayerId");
		
		try {
            $meetings = Model_Meeting::getMeetings(-1,-1, $firstPlayerId, $secondPlayerId);
            $FirstPlayer = new Model_Player($firstPlayerId);
            $SecondPlayer = new Model_Player($secondPlayerId);

            // Посчитаем кто-сколько раз выиграл
            $FirstWin = 0;
            $SecondWin = 0;
            
            foreach ($meetings as $meeting) {
                if ( ($meeting['m_WinnerPlayerId'] == $firstPlayerId) || ($meeting['m_Winner2PlayerId'] == $firstPlayerId) )
                     $FirstWin++;
                if ( ($meeting['m_WinnerPlayerId'] == $secondPlayerId) || ($meeting['m_Winner2PlayerId'] == $secondPlayerId) )
                     $SecondWin++;
            }
            
            $FirstPlayer->NumberOfWins = $FirstWin;
            $SecondPlayer->NumberOfWins = $SecondWin;
            
			$smarty->assign('FirstPlayer', $FirstPlayer);	
			$smarty->assign('SecondPlayer', $SecondPlayer);	
			$smarty->assign('Meetings', $meetings);	

			$smarty->display('Statistics/HeadToHeadStat.tpl');
		}
		catch(Exception $e) {
            echo $e->getMessage();	
		}
	
	}

	
    function HeadToHeadStatForm($r) {
		$smarty = $r['smarty'];

		// Если первый игрок заранее определён
		if (isset($r['Params']['FixedPlayerId'])) {			
			$FixedPlayerId = $r['Params']['FixedPlayerId'];
			$FixedPlayerModel = new Model_Player($FixedPlayerId);

			$smarty->assign('FixedPlayer', $FixedPlayerModel);	
		}
		else {
			$PlayersList = Model_Player::GetPlayersListWithMeeting();

			$smarty->assign('PlayersList', $PlayersList);	
		}
		
		$smarty->display('Statistics/HeadToHeadStatForm.tpl');
        
    }
    
}
?>
