<?php

Class Controller_Index Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      = anonym_AR;
        $AccessMatrix['ShowVideo']  = anonym_AR;

        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
    
	function Index($r) {
		$smarty = $r['smarty'];
		$smarty->display('Index.tpl');
	}


    // показывает видео с ютуба
    function ShowVideo($r) {
		if (isset($r['Params']['VideoIds']))
			$VideoIds = $r['Params']['VideoIds'];
		else {
			throw new Exception('Для отображения видео необходимо передать его идентификатор');
		}

	    $VideoIds = explode(';', $VideoIds);

		$smarty = $r['smarty'];
        $smarty->assign('VideoIds', $VideoIds);
		$smarty->display('Video.tpl');
		
    }

}


?>