<?php

    header('Content-Type: text/html; charset=utf-8');

	if (version_compare(phpversion(), '5.4.0', '<') == true) { die ('PHP5.4 Only'); }
	
	setlocale(LC_ALL, 'russian');
	
	//----------------------------
	// Загрузка классов «на лету»
	function mvcautoload($class_name) {

		// пробуем искать в папке classes
		$filename = $class_name . '.php';
		$file = site_path . 'classes' . DIRSEP . $filename;

		if (file_exists($file) == false)
			$file = null;

		// Если в classes класс не найден, то ищем в папке classes уровнем выше
		if (!isset($file)) {
			$file = site_path . '..' . DIRSEP . 'classes' . DIRSEP . $filename;
			
			if (file_exists($file) == false)
				$file = null;
		}

			
		// Если в classes класс не найден, то ищем модель в папке models
		if (!isset($file) && strpos($class_name,'Model_')===0){
			$filename = str_replace('Model_','',"$class_name.php");
			$file = site_path . 'models' . DIRSEP . $filename;

			if (file_exists($file) == false) 
				$file = null;
		}
		
		// Если класс не найден, то ищем в папке Controllers
		if (!isset($file) && strpos($class_name,'Controller_')===0){
			$filename = str_replace('Controller_','',"$class_name.php");
			$file = site_path . 'controllers' . DIRSEP . $filename;

			if (file_exists($file) == false) 
				$file = null;
		}
		
		// если файл не нашли, то ...
		if (isset($file))
			// Подключим найденный файл
			include_once($file);
		else
			// Иначе выйдем
			return false;
	}
	spl_autoload_register('mvcautoload');

	//------------------------------
	// Константы:
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	// Узнаём путь до файлов сайта
	$site_path = realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP) . DIRSEP;
    
	define ('site_path', $site_path);

//$res = chdir('/home/f/folkex/public_html/phpbb3');
//echo get_current_user ( )." @ getcwd()=".(($res) ? 'true=' : 'false=').getcwd()."<br>\n";
//die($site_path."<br>".realpath($site_path.'../../public_html'));

    define ('phpbb3_path', realpath($site_path.'..'.DIRSEP.'..'.DIRSEP.'phpbb3'));
	define('SMARTY_DIR', site_path.'..'.DIRSEP.'Smarty'.DIRSEP.'libs'.DIRSEP);
	define('cache_dir', site_path.'..'.DIRSEP.'.cache'.DIRSEP);

    //------------------------------------------------
	// Класс для глобальных данных
	$R = Registry::getInstance();
	
	// текущий URL
	function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"])) 
            if ($_SERVER["HTTPS"] == "on") 
                {$pageURL .= "s";}
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
        } 
        else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
        }

        return $pageURL;
    }

	$R['curPageURL']	=  curPageURL();
	$R['ImageFolderURL']	= substr($R['curPageURL'], 0, strrpos($R['curPageURL'],"/")+1) . "images/";


    //------------------------------------------------------------------------------
    // определим среду выполнения (продуктив/тест/разработка)
    if (strpos($R['curPageURL'], '_dev')!==false)
        $R['Env'] = 'DEV';
    elseif (strpos($R['curPageURL'], '_test')!==false)
        $R['Env'] = 'TEST';
    else 
        $R['Env'] = 'PROD';
    // определим среду выполнения (продуктив/тест/разработка)
    //------------------------------------------------------------------------------
    
    
    
    // Если не продуктивная среда, то включим все сообщения
    if ($R['Env'] != 'PROD') {
        error_reporting (E_ALL);
        ini_set('display_errors', 1);

        // Проверим, что правильно определён путь до движка форума
    	if (!file_exists(phpbb3_path))
    	    throw new Exception ('Invalid phpbb3 path:' . phpbb3_path . '  site_path:' . $site_path, -1);
    }
    
    
	//-------------
	// Smarty init
	require_once(SMARTY_DIR . 'Smarty.class.php');
	try {
		$mvcsmarty = new MVCSmarty();	
		$mvcsmarty->assign('curPageURL',$R['curPageURL']);
		$mvcsmarty->assign('ImageFolderURL',$R['ImageFolderURL']);
		$mvcsmarty->assign('Registry',$R);
	}
	catch(Exception $e) {
		// TODO: Обработать ошибку
		echo $e->getMessage();
	}
	
	$R['smarty']=$mvcsmarty;
	

	
	// Smarty init
	//-------------
	
	
	//-----------------------------------------------------------------
	//Здесь определяем логин авторизировавшегося пользователя

		include_once(site_path . 'models' . DIRSEP . 'Auth.php');
//var_dump($auth);
		// Передадим в шаблон инфу об авторизировавшемся игроке
		$mvcsmarty->assign('Auth', $R['Auth']);
	try {	}
	catch (Exception $e) {
	    /*
		$mvcsmarty->append('Message',Array('Type'=>'Error',
										'Title'=>'Ошибка авторизации',
										'Body'=>$e->getMessage(),
										'TechInfo'=>$e));	
		$mvcsmarty->display('Layout.tpl');
		exit;
		*/
	}

	// echo "Auth: " . get_class($R['Auth']);
	// echo "<br>group_id" . $R['Auth']->group_id.":". is_string($R['Auth']->group_id);

	//Здесь определяем логин авторизировавшегося пользователя
	//-----------------------------------------------------------------

    
?>