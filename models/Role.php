<?php
Class Model_Role Extends Model_Base {

	protected static $TableName = '';
	protected static $PrimaryKey = 'r_Id';

	protected static $Roles = array(
	                                1 => array(
	                                            'r_Id' => 1,
                                                'r_Name' =>'Аноним'
                                            ),
	                                2 => array(
	                                            'r_Id' => 2,
                                                'r_Name' =>'Администратор'
                                            ),
	                                4 => array(
	                                            'r_Id' => 4,
                                                'r_Name' =>'Организатор турниров'
                                            ),
	                                8 => array(
	                                            'r_Id' => 8,
                                                'r_Name' =>'Игрок'
                                            )
	                            );

    // Возвращает список поле БД, соответствующей модели таблицы
    protected static function GetFields() {
        return array();
    }

    public static function GetOne($model_Id, $secondParam=3) {
        $R = new Model_Role(null, self::$Roles[$model_Id]);
        return $R;
    }

    public static function GetList($where = 'true', $fields=3, $params=array()) {
        $RolesList = array();
        foreach(self::$Roles as $r_Id => $Role) 
            $RolesList[$r_Id] = new Model_Role(null, $Role);
//var_dump($RolesList);
        return $RolesList;
    }    

    public static function GetCount($where = 'true', $params=array()) {
        return count(self::$Roles);
    }    
    
    public function __get($name) {
        //  и этот атрибут не был запрошен ранее, запросим его из БД
        if (isset($this->Data[$name]))
            return $this->Data[$name];
        else 
            return null;
    }
     
    public function save() {}
    
    public function delete() {}
}


?>