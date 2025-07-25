<?php

Class Controller_ClearCache Extends Controller_Base {

	//clear cache in case player joined/left a tournament via telegram bot
	function Clear() {
        // Очистим кэш
        $r['router']->clearCache();
	}
		
}
?>