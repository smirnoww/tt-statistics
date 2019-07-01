<?php
Class Model_TourGroup Extends Model_Base {

	protected static $TableName = 'x_TourGroups';
	protected static $PrimaryKey = 'g_Id';

    // Возвращает массив групп турнира TODO: need for test
    public static function GetTourGroups($TourId) {
        return self::GetList("g_TourId = :TourId","*",array(':TourId'=>$TourId));
    }
}


?>