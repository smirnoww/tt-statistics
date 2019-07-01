<?php

Class Router {

	private $path;

	function setPath($path) {

        $path = rtrim($path, '/\\');
        $path = $path . DIRSEP;
        if (is_dir($path) == false) {
                throw new Exception ('Invalid controller path: `' . $path . '`');
        }

        $this->path = $path;
	}


	private function getController($r, &$file, &$controller, &$action) {

		$controller = $r['Params']['ctrl'];
		$controller = trim($controller, '/\\');
		$action = $r['Params']['act'];
        if (empty($controller)) { $controller = 'Index'; }
        if (empty($action)) { $action = 'Index'; }
		$file = $this->path . $controller . '.php';

		// Файл доступен?
		if (is_readable($file) == false) 
            throw new Exception("Контроллер $controller не найден");
	}
	
	
	function delegate() {

		try {
            $startTime = microtime(true);
		    $successful = 1;
		    $FromCache  = 0;
		    
		    $r = Registry::getInstance();

            $controller_name = 'NULL';
            $action = 'NULL';
			// Анализируем путь
			$this->getController($r, $file, $controller_name, $action);


            // Имя класса контроллера			
			$class = 'Controller_' . $controller_name;


            // Проверяем права на выполнение действия
            // Если для действия ограничен доступ 
            $Permission = $class::CheckPermissions($action, $r);
            if ($Permission!==true) {
                header($_SERVER["SERVER_PROTOCOL"].' 403 Forbidden');
                die($Permission);
            }


            // кэшируем GET запросы 
            if ($_SERVER['REQUEST_METHOD']=='GET' && $r['Env']!='DEV') {
                $p=$r['Params'];
                unset($p['_']); // параметр нужен, что бы отключить кэш браузера, но нам не нужен для самостоятельного управления кэшем 
                $params = str_replace('/', '_', implode('_',$p));
            	$cacheFN = cache_dir.$r['Env'].'_'.$params.'_for_'.$r['Auth']->GetRoles().'.cache';
            	
                // Если существует кэшированная версия:
                  if (file_exists($cacheFN)) {
                    // Читаем и выводим файл
                    readfile($cacheFN);
                    $FromCache  = 1;
                    throw new Exception('Страница выведена из кэша', -777); // для перехода к записи результата в журнал
                  }
            
                // Начинаем буферизацию вывода
                ob_start();
                $buffering = true;
            }
			

			// Подключаем файл
			include_once($file);

			// Создаём экземпляр контроллера
		    $controller = new $class();
			// Действие доступно?
			if (is_callable(array($controller, $action)) == false)
				throw new Exception("Действие '$action' в контроллере '$controller_name' не найдено.");

			$Result = $controller->$action($r);
			
			
			if ($buffering) {
                // Получаем содержимое буфера
                $buffer = ob_get_contents();
                
                // Останов буферирования и вывод буфера
                ob_end_flush();
                
                // Сохранение кэш-файла с контентом
                $fp = fopen($cacheFN, 'w');
                fwrite($fp, $buffer);
                fclose($fp);
			}
			
				
		}
		catch (Exception $e) {
		    // -777 - означает, что страница выведена из кэша. это не ошибка
		    if ($e->getCode()!==-777) {
    		    $successful = 0;
    			$smarty = $r['smarty'];
    			// Если код<0, то это ошибка/предупреждение с низким приоритетом, сформированная программно
    			if ($e->getCode()<0) {
        			$Type = 'Warning';
        			$Title = 'Предупреждение';
        			$TechInfo = '';
    			}
        		else {
        			$Type = 'Error';
        			$Title = 'Ошибка выполнения запрошенного действия';
        			$TechInfo = $e;
        		}
    			$smarty->append('Message',Array('Type'=>$Type,
    											'Title'=>$Title,
    											'Body'=>$e->getMessage(),
    											'TechInfo'=>$TechInfo));	
    			
    			$smarty->display('Layout.tpl');
            }
		}
		finally {
            // Сохраним в вызов в журнал
            global $user_id, $username_clean;
            $endTime = microtime(true);
            
            $log = new Model_Log();
/*
            $log->l_Username    = "$username_clean ($user_id)";
            $log->l_PlayerId    = $r['Auth']->AuthPlayerId;
            
            if ($r['Auth']->AuthPlayerId>0)
                $log->l_PlayerName  = $r['Auth']->AuthPlayer->p_Name;

            $log->l_PlayerRole  = $r['Auth']->GetRoles();
*/
/*
var_dump($r['Auth']);
echo "l_PlayerRole={$log->l_PlayerRole} ";
echo "Auth->Roles={$r['Auth']->GetRoles()}";
die();
*/
/*
            $log->l_Method      = $_SERVER['REQUEST_METHOD'];
            $log->l_URL         = $r['curPageURL'];
*/
            $log->l_Controller  = $controller_name;
            $log->l_Action      = $action;
            $log->l_Params      = json_encode($r['Params']);
            $log->l_Result      = isset($Result) ? $Result : null;
            $log->l_FromCache   = $FromCache; 
            $log->l_Successful  = $successful;
            $log->l_Duration    = round($endTime - $startTime,3);
            
            $log->Save();
		}
		
        

	}
	
	// Очистка кэша. вызывается в методах контроллеров, которые изменяют данные
	function clearCache() {
        $startTime = microtime(true);
        
        $log = new Model_Log();
        $log->l_Controller  = 'Router';
        $log->l_Action      = 'clearCache';
        $log->l_FromCache   = 0; 
        try {
            $CacheFiles = glob(cache_dir.'*'); // get all file names
            foreach($CacheFiles as $CacheFile){ // iterate files
              if(is_file($CacheFile))
                unlink($CacheFile); // delete file
            }
    
            $log->l_Result = count($CacheFiles);
            $log->l_Successful  = 1;
        }
        catch (Exception $e) {
            $log->l_Successful  = 0;
            
        }
        
        $endTime = microtime(true);
        
        $log->l_Duration    = round($endTime - $startTime,3);
        
        $log->Save();
	}   //  function clearCache()
}	
?>
