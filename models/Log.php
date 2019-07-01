<?php
Class Model_Log Extends Model_Base {

	protected static $TableName = 'x_Log';
	protected static $PrimaryKey = 'l_Id';
	
	function __construct() {
        global $user_id, $username_clean;
        $r = Registry::getInstance();

        // общий конструктор из Model_Base
        parent::__construct();
        $this->l_Username    = "$username_clean ($user_id)";
        $this->l_PlayerId    = $r['Auth']->AuthPlayerId;
        if ($r['Auth']->AuthPlayerId>0)
            $this->l_PlayerName  = $r['Auth']->AuthPlayer->p_Name;
        $this->l_PlayerRole  = $r['Auth']->GetRoles();
        
        $this->l_Method      = $_SERVER['REQUEST_METHOD'];
        $this->l_URL         = $r['curPageURL'];
        
        $this->l_Params      = '';
        
	}   //  __construct()
}


?>