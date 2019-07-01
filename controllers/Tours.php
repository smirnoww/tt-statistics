<?php
Class Controller_Tours Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']          = anonym_AR;
        $AccessMatrix['GetTours']      	= anonym_AR;
        $AccessMatrix['AdminList']      = admin_AR;
        $AccessMatrix['GetAdminTours']  = admin_AR;
        $AccessMatrix['NewTour']  		= admin_AR;
        $AccessMatrix['EditTour']		= admin_AR;
        $AccessMatrix['Save']			= admin_AR;
        $AccessMatrix['Delete']			= admin_AR;

        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    }


    // Список годов турниров для всех
	function Index($r) {
	    $YearsArray = Model_Tour::GetYears();
        arsort($YearsArray);
        
	    $smarty = $r['smarty'];
		$smarty->assign('Years', $YearsArray);	
		$smarty->display('Tours/Index.tpl');        
	}
	
    // Список турниров для всех по году
	function GetTours($r) {
        // Определим год
		if (isset($r['Params']['year']))
			$year = (int) $r['Params']['year'];
		else
			$year=null;

        $tours = Model_Tour::GetList(
                                    "(YEAR(t_DateTime) = :year1) or :year2 is null Order by t_DateTime desc", 
                                    array('t_Name','t_CourtId','t_URL','t_DateTime'), // fields for load
                                    array(
                                            ':year1' => $year,
                                            ':year2' => $year
                                    )
                );

	    $smarty = $r['smarty'];
		$smarty->assign('Tours', $tours);	
		$smarty->display('Tours/ToursList.tpl');
	}
	
	function AdminList($r) {
	    $YearsArray = Model_Tour::GetYears();
        arsort($YearsArray);
        
	    $smarty = $r['smarty'];
		$smarty->assign('Years', $YearsArray);	
		$smarty->display('Tours/AdminTourYears.tpl');        
	}

    // Список турниров для всех по году
	function GetAdminTours($r) {
        // Определим год
		if (isset($r['Params']['year']))
			$year = (int) $r['Params']['year'];
		else
			$year=null;

        $tours = Model_Tour::GetList(
                                    "(YEAR(t_DateTime) = :year1) or :year2 is null Order by t_DateTime desc", 
                                    array('t_Name','t_CourtId','t_URL','t_DateTime'), // fields for load
                                    array(
                                            ':year1' => $year,
                                            ':year2' => $year
                                    )
                );

	    $smarty = $r['smarty'];
		$smarty->assign('Tours', $tours);	
		$smarty->display('Tours/AdminToursList.tpl');
	}


    // Создание турнира
	function NewTour($r) {
	    $smarty = $r['smarty'];

        $tt = Model_TournamentType::GetList();
		$smarty->assign('TourTypes', $tt);	

        $courts = Model_Court::GetList();
		$smarty->assign('Courts', $courts);	

        $TourOrganizers = Model_TourOrganizer::GetList();
		$smarty->assign('TourOrganizers', $TourOrganizers);	

        $Ratings = Model_Rating::GetList();
		$smarty->assign('Ratings', $Ratings);	

        $fu = Model_ForumUser::GetList('group_id in (13)', array('username'));
		$smarty->assign('ForumUsers', $fu);	


		$smarty->display('Tours/TourTitleForm.tpl');
	}
	
	
	// Редактирование общей информации по турниру
	function EditTour($r) {
        // Определим id
		if (isset($r['Params']['TourId']))
			$t_Id = (int) $r['Params']['TourId'];
		else
			throw new Exception ('TourId need' );
			
			
	    $smarty = $r['smarty'];

        $tt = Model_TournamentType::GetList();
		$smarty->assign('TourTypes', $tt);	

        $courts = Model_Court::GetList();
		$smarty->assign('Courts', $courts);	

        $TourOrganizers = Model_TourOrganizer::GetList();
		$smarty->assign('TourOrganizers', $TourOrganizers);	

//        $fu = Model_Player::GetPlayersWithRoles(tourorg_AR);

        $fu = Model_ForumUser::GetList('group_id in (13)', array('username'));
		$smarty->assign('ForumUsers', $fu);
		//	todo : добавить того форумчанина, который организовал именно этот турнир, даже если он уже не организатор

		$t = Model_Tour::GetOne($t_Id,"*");
		$smarty->assign('Tour', $t);

        $Ratings = Model_Rating::GetList();
		$smarty->assign('Ratings', $Ratings);	

		$smarty->display('Tours/TourTitleForm.tpl');
	}
	
	
    // сохраним турнир
    function Save($r) {
        // Очистим кэш
        $r['router']->clearCache();

        $tourData = $r['Params']['tourData'];
        
        $tourData['t_DateTime'] ="{$tourData['t_Date']} {$tourData['t_Time']}";

        $t = new Model_Tour(null, $tourData);

        $t->Save();
        if (isset($r['Params']['Influence']))
            $InflRatings = array_keys($r['Params']['Influence']);
        else
            $InflRatings = array();

        $t->SaveTourRatingsList($InflRatings);
        
        // Определим ссылку для возврата
		if (!empty($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=TourProfile&t_Id=".$t->t_Id; // профиль созданного турнира

		//Вернёмся на исходную страницу
		header("Location: $BackURL");
    }
    
    // Удалим турнир
    function Delete($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим id
		if (isset($r['Params']['TourId']))
			$t_Id = (int) $r['Params']['TourId'];
		else
			throw new Exception ('TourId need' );

        $t = new Model_Tour($t_Id);

        $t->Delete();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Tours&act=AdminList"; // список турниров для администрирования

		//Вернёмся на исходную страницу
		header("Location: $BackURL");

    }
}
?>