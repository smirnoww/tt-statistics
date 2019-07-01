<?php

Abstract Class Model_Base Implements ArrayAccess, JsonSerializable { 

    protected static $DBInstances;

    protected static $DBInstanceName    = 'stat';
    protected static $TableName         = '';
    protected static $PrimaryKey        = '';
    
    protected static $Fields = array();
    protected $Data;   // Model properties in DB format

    public function showModelInfo() {
        echo "<br><br>-----Model info: ".static::$TableName."(".static::$PrimaryKey."=".$this->Data[static::$PrimaryKey].")------<br>";
        print_r($this->Data);
        echo "<br>-----End of Model info: ".static::$TableName."(".static::$PrimaryKey."=".$this->Data[static::$PrimaryKey].")------<br><br>";
    }
    
    public static function showStaticInfo() {
        echo "<br><br>-----Static info: ".static::$TableName."(".static::$PrimaryKey.")------<br><br>";
        self::GetFields();
        print_r(static::$Fields[static::$TableName]);
    }
    
    
    // Возвращает список поле БД, соответствующей модели таблицы
    protected static function GetFields() {
        // если списка полей ещё нет, получим из БД
        if (!isset(self::$Fields[static::$TableName])) {
            // получим список полей в БД TODO: сделать передачу имени таблицы через параметр
            $fields = self::ExecArraySQL("desc ".static::$TableName, static::$DBInstanceName);
            // перенесём в атрибут класса
            // В качестве индекса массива сделаем наименование поля таблицы
            $class_fields = array();
            foreach ($fields as $key => $field) {
                $class_fields[$field['Field']] = $field;
            }
            self::$Fields[static::$TableName] = $class_fields;
            unset($class_fields);
    	    unset($Fields);
        }
        
        return self::$Fields[static::$TableName];
    }


    //  список полей для загрузки
    // $infields=N          - первые N не блоб поля
    // $infields="*"        - все не blob поля
    // $infields="**"       - все поля
    // $infields=array(...) - список полей для загрузки
    protected static function GetFieldsForLoad($infields=3) {  

        // Получим список полей в БД
        $fields = self::GetFields();

        // сформируем список полей для загрузки
        // первичный ключ будет всегда
        $fieldForLoad[static::$PrimaryKey] = static::$PrimaryKey;

        if (is_int($infields)) { // Задано количество полей
            foreach($fields as $dbFieldName => $dbFieldInfo) {
                if (count($fieldForLoad)<$infields) {
                 if (
                        (stripos($dbFieldInfo['Type'],'blob') === false)
                        && (stripos($dbFieldInfo['Type'],'text') === false)
                    ) 
                 		 $fieldForLoad[$dbFieldName] = $dbFieldName;
                  }
                else
                    break; // если задано кол-во полей для загрузки, то выходим когда достигнем нужного количества
            }
        }
        elseif ($infields=="*") { // все не blob поля
            foreach($fields as $dbFieldName => $dbFieldInfo) {
                if (
                        (stripos($dbFieldInfo['Type'],'blob') === false)
                        && (stripos($dbFieldInfo['Type'],'text') === false)
                    ) 
                    $fieldForLoad[$dbFieldName] = $dbFieldName;
            }
        }
        elseif (is_array($infields)) {
            foreach($fields as $dbFieldName => $dbFieldInfo)
                if (in_array($dbFieldName, $infields)) 
                    $fieldForLoad[$dbFieldName] = $dbFieldName;
        }
        elseif ($infields=="**") { // Все поля
            foreach($fields as $dbFieldName => $dbFieldInfo)
                $fieldForLoad[$dbFieldName] = $dbFieldName;
        }
        else {
            $ModelClassName = get_called_class();

            throw new Exception("На вход функции $ModelClassName::GetFieldsForLoad($infields) можно передать int - количество полей, '**' - все поля, включая blob, '*' - все поля без blob или массив полей для загрузки ");
        }
        


        // если среди загружаемых полей нет идентификатора, то добавим его в список запрашиваемых полей
        $fieldForLoad[static::$PrimaryKey] = static::$PrimaryKey;        
        return $fieldForLoad;
    }
    

    // вернём одну модель
    // если $model_Id === null, то вторым параметром можно передать предзагруженные данные для создания новой заполненной модели
    // если $model_Id !== null, то вторым параметром можно передать поля для начальной загрузки модели из бд
    public static function GetOne( $model_Id, $secondParam=3 ) {
        $ModelClassName = get_called_class();
        try {
			$DBData = array();
			
			// если второй параметр - массив, то переведём его в формат бд
			if (is_array($secondParam)) {
				foreach ($secondParam as $key => $value)
					$DBData[$key] = static::CastValueToDBFormat($key, $value);
				$secondParam = $DBData;
			}
				
            $model = new $ModelClassName( $model_Id, $secondParam );
            return $model;
        }
        catch (Exception $e) {
            //echo "Exception {$e->getMessage()}\n<br>";
            return null;
        }
    }

    
    // вернём массив с моделями
    public static function GetList($where = 'true', $fields=3, $params=array() ) {
//echo get_called_class()."->GetList(..., ..., ".json_encode($params).")<br>\n";
        // Получим список полей в БД
        $fields = static::GetFieldsForLoad($fields);


        $fieldForLoadStr = implode(', ', $fields);
        
        $query = "select $fieldForLoadStr from ".static::$TableName." where $where";
        $sqlres = self::ExecArraySQLWithParams($query, $params, static::$DBInstanceName);
        $ModelsList = array();
        $ModelClassName = get_called_class();
        foreach ($sqlres as $sqlrow) {
            $model = new $ModelClassName(null, $sqlrow); //id не передаём, что бы второй параметр воспринимался как предзагруженные данные
            $ModelsList[] = $model;
        }

        return $ModelsList;
    }
    
    // вернём количество моделей по запросу
    public static function GetCount($where = 'true', $params=array() ) {
        $ModelsCount = self::ExecScalarSQLWithParams("select count(1) ModelsCount from (select 1 from ".static::$TableName." where $where) t", $params, static::$DBInstanceName);
        return $ModelsCount;
    }    
    
    
    // если $model_Id === null, то вторым параметром можно передать предзагруженные данные для создания новой заполненной модели
    // если $model_Id !== null, то вторым параметром можно передать поля для начальной загрузки модели из бд
    function __construct($model_Id = null, $secondParam=3) { 
// echo "__construct($model_Id = null, $secondParam=array())<br>";
// echo "\n<br>\n". get_called_class()." preloaded data: ";
// print_r( $secondParam );

        $this->Data = Array();
        
        
        // если $model_Id === null, то вторым параметром можно передать предзагруженные данные для создания новой заполненной модели
        if ($model_Id === null) {
            $PreloadedData = $secondParam;
            
            // если передан массив предзагруженных данных, то поместим их в модель
            if ( is_array($PreloadedData) ) 
                foreach ($PreloadedData as $key => $value)
                    $this->$key = $value;
        }

        // если $model_Id !== null, то вторым параметром можно передать поля для начальной загрузки модели из бд
        if ($model_Id !== null) {

            $this->Data[static::$PrimaryKey] = $model_Id;

            $fields = $secondParam;
            // загрузим модель из БД
            // Получим список полей в БД и для загрузки
            $fieldForLoad = static::GetFieldsForLoad($fields);
            $fieldForLoadStr = implode(', ', $fieldForLoad);
            
            $Attributes = self::ExecArraySQLWithParams("select $fieldForLoadStr from ".static::$TableName." where ".static::$PrimaryKey." = :modelId", Array(':modelId' => $model_Id), static::$DBInstanceName);
            if (count($Attributes)==0) {
                $ModelClassName = get_called_class();
                throw new Exception("$ModelClassName($model_Id) not found in DB");
            }
            else
                $this->Data = $Attributes[0];
            // загрузили модель из БД
        }
    }
    
    function __destruct() {
       unset($Data);
    }
    
    // Вернём атрибут модели - Lasy load
    public function __get($name) {
//        echo "\n\n<!-- __get($name): select $name from ".static::$TableName." where ".static::$PrimaryKey."=:".static::$PrimaryKey." -->\n\n";

        //  и этот атрибут не был запрошен ранее, запросим его из БД
        if (!isset($this->Data[$name])) {
            // Если экземпляр новый (не сохранён в бд) и реквизита нет то вернём Null
            if (empty($this->Data[static::$PrimaryKey])){ 
                 $this->Data[$name] = null;
            }
            else //если не новый, то запросим в БД
                try {
            	    $query = "select $name from ".static::$TableName." where ".static::$PrimaryKey."=:".static::$PrimaryKey;
            	    $this->Data[$name] = self::ExecScalarSQLWithParams($query, array(':'.static::$PrimaryKey => $this->Data[static::$PrimaryKey]), static::$DBInstanceName);
                }
                catch (Exception $e) {
                    $this->Data[$name] = null;
        	        //throw new Exception("Property $name of ".self::$TableName." quering error. May be player property $name not exists!",0,$e);
                }
        }

        return static::CastDBValueToModelFormat($name, $this->Data[$name]);
    }
    
    // Временно запомним атрибут модели
    public function __set($name, $value) {
        $this->Data[$name] = static::CastValueToDBFormat($name, $value);
    }    
    
    // вернём экземпляр другой модели по ссылке
    public function __call($name, $arguments) {
//echo "__call($name, $arguments)<br>";
        if (isset($arguments[0])) {
            $modelName = $arguments[0];
            if (!class_exists($modelName)) 
                throw new Exception("class $modelName not exists");
        }
        else
            return null;
            
        $model_Id = $this->$name;

        if (!empty($model_Id)) {
            return new $modelName($model_Id);
        }
        else
            return null;
    }
    
    public function __isset($name) {
        $data = $this->$name;
        return isset($data);
    }
    
    // сохраним модель
    public function save() {

        // поля таблицы в БД, что бы короче было.
        $Fields = self::GetFields();
        
        // скомпонуем запрос для вставки/обновления данных
        $fieldNames = "";
        $fieldParams = "";
        $updateField = "";
        
        $DataForSave = array();
        
	    foreach ($this->Data as $name => $value)
	        if (isset($Fields[$name])) {
				if ( ($name != static::$PrimaryKey) || ($this->Data[static::$PrimaryKey] > 0) ) { // не включаем в список полей первичный ключ, если он меньше нуля, что бы база данные сама создала идентификатор
					$fieldNames .= "$name,";
					$fieldParams .= ":$name,";
					$updateField .= "$name = :$name,"; 

					// сохранять значение будем если недопустимы Null или есть значение для сохранения. Если допустим NULL и значение отсутствует, то сохраним null
					if (($Fields[$name]['Null']=='NO') || ($value > ''))                     
						$DataForSave[":$name"] = $value;
					else
						$DataForSave[":$name"] = null;
				}
                
	        }

	    $fieldNames = rtrim($fieldNames, ',');
	    $fieldParams = rtrim($fieldParams, ',');
	    $updateField = rtrim($updateField, ',');

//echo "<pre>".json_encode($this,JSON_PRETTY_PRINT)."</pre>\n\n<br>";
		
        if ($this->Data[static::$PrimaryKey] > 0) 
            $query = "update ".static::$TableName." set $updateField where ".static::$PrimaryKey." = ".$this->Data[static::$PrimaryKey];
        else 
            $query = "insert into ".static::$TableName." ($fieldNames) values ($fieldParams)"; 

//echo "<pre>$query</pre>\n\n<br>";


        // Выполним запрос и
         $res = self::ExecuteSQLWithParams($query,  $DataForSave, static::$DBInstanceName);
         if ($res===false)
            throw new Exception("query error: $query");
            
         // запомним полученный идентификатор
        if (is_int($res))
	        $this->Data[static::$PrimaryKey] = $res;

    }   // save()  

    // удалим модель
    public function delete() {
        try {
            if ($this->Data[static::$PrimaryKey]>0) {
    	        $query = "delete from ".static::$TableName." where ". static::$PrimaryKey ." = :Id"; 
        	    $stm = self::ExecuteSQLWithParams($query, array( ':Id' => $this->Data[static::$PrimaryKey]), static::$DBInstanceName);
            }
            $this->Data = Array();
            return true;
        }
        catch (Exception $e) {
            $ModelClassName = get_called_class();
            throw new Exception("Exception $ModelClassName ->Delete(): {$e->getMessage()}", 0, $e);
        }
    }    

    // Преобразуем данные модели к формату БД
    protected static function CastValueToDBFormat($name, $value) {
//echo "Model_Base::CastValueToDBFormat($name, $value)<br>";
        // поля таблицы в БД, что бы короче было.
        $Fields = static::GetFields();

        $FieldType = strtoupper($Fields[$name]['Type']);
        switch ($FieldType) {
            case 'DATE':
                    if (!is_object($value)) {
    					$date = DateTime::createFromFormat('d.m.Y', $value); 	
    					if($date!==false) $value = $date->format('Y-m-d');
                    }
                    if (is_object($value))
                        if (get_class($value)=='DateTime')
                            $value = $value->format('Y-m-d');
                break;
            case 'DATETIME':
                    if (!is_object($value)) {
    					$date = DateTime::createFromFormat('d.m.Y H:i', $value); 	
    					if($date===false) $date = DateTime::createFromFormat('d.m.Y H:i:s', $value); 	
    					if($date!==false) $value = $date->format('Y-m-d H:i:s');
                    }
                    
                    if (is_object($value))
                        if (get_class($value)=='DateTime')
                            $value = $value->format('Y-m-d H:i');
                break;
            case 'TIME':
                    if (!is_object($value)) {
    					$date = DateTime::createFromFormat('H:i', $value); 	
    					if($date!==false) $value = $date->format('H:i');
                    }
                    if (is_object($value))
                        if (get_class($value)=='DateTime')
                            $value = $value->format('H:i:s');
                break;                
            case 'TIMESTAMP':
                if (get_class($value)=='DateTime')
                    if (is_object($value))
                        if (get_class($value)=='DateTime')
                            $value = $value->format('Y-m-d H:i:s');
                break;                
            case '':
                break;                
        }
        
        return $value;
    }
    
    
    // Преобразуем данные БД к формату модели 
    protected static function CastDBValueToModelFormat($name, $invalue) {
        // поля таблицы в БД, что бы короче было.
        $Fields = self::GetFields();

        if ( key_exists( $name, $Fields )) {
            $FieldType = strtoupper($Fields[$name]['Type']);
            switch ($FieldType) {
                case 'DATE':
                    $outvalue = Model_DateTime::createFromFormat('Y-m-d', $invalue);
                    $outvalue->setDefaultFormat('d.m.Y');
                    break;
                case 'DATETIME':
                    $outvalue = Model_DateTime::createFromFormat('Y-m-d H:i:s', $invalue);
                    $outvalue->setDefaultFormat('d.m.Y H:i');
                    break;
                case 'TIME':
                    $outvalue = Model_DateTime::createFromFormat('H:i:s', $invalue);
                    $outvalue->setDefaultFormat('H:i');
                    break;                
                case 'TIMESTAMP':
                    $outvalue = Model_DateTime::createFromFormat('Y-m-d H:i:s', $invalue);
                    $outvalue->setDefaultFormat('d.m.Y H:i');
                    break;                
                case '':
                    
                    break;                
                default:
                    $outvalue = $invalue;
            }
        }
        else {
            $outvalue = $invalue;
        }
        
//echo "\n\n<!--\n\ninvalue=$invalue;outvalue=$outvalue\n\n-->\n\n";
        return $outvalue;
    }
 
/* ======================================================================= */
/* ===                     Функции для работы с БД ===                     */

    static function getDBInstance($InstanceName='noname'){
//echo "call getDBInstance($InstanceName) <br>";
        
        // Если экземпляра PDO ещё не было создано, то создадим его
        if (!isset(self::$DBInstances[$InstanceName])) {
            // Зададим БД для статистики и форума
    	    $DB_Config	= array(
    	                    'statdev'   => array('dbhost' => 'localhost','dbname' => 'folkex_rate_t','dbuser'   => 'folkex_rate_t','dbpwd'  => 'etar_xeklof'), 
    	                    'stat'      => array('dbhost' => 'localhost','dbname' => 'folkex_rate','dbuser'     => 'folkex_rate','dbpwd'    => 'etar_xeklof'), 
    			            'forum'     => array('dbhost' => 'localhost','dbname' => 'folkex_phpbb','dbuser'    => 'folkex_phpbb','dbpwd'   => 'bbphp121234')
    			         ); 
    			            
            if (isset($DB_Config[$InstanceName]))
                $InstanceConfig = $DB_Config[$InstanceName];
            else
                throw new Exception("DB $InstanceName not configured!");
            
            $dsn    = "mysql:host=".$InstanceConfig['dbhost'].";dbname=".$InstanceConfig['dbname']."";
            $dbuser = $InstanceConfig['dbuser'];
            $dbpass = $InstanceConfig['dbpwd'];
            
            $newInstance = new PDO($dsn,
                                    $dbuser,
                                    $dbpass,
                                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'")
                                );
            $newInstance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            
            self::$DBInstances[$InstanceName] = $newInstance;
    
//echo "\n<br>\nDBInstance $InstanceName have created\n<br>\n";
        }
        
        return self::$DBInstances[$InstanceName];
    }
    
    static function ExecuteSQLWithParams($sql, $params, $InstanceName='stat') {
//echo "call ExecuteSQLWithParams($sql, $params, $InstanceName) <br>";

        $db = self::getDBInstance($InstanceName);
        $db->beginTransaction();
//echo "<br>$sql<br>";
//var_dump($params);
        try {
			$stm = $db->prepare($sql);
			
			// зададим значения параметров запроса
			if (is_array($params)) 
			    foreach ($params as $paramName => $paramValue) {
	                $nextParamBinded = false;
			        if (is_array($paramValue))
			            if (isset($paramValue['value']) && isset($paramValue['type'])) {
			                $stm->bindValue($paramName, $paramValue['value'], $paramValue['type']);
			                $nextParamBinded = true;
//echo "<br>stm->bindValue($paramName, {$paramValue['value']}, {$paramValue['type']});";
                        }
			        if (!$nextParamBinded) {
//echo "\n<br>$sql: stm->bindValue($paramName, $paramValue);<br>\n";
			            $stm->bindValue($paramName, $paramValue);
			        }
			    }

			if ($stm->execute())
				$res = $stm;
			else 
			    $res = false;

            //-----------------------------------------------------------------------------------------------
            // на непродуктивном ландшафте зафиксируем выполняемый запрос в логе для последующей оптимизации
            $r = Registry::getInstance();
            
            if ($r['Env']!='PROD') {
                $log = $r['QueriesLog'];
                $log[]= array(
                                'Called from'=>get_called_class(),
                                $InstanceName=>$sql, 
                                'params'=>$params,
                                'success'=>($res!==false)
                            );
                $r->reset('QueriesLog', $log);
            }
            // на непродуктивном ландшафте зафиксируем выполняемый запрос в логе для последующей оптимизации
            //-----------------------------------------------------------------------------------------------

            // Если была вставка, то получим новый Id
            if ((strpos($sql,'insert') !== false) ||(strpos($sql,'replace') !== false))
                $res = intval($db->lastInsertId());

            $db->commit();
            return $res;
        }
        catch (PDOException $e) {
            $db->rollback();
            $strparams = print_r($params, true);
            throw new Exception("Exception of execution $sql with params: $strparams - {$e->getMessage()}", 0, $e);
        }
    }
    
    static function ExecArraySQL($sql, $InstanceName='stat') {
        return self:: ExecArraySQLWithParams($sql, array(), $InstanceName);
    }
    
    static function ExecArraySQLWithParams($sql, $params, $InstanceName='stat') {
//echo "call ExecArraySQLWithParams($sql, $params, $InstanceName) <br>";
        $stm = self::ExecuteSQLWithParams($sql, $params, $InstanceName);
        
        if (get_class($stm) == 'PDOStatement') {
            $Result = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm->closeCursor();
        }
        else 
            $Result = $stm;
        
        return $Result;
    }
    
    static function ExecScalarSQL($sql, $InstanceName='stat') {
        return self:: ExecScalarSQLWithParams( $sql, array(), $InstanceName );
    }

    static function ExecScalarSQLWithParams($sql, $params, $InstanceName='stat') {
            $stm = self::ExecuteSQLWithParams($sql, $params, $InstanceName);
            
            if (get_class($stm) == 'PDOStatement') {
                $Result = $stm->fetchColumn();
                $stm->closeCursor();
            }
            else 
                $Result = $stm;
            
            return $Result;
    }    

/* ===                     Функции для работы с БД                     === */
/* ======================================================================= */



/* ======================================================================= */
/* ===                     Implements ArrayAccess                      === */
	function offsetExists($offset) {
        return isset($this->Data[$offset]);
	}

	function offsetGet($offset) {
		return $this->$offset;
	}

	function offsetSet($offset, $value) {
		$this->$offset = $value;
	}

	function offsetUnset($offset) {
        unset($this->Data[$offset]);
	}
/* ===                     Implements ArrayAccess                      === */
/* ======================================================================= */


/* ======================================================================= */
/* ===                     JsonSerializable                            === */
    public function jsonSerialize() {
        return $this->Data;
    }
/* ===                     JsonSerializable                            === */
/* ======================================================================= */
}

?>