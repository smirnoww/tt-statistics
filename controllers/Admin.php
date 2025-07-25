<?php
Class Controller_Admin Extends Controller_Base {

    // Проверяем права пользователя
    static function CheckPermissions($action, $r) {
        $GlobalRights = parent::CheckPermissions($action, $r);
        
        if ($GlobalRights!==true)
            return $GlobalRights;
            
        // если прошла глобальная проверка прав, проверим права пользователя здесь.
        $Auth = $r['Auth'];
 
 
        $AccessMatrix['Index']      = admin_AR;
        $AccessMatrix['phpinfo']    = admin_AR;
        $AccessMatrix['ArchiveLog'] = anonym_AR;
        $AccessMatrix['ForceClearCache'] = anonym_AR;
        return $Auth->CR($AccessMatrix[$action]) == 0 ? "У вас недостаточно прав для выполнения этого действия. Выполните вход в систему или обратитесь к администратору сайта." : true;
        
    }
    
    
    // заглавная страничка для администрирования
	function Index($r) {
	    $smarty = $r['smarty'];
		$smarty->display('AdminLayout.tpl');        
	}

	function ForceClearCache($r) {
        // Очистим кэш
        $r['router']->clearCache();
	}
	
	
    // Архивирование журнала операций
	function ArchiveLog($r) {
	    
        // Определим сколько дней оставить в журнале и столько же заархивируем
		if (isset($r['Params']['LogLengthDays']))
			$LogLengthDays = $r['Params']['LogLengthDays'];
		else
			die('Не задано кол-во дней');
			
		$q = "  START TRANSACTION;
		
		        SET @ActiveLog = NOW() - INTERVAL :days DAY;
		        
		        insert into x_LogArchive
                SELECT 
                		NOW() ArchiveDateTime,
                        `l_Controller`,
                        `l_Action`,
                        `l_Username`,
                        `l_PlayerName`,
                        min(`l_DateTime`) min_DateTime, 
                        max(`l_DateTime`) max_DateTime,
                        sum(`l_Duration`) sum_Duration, 
                        avg(`l_Duration`) avg_Duration, 
                        count(1)		  requestCount
                FROM 
                		`x_Log` 
                WHERE
                		l_DateTime < @ActiveLog
                GROUP BY
                		`l_Controller`,
                        `l_Action`,
                        `l_Username`,
                        `l_PlayerName`
                ORDER BY
                		 avg(`l_Duration`) DESC;
                
                
                DELETE FROM x_Log
                WHERE
                		l_DateTime < @ActiveLog;
                		
                COMMIT;";
                		
		Model_Base::ExecuteSQLWithParams($q, array(':days'=> LogLengthDays));
		echo "Ok";
		return 'Ok';
	}   // ArchiveLog($r)

    
 
	function phpinfo() {
	    phpinfo();
	}
}
?>