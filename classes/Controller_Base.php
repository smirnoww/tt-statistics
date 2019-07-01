<?php


Abstract Class Controller_Base {

    abstract function index($r);
    
    static function CheckPermissions($action, $r) {
return true;
        $Auth = $r['Auth'];
        
        $result = true;
        
    	//Доступ к среде для разработки доступен только главным модераторам
    	if ($Auth->CR(admin_AR) && $r['Env']=='DEV') 
            $result = $_SERVER["SERVER_PROTOCOL"].' 403 Forbidden'; //+"Доступ к среде для разработки доступен только главным модераторам. username=".$Auth->username."; Roles=".$Auth->GetRoles()."; Env=".$r['Env'];
    	
    	//Доступ к тестовой среде доступен только главным модераторам и организаторам
    	if ($Auth->CR(admin_AR + tourorg_AR)  && $r['Env']=='TEST') 
            $result = $_SERVER["SERVER_PROTOCOL"].' 403 Forbidden'; //+"Доступ к тестовой среде доступен только главным модераторам и организаторам турниров username=".$Auth->username."; Roles=".$Auth->GetRoles()."; Env=".$r['Env'];
//echo "base::CheckPermissions=$result";
        
        return $result;

    }
    
}


?>