<?php
Class Model_Rank Extends Model_Base {

	protected static $TableName = 'x_Ranks';
	protected static $PrimaryKey = 'r_Id';
	
	function __toString (){
		return $this->r_Name;
	}

}


?>