<?php


Class Model_Player Extends Model_Base {
    protected static $TableName = 'x_Players';
    protected static $PrimaryKey = 'p_Id';


    // Найдём игрока по форумному имени
    public static function GetPlayerByUsername($username) {
        $PlayersArray = Model_Player::GetList("p_ActivatedLogin>0 and p_Login=:username", 3, array(':username'=> self::ClearLogin($username)));
        return array_shift($PlayersArray); // return first Player in array, or NULL if array is empty or is not an array
    }


    // Вернём реквизит Игрока
    public function __get($name) {
        $value = parent::__get($name);
	    if ($name == 'p_Avatar' && empty($value)) {
	        $value = file_get_contents('images/noavatar.png');
	    }
        elseif ($name == 'p_Photo' && empty($value)) {
	        $value = file_get_contents('images/nophoto.png');
	    }

        return $value;
    }


    // список игроков со встречами
    public static function GetPlayersListWithMeeting() {
	    $query = "select distinct
				    p_Id, p_Name
				from 
				    x_Players  p
				    join
				    x_Meetings m on m.m_WinnerPlayerId = p.p_Id or 
				                    m.m_Winner2PlayerId = p.p_Id or 
				                    m.m_LoserPlayerId = p.p_Id or 
				                    m.m_Loser2PlayerId = p.p_Id
				order by
				    p_Name";
	    $Players = Model_Base::ExecArraySQL($query);

	    $PlayerModels = array();
        $ModelClassName = get_called_class();
        foreach ($Players as $PlayerRow) 
            $PlayerModels[] = new $ModelClassName(null, $PlayerRow); //id не передаём, т.к. он есть в $PlayerRow

        return $PlayerModels;
    }	//	GetPlayersListWithMeeting()


    // список игроков по организатору турниров
    public static function GetPlayersByTourOrg() {
	    $query = "select distinct
				    p_Id, p_Name
				from 
				    x_Players  p
				    join
				    x_Meetings m on m.m_WinnerPlayerId = p.p_Id or 
				                    m.m_Winner2PlayerId = p.p_Id or 
				                    m.m_LoserPlayerId = p.p_Id or 
				                    m.m_Loser2PlayerId = p.p_Id
				order by
				    p_Name";
	    $Players = Model_Base::ExecArraySQL($query);

	    $PlayerModels = array();
        $ModelClassName = get_called_class();
        foreach ($Players as $PlayerRow) 
            $PlayerModels[] = new $ModelClassName(null, $PlayerRow); //id не передаём, т.к. он есть в $PlayerRow

        return $PlayerModels;
    }	//	GetPlayersListWithMeeting()
    
    
    // Список игроков с днями рождения
    public static function GetNearestPlayersBirthday($FutureDays=0, $Today='2014-08-06') {

	    $query = "select 
                        p_Id,
                        p_Name, 
                        p_Birthdate, 
                        nextbirthday,
                        datediff(nextbirthday,:Today1) DaysToBirthday
                    from
                        (
                        select 
                            p_Id,
                            p_Name, 
                            p_Birthdate, 
                            p_Birthdate + INTERVAL (YEAR(:Today2) - YEAR(p_Birthdate))+case when substr(p_Birthdate,6)<substr(:Today3,6) then 1 else 0 end YEAR AS nextbirthday
                        from 
                            x_Players 
                        ) bd
                    where
                        datediff(nextbirthday,:Today4) between 0 and :FutureDays
                    order by
                        DaysToBirthday";
 
        $params = array(
                        ':Today1'     => $Today,
                        ':Today2'     => $Today,
                        ':Today3'     => $Today,
                        ':Today4'     => $Today,
                        ':FutureDays'=> $FutureDays
                    );
//die($query);
	    $sqlres = self::ExecArraySQLWithParams($query, $params);
        $PlayerModelsList = array();
        $ModelClassName = get_called_class();
        foreach ($sqlres as $sqlrow) {
            $model = new $ModelClassName(null, $sqlrow); //id не передаём, т.к. он гарантировано есть в $sqlrow
            $PlayerModelsList[] = $model;
        }

	    return $PlayerModelsList;
    }
        

    // Получим игроков по ролям
    public static function GetPlayersWithRoles($Roles) {
        return Model_Player::GetList("p_Roles & $Roles = $Roles order by p_Name");
    }
        
        
    // Вернём список соперников игрока 
    public function GetPlayerOpponents() {
        
        $p_Id = $this->Data[static::$PrimaryKey];
        
	    $query = "select 
                        p_Id, p_Name
                    from 
                        (
                            select m_LoserPlayerId OpponentId from x_Meetings where :pId1 in (m_WinnerPlayerId, m_Winner2PlayerId)
                            union
                            select m_Loser2PlayerId from x_Meetings where :pId2 in (m_WinnerPlayerId, m_Winner2PlayerId)
                            union
                            select m_WinnerPlayerId from x_Meetings where :pId3 in (m_LoserPlayerId, m_Loser2PlayerId)
                            union
                            select m_Winner2PlayerId from x_Meetings where :pId4 in (m_LoserPlayerId, m_Loser2PlayerId)
                        ) opponents
                        join
                        x_Players on p_Id = OpponentId
                    order by
                        p_Name";
                        
        $params = array(
                        ':pId1'     => $p_Id,
                        ':pId2'     => $p_Id,
                        ':pId3'     => $p_Id,
                        ':pId4'     => $p_Id
                    );

	    $sqlres = self::ExecArraySQLWithParams($query, $params);
        $OpponentModelsList = array();
        $ModelClassName = get_called_class();
        foreach ($sqlres as $PlayerRow) 
            $OpponentModelsList[] = new $ModelClassName(null, $PlayerRow); //id не передаём, т.к. он есть в $PlayerRow

	    return $OpponentModelsList;
        
    }
    
    
	// Список лет, когда игрок играл
	public function getPlayerActiveYears() {
	    $p_Id = $this->p_Id;
	    
	    $query = "select 
                      YEAR(t_DateTime) Year,
                      count(distinct t_Id) TourCount,
                      sum(case when gp_Place = 1 then 1 else 0 end) FirstPlaces,
                      sum(case when gp_Place = 2 then 1 else 0 end) SecondPlaces,
                      sum(case when gp_Place = 3 then 1 else 0 end) ThirdPlaces,
                      sum(m.Meetings) Meetings, 
                      sum(m.Wins) Wins
                    from 
                      x_GroupPlayer 
                      join
                      x_TourGroups		on 	g_Id = gp_GroupId
                      join
                      x_Tours    		on 	t_Id = g_TourId
                      join
                      (
                        select 
                            m_GroupId, 
                            sum(case when m_WinnerPlayerId = :pId or m_Winner2PlayerId = :pId then 1 else 0 end) Wins, 
                            count(1) Meetings 
                        from
                            x_Meetings 
                        where
                            m_WinnerPlayerId = :pId or
                            m_Winner2PlayerId = :pId or
                            m_LoserPlayerId = :pId or
                            m_Loser2PlayerId = :pId 
                        group by m_GroupId
                      ) m on m_GroupId = g_Id                        
                    where 
                      gp_PlayerId = :pId
                    Group by
                      YEAR(t_DateTime)
                    order by 
                      Year";
// die($query);
        $params = array(
                        ':pId'     => $p_Id);
/*
        $params = array(
                        ':pId1'     => $p_Id,
                        ':pId2'     => $p_Id,
                        ':pId3'     => $p_Id,
                        ':pId4'     => $p_Id,
                        ':pId5'     => $p_Id,
                        ':pId6'     => $p_Id,
                        ':pId7'     => $p_Id
                    );
*/
	    $years = self::ExecArraySQLWithParams($query, $params);

	    // set Year as array key
	    foreach($years as $key=>$year) {
	        $years[$year['Year']] = $year;
	        unset($years[$key]);
	    
	    }

		return $years;
	}
    
    
	// Список турниров игрока
	public function getPlayerTournaments($Year=-1) {
	    $query = "
                select 
                  t_Id	    	,
                  t_TourTypeId 	,	
                  ttype_Name	,
                  t_DateTime	,
--                  t_Site		,
                  c_Name        ,
                  t_Name		,
                  t_Coefficient	,
                  t_URL			,	
				  gp_Place		,
				  (select max(gp_Place) from x_GroupPlayer gp2 where gp2.gp_GroupId = gp.gp_GroupId) PlayersNum,
                  (select 
                        sum(case when m_VideoURL>'' then 1 else null end) 
                    from 
                        x_Meetings 
                    where
                        m_TourId = t_Id and 
						(
							m_LoserPlayerId = gp_PlayerId or 
							m_WinnerPlayerId = gp_PlayerId or 
							m_Winner2PlayerId = gp_PlayerId or 
							m_Loser2PlayerId = gp_PlayerId
						)
					)   VideoAmount
                from 
                  x_GroupPlayer gp
                  join
                  x_TourGroups	g	on 	g_Id = gp_GroupId
                  join
                  x_Tours		t	on 	t_Id = g_TourId
                  join 
                  x_TournamentTypes on ttype_Id = t_TourTypeId
                  join
                  x_Courts      c    on c_Id = t_CourtId
                where 
                  not gp_Place is null and
                  gp_PlayerId = :pId and 
                  (:year=-1 or :year=YEAR(t_DateTime))
				order by 
                  t_DateTime";
 //die($query);
	   // $tours = $this->ExecArraySQL($query);
        $params = array(
                        ':year'     => $Year,
                        ':pId'     => $this->Data[static::$PrimaryKey]
                    );
/*
        $params = array(
                        ':year1'     => $Year,
                        ':year2'     => $Year,
                        ':pId'     => $this->Data[static::$PrimaryKey]
                    );
*/
	    $tours = self::ExecArraySQLWithParams($query, $params);
	                                       

		return $tours;
	}


    // информация о форумном пользователе
    public function GetForumUserInfo() {
        $FormUserInfo = null;


        if ( $this->p_ActivatedLogin > 0 ) {
            $login = $this->p_Login;
		    $ForumUsers =  Model_Base::ExecArraySQLWithParams("select * from phpbb1_users where username_clean = :login", array('login' => $login), 'forum');

    		if (count($ForumUsers)==1)
    		    $FormUserInfo = $ForumUsers[0];
        }
        
	    return $FormUserInfo;
    }


    // Вернём историю рейтинга для одного игрока
    public function GetRatingHistory($from=null, $to=null) { 

        if (get_class($from)!='DateTime')  $from = new datetime('1901-01-01');
        if (get_class($to)!='DateTime')    $to = new datetime('2100-01-01');
        $to->setTime(23, 59, 59);

        $params = array(
                        ':pId'  => $this->Data[static::$PrimaryKey],
                        ':from' => $from->format('Y-m-d'),
                        ':to'   => $to->format('Y-m-d H:i:s')
                    );

        $RatingHistory = Model_PlayerRateHistory::GetList("pr_PlayerId = :pId and
                                            				    pr_Date between :from and :to
                                            				order by
                                            				    pr_Date", 
                                            				'*', 
                                            				$params
                                            			);
	    return $RatingHistory;
    }

    
    // Получим рейтинг на дату
    public function GetRate($ActualDate = null) {
        if (empty($ActualDate))
            $ActualDate = new Model_DateTime();
            
        return Model_PlayerRateHistory::GetPlayerRate($this->p_Id, $ActualDate); 
    }


    // Получим разряд на дату
    public function GetRank($ActualDate = null) {
        if (empty($ActualDate))
            $ActualDate = new Model_DateTime();
        
        if (is_a($ActualDate,'DateTime'))
            $ActualDate = $ActualDate->format('Y-m-d');

        $query = "SELECT 
                    	*
                    FROM
                        x_PlayerRank pr
                        JOIN x_Ranks r ON r.r_Id = pr.pr_RankId
                    WHERE 
                    	pr.pr_PlayerId = :pId
                        AND :ActualDate BETWEEN pr_DateFrom AND IFNULL(pr_DateTo,'2100-01-01')
                    ORDER BY
                    	pr_RankId DESC
                    LIMIT 
                    	1";

        $params = array(
                        ':ActualDate'   => $ActualDate,
                        ':pId'          => $this->Data[static::$PrimaryKey]
                    );


	    $ranks = self::ExecArraySQLWithParams($query, $params);
// echo '<pre>'.json_encode($ranks, JSON_PRETTY_PRINT).'</pre>';
        $rank = array_shift($ranks);                              

		return $rank;
    }   //  function GetRank(...)


	private static function ClearLogin($dirtyLogin){
		return strtolower($dirtyLogin);
	}


    public static function correctKeyboard($string) {
        $rus = array(
                        "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
                        "ф","ы","в","а","п","р","о","л","д","ж","э",
                        "я","ч","с","м","и","т","ь","б","ю"
                        );
        $lat = array(
                        "q","w","e","r","t","y","u","i","o","p","[","]",
                        "a","s","d","f","g","h","j","k","l",";","'",
                        "z","x","c","v","b","n","m",",","."
                        );
        return str_ireplace($lat, $rus, $string);
    } 

}

?>