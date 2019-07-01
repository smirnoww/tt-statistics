<?php
Class Controller_Courts Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      = anonym_AR;
        $AccessMatrix['ViewCourt']  = anonym_AR;
        $AccessMatrix['AdminList']  = admin_AR;
        $AccessMatrix['NewCourt']   = admin_AR;
        $AccessMatrix['EditCourt']  = admin_AR;
        $AccessMatrix['Save']       = admin_AR;
        $AccessMatrix['Delete']     = admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
    
    
    // Список игродромов для пользователя
	function Index($r) {
        $CourtsList = Model_Court::GetList('true', array('c_Name','c_Address','c_URL','c_Contacts','c_Description'), '**');

	    $smarty = $r['smarty'];
		$smarty->assign('Courts', $CourtsList);	
		$smarty->display('Courts/Index.tpl');        
	}

	// Возвратим список игродромов для администрирования
	function AdminList($r) {	
        $CourtsList = Model_Court::GetList('true', array('c_Name','c_Address','c_URL','c_Contacts','c_Description', '**'));

	    $smarty = $r['smarty'];
		$smarty->assign('Courts', $CourtsList);	
		$smarty->display('Courts/CourtsAdminList.tpl');        
	}

	// Покажем сведения об игродроме
	function ViewCourt($r) {
        // Определим id
		if (isset($r['Params']['c_Id']))
			$c_Id = $r['Params']['c_Id'];
		else
			die('Не задан id площадки');

        $Court = Model_Court::GetOne($c_Id, '**');
        
	    $smarty = $r['smarty'];
		$smarty->assign('Court', $Court);	
		$smarty->display('Courts/ViewCourt.tpl');        
	}

	// Покажем форму для создания нового игродрома
	function NewCourt($r) {
	    $smarty = $r['smarty'];
		$smarty->display('Courts/NewCourt.tpl');        
	}

	// Покажем форму для редактирования игродрома
	function EditCourt($r) {
        // Определим id
		if (isset($r['Params']['CourtId']))
			$c_Id = $r['Params']['CourtId'];
		else
			die('Не задан id площадки');

        $Court = Model_Court::GetOne($c_Id);
        
	    $smarty = $r['smarty'];
		$smarty->assign('Court', $Court);	
		$smarty->display('Courts/EditCourt.tpl');        
	}

    // сохраним игродром
    function Save($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Courts&act=AdminList"; // список площадок

        $c = new Model_Court(null,$r['Params']['courtData']);
        $c->Save();

		//Вернёмся на исходную страницу
		header("Location: $BackURL");

    }
    
    // Удалим игродром
    function Delete($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Courts&act=AdminList"; // список площадок

        // Определим id
		if (isset($r['Params']['CourtId']))
			$c_Id = $r['Params']['CourtId'];
		else
			die('Не задан id площадки');

        $c = Model_Court::GetOne($c_Id);
        try {
            if ($c)
                $c->Delete();
        }
        catch (Exception $e) {
    		header('HTTP/1.0 500 Internal Server Error', true, 500);
    		echo $e->getMessage();
    		exit;
        }
        
		//Вернёмся на исходную страницу
		header("Location: $BackURL");

    }
  
}
?>