<?php
Class Model_TourGroupPlayer Extends Model_Base {

	protected static $TableName = 'x_GroupPlayer';
	protected static $PrimaryKey = 'gp_Id';

    // Возвращает массив игроков группы турнира 
    public static function GetTourGroupPlayers($GroupId, $NameFilter='') {
		$alternateFilter = Model_Player::correctKeyboard($NameFilter);
		
	    $query = "SELECT gp.*,p.p_Name 
                    FROM 
                        `x_GroupPlayer` gp 
                        join 
                        x_Players p on p_Id = gp_PlayerId 
                    WHERE 
                        gp_GroupId=:GroupId 
                        and (p.p_Name like '%$NameFilter%'
                            or p.p_Name like '%$alternateFilter%')
                    Order by 
                        IfNull(gp_Place, 9999),
                        p.p_Name";
        $params = array(
                        ':GroupId'           => $GroupId
                    );

	    $sqlres = self::ExecArraySQLWithParams($query, $params);
        $GroupPlayers = array();
        $ModelClassName = get_called_class();
        foreach ($sqlres as $sqlRow) 
            $GroupPlayers[$sqlRow['gp_Id']] = new $ModelClassName(null, $sqlRow); //id не передаём, т.к. он есть в $sqlRow

	    return $GroupPlayers;
    }
}


?>