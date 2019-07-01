<?php
Class Model_PlayerRank Extends Model_Base {

	protected static $TableName = 'x_PlayerRank';
	protected static $PrimaryKey = 'pr_Id';

	// список разрядов с наименованиями для вывода в профиле
	static function getPlayerRanks($p_Id) {
	    $query = "SELECT 
					r.r_Name,
					r.r_Description,
					pr.*,
					CASE WHEN pr.pr_DateTo IS NULL or pr.pr_DateTo >= NOW() THEN 1 ELSE 0 END Active
				FROM 
					x_PlayerRank pr
					JOIN
					x_Ranks r ON r.r_Id = pr.pr_RankId
				WHERE 
					pr.pr_PlayerId = $p_Id
				 ORDER BY 
				 	pr_DateFrom";
		
	    $Ranks = Model_Base::ExecArraySQL($query);
	    $RankModels = array();
        $ModelClassName = get_called_class();
        foreach ($Ranks as $r) 
            $RankModels[] = new $ModelClassName(null, $r); //id не передаём, т.к. он есть в $r
//die(json_encode($RankModels));
        return $RankModels;
	}

}


?>