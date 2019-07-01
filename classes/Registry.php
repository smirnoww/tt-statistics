<?php

Class Registry Implements ArrayAccess, JsonSerializable {
    private $vars = array();
    private $DBs = array ('stat'=>null, 'forum'=>null); // два экземпляра PDO для соединений с БД статистики и форума
	private static $_instance; //The single instance

	public static function getInstance() {
		if(!self::$_instance) { // If no instance then make one
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	function __construct() {
		// В конструкторе получим _GET и _POST параметры и сохраним их в глобальном массиве
		$this->vars['Params'] = array();
		foreach ($_POST as $key => $value) 
			$this->vars['Params'][$key] = $value;
		foreach ($_GET as $key => $value) 
			$this->vars['Params'][$key] = $value;
	}	
	
	function set($key, $var) {
        if (isset($this->vars[$key]) == true) {
                throw new Exception('Unable to set var `' . $key . '`. Already set.');
        }
        
        $this->vars[$key] = $var;
        return true;
	}


	function reset($key, $var) {
        $this->vars[$key] = $var;
        return true;
	}


	function get($key) {
        if (isset($this->vars[$key]) == false) {
                return null;
        }
        return $this->vars[$key];
	}


	function remove($var) {
        unset($this->vars[$key]);
	}
	
	function offsetExists($offset) {
        return isset($this->vars[$offset]);
	}

	function offsetGet($offset) {
		return $this->get($offset);
	}

	function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	function offsetUnset($offset) {
        unset($this->vars[$offset]);
	}	
	
	// Отправка почты
	static function sendmail($to,$subj='(без темы)',$body='(без содержания)') {
		$headers = "From: info@tt-saratov.ru\n"; 
		$headers .= "MIME-Version: 1.0\n"; 
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		$headers .="Content-Transfer-Encoding: 8bit";

		$res = mail($to, "=?UTF-8?B?".base64_encode($subj)."?=", $body,$headers);
		
		return $res;
	}


/*
	//Получение файлов из массива $_FILES
	static function GetFileParam($param_name, $max_size=0.7, $masks=Array('*.*')) {
		
		// Если файл не передан, вернём null
		if (isset($_FILES[$param_name])) 
			$file = $_FILES[$param_name];
		else 
			return null;

		//Если передан один файл
		if (!is_array($file["tmp_name"])) {
			return GetOneFileParam($file, $max_size, $masks);
		}
		else { // Если передан массив файлов
			// Пересортируем массив с файлами
		    foreach ( $file as $key => $all )
				foreach ( $all as $i => $val )
					$files[$i][$key] = $val;    

			$Result = Array();
			
			Foreach ($files as $key => $file)
				$Result[$key] = GetOneFileParam($file, $max_size, $masks);
				
			Return $Result;

		}
	}
*/
	// Magic method clone is empty to prevent duplication of connection
	private function __clone() { }

/* ======================================================================= */
/* ===                     JsonSerializable                            === */
    public function jsonSerialize() {
        return $this->vars;
    }
/* ===                     JsonSerializable                            === */
/* ======================================================================= */
}

?>