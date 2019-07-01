<?php

    // Определим значения констант для ролей
    define('anonym_AR',  1);
    define('admin_AR',   2);
    define('tourorg_AR', 4);
    define('player_AR',  8);
    
    //Передадим их в Smarty
    $r = Registry::GetInstance();
    $smarty = $r['smarty'];
    $smarty->assign('anonym_AR',    anonym_AR);
    $smarty->assign('admin_AR',     admin_AR);
    $smarty->assign('tourorg_AR',   tourorg_AR);
    $smarty->assign('player_AR',    player_AR);

	// =================================================
	// получаем авторизировавшегося пользователя форума
 
	try{

		// открываем папку корня phpbb, что бы api работал

		// определим пользователя

		$curdir = getcwd();
		chdir(phpbb3_path);
		$phpbb_root_path = './';
		
		define('IN_PHPBB', true);
		$phpEx = substr(strrchr(__FILE__, '.'), 1);
		$includefile = 'common.' . $phpEx;
		include($includefile);

		// Start session management
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();


        // получим id пользователя и логин из форума
		$user_id = $user->data['user_id'];

        $username_clean = $user->data['username_clean'];
//echo "$user_id :$username_clean\n";		
		$user_data = $user->data;
		chdir($curdir);
	}
	catch (Exception $e) {
		$user_id = -2;
		$username_clean = $e->getMessage();
	}


	$R = Registry::getInstance();
	$auth = new Model_Auth();

	$R['Auth'] = $auth;
    
	// получили авторизировавшегося пользователя форума
	//==================================================
	
	
Class Model_Auth {
	Private $ForumUserData;
	
	Public $AuthPlayer;
	Public $AuthPlayerId;
	Private $Roles = 1; // битовая маска из id ролей доступных пользователю. 1 - аноним
	Private $Admins = array(1,2,4,5); // администраторы 
	Private $TourOrganizers = array(71, 1168,664); // Организаторы турниров 1168=test,664=Тест

	function __construct() {
		global $user_id, $username_clean, $user_data;
		
		// Находим игрока по пользователю форума
		if (isset($username_clean)) {
			$this->AuthPlayer = Model_Player::GetPlayerByUsername($username_clean);
			if (isset($this->AuthPlayer)) {
			    $this->Roles = $this->AuthPlayer->p_Roles;
			    $this->AuthPlayerId = $this->AuthPlayer->p_Id;
			}
			else
			    $this->AuthPlayerId = -1;
//echo get_class($this->AuthPlayer).":".$this->AuthPlayer->p_Name."\n";
			$this->ForumUserData = $user_data;
//echo "group_id:".$this->ForumUserData['group_id'].",(".$this->group_id.")\n";
		}

/*
        //------------------------------------
        // Разграничение доступа

        if ($this->AuthPlayerId > 0) 
            $this->Roles += player_AR; // доступна роль Игрок

        if (in_array($this->AuthPlayerId, $this->TourOrganizers) )
            $this->Roles += tourorg_AR; // доступна роль Организатор турниров

        if (in_array($this->AuthPlayerId, $this->Admins) )
            $this->Roles += admin_AR; // доступна роль Администратор

        // Разграничение доступа
        //------------------------------------
*/		                                                        

	}

    function GetRoles() {
        return $this->Roles;
    }	
    
    // CheckRoles
    function CR($roles) {
        return ($this->Roles & $roles) > 0 ;
    }	
    
	// Возвращает сведения о пользователе форума
	function __get($name) {
	    if (isset($this->ForumUserData[$name]))    
	        return $this->ForumUserData[$name];
	    else
	        return null;
	}

}


?>