<?php
Class Model_Gallery Extends Model_Base {

	protected static $TableName = '';
	protected static $PrimaryKey = '';

    public static function GetOne($photo_id) {
        return new Model_Gallery($photo_id);
    }

    public static function GetList($offset=0, $limit=100) {
    }    

    public static function GetCount($album_id=0) {
    }    
    
    function __construct($photo_id) {
        
    }
    
    public function __get($name) {
         
    }
     
    public function __call($name, $arguments) {return null;}
     
    public function save() {}
    
    public function delete() {}
}


?>