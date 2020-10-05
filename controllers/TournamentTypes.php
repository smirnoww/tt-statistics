<?php

Class Controller_TournamentTypes Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
        $AccessMatrix['Index']      		= anonym_AR;
        $AccessMatrix['AdminTourTypesList'] = admin_AR;
        $AccessMatrix['SaveTourTypesList']  = admin_AR;
        
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    } // Проверяем права пользователя
 
	function Index($r) {
	    $smarty = $r['smarty'];

	    $TourTypes = Model_TournamentType::GetList();

		$smarty->assign('TourTypes', $TourTypes);	
		$smarty->display('TourTypes/Index.tpl');
	}

	function AdminTourTypesList($r) {
	    $smarty = $r['smarty'];

	    $TourTypes = Model_TournamentType::GetList();

		$smarty->assign('TourTypes', $TourTypes);	
		$smarty->display('TourTypes/AdminTourTypesList.tpl');
	}

	function SaveTourTypesList($r) {
        // Очистим кэш
        $r['router']->clearCache();

		// Определим ссылку для возврата
		if (isset($r['Params']['BackURL']))
			$BackURL = $r['Params']['BackURL'];
		else
			$BackURL = "?ctrl=TournamentTypes&act=AdminTourTypesList"; // список типов турниров

        $Acts = $r['Params']['ttype_Act'];
// echo "params in head: ".json_encode($r['Params'])."<br>";

        foreach ($Acts as $ttype_Id => $act) {
 //echo "<hr>$ttype_Id:$act<br>array1:";
 //print_r($r['Params']['ttype_Data'][$ttype_Id]);

            if ($act!='none') {
// echo "<br>$act!='none'<br>array2:";               
                $ttype_model = new Model_TournamentType(null, $r['Params']['ttype_Data'][$ttype_Id]);
// echo "<br>param in cycle: ".json_encode($r['Params'])."<br>";
// print_r($r['Params']['ttype_Data'][$ttype_Id]);                
// echo "<br>r:".json_encode($r);                
                
                if ($act=='del') {
 //echo "<br>$act=='del' - delete" ;               
                    $ttype_model->Delete();
                }
                else {
// echo "<br>$act!='del' - save" ;               
                    $ttype_model->Save();
                }
            } 
        }
// die();
		//Вернёмся на исходную страницу
		header("Location: $BackURL");
	}

}


?>