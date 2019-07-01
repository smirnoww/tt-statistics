<?php
Class Controller_Penalties Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      = anonym_AR;
        $AccessMatrix['NewPenalty']   = admin_AR;
        $AccessMatrix['EditPenalty']  = admin_AR;
        $AccessMatrix['Save']       = admin_AR;
        $AccessMatrix['Delete']     = admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
    
    
    // Список взысканий
	function Index($r) {
        $PenaltiesList = Model_Penalty::GetPenaltiesList();

	    $smarty = $r['smarty'];
		$smarty->assign('PenaltiesList', $PenaltiesList);	
		$smarty->display('Penalties/Index.tpl');        
	}

	// Покажем форму для создания нового взыскания
	function NewPenalty($r) {
	    $PenaltyTypes = Model_PenaltyType::GetList();

	    $smarty = $r['smarty'];
		$smarty->assign('PenaltyTypes', $PenaltyTypes);	
		$smarty->display('Penalties/PenaltyForm.tpl');        
	}

	// Покажем форму для редактирования взыскания
	function EditPenalty($r) {
        // Определим id
		if (isset($r['Params']['PenaltyId']))
			$pnlt_Id = $r['Params']['PenaltyId'];
		else
			die('Не задан id взыскания');

        $Penalty = Model_Penalty::GetOne($pnlt_Id, '*');
	    $PenaltyTypes = Model_PenaltyType::GetList();
        
	    $smarty = $r['smarty'];
		$smarty->assign('PenaltyTypes', $PenaltyTypes);	
		$smarty->assign('Penalty', $Penalty);	
		$smarty->display('Penalties/PenaltyForm.tpl');        
	}

    // сохраним взыскание
    function Save($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Penalties"; // список взысканий

        $pnlt = new Model_Penalty(null,$r['Params']['pnltData']);
        
        // Запомним создавшего/изменившего пользователя
        $Auth = $r['Auth'];

        if (isset($pnlt->pnlt_Id))
        {
            $pnlt->pnlt_LastUpdatePlayerId = $Auth->AuthPlayerId;
        }
        else
            $pnlt->pnlt_CreatePlayerId = $Auth->AuthPlayerId;

        
        $pnlt->Save();

		//Вернёмся на исходную страницу
		header("Location: $BackURL");

    }
    
    // Удалим взыскание
    function Delete($r) {
        // Очистим кэш
        $r['router']->clearCache();

        // Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=Penalties"; // список взысканий

        // Определим id
		if (isset($r['Params']['PenaltyId']))
			$pnlt_Id = $r['Params']['PenaltyId'];
		else
			die('Не задан id площадки');

        $pnlt = Model_Penalty::GetOne($pnlt_Id);
        
        $pnlt->Delete();

		//Вернёмся на исходную страницу
		header("Location: $BackURL");

    }
  
}
?>