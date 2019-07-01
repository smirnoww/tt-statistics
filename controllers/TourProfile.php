<?php
Class Controller_TourProfile Extends Controller_Base {

    // Профиль турнира
	function Index($r) {
        // Определим id
		if (isset($r['Params']['t_Id']))
			$t_Id = (int) $r['Params']['t_Id'];
		elseif (isset($r['Params']['TourId']))
			$t_Id = (int) $r['Params']['TourId'];
		else
			throw new Exception ('id need');
			
			
	    $t = Model_Tour::GetOne($t_Id,'*');
        
	    $smarty = $r['smarty'];
		$smarty->assign('Tour', $t);	
		$smarty->display('TourProfile/TourProfile.tpl');        
	}

}
?>