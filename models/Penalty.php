<?php
Class Model_Penalty Extends Model_Base {

	protected static $TableName = 'x_Penalty';
	protected static $PrimaryKey = 'pnlt_Id';

    // список взысканий для вывода списка с дополнительными данными
    public static function GetPenaltiesList($p_Id = -1, $Expired = -1) {
	    $query = "select 
                  pnlt.pnlt_Id, 
                  pnlt.pnlt_PlayerId, 
                  pnlt.pnlt_Date, 
                  pnlt.pnlt_ExpDate, 
                  pnlt.pnlt_Description, 
                  pnlt.pnlt_PenaltyTypeId, 
                  pt.pt_Name, 
                  pt.pt_Color, 
                  p_Name,
                  case when pnlt.pnlt_ExpDate<=CURDATE() then 1 else 0 end Expired
                from 
                  x_Penalty pnlt
                  join 
                  x_PenaltyTypes pt on pt_Id = pnlt_PenaltyTypeId
                  join 
                  x_Players p on p_Id = pnlt_PlayerId
                where
                  (p_Id = $p_Id or $p_Id = -1) and
                  (
                    (pnlt.pnlt_ExpDate<=CURDATE() and $Expired=1) or
                    (pnlt.pnlt_ExpDate>CURDATE()  and $Expired=0) or
                    $Expired=-1
                  )
                order by
                  pnlt_Date,
                  p_Name ";
	    $Penalties = Model_Base::ExecArraySQL($query);

	    $PenaltiesModels = array();
        $ModelClassName = get_called_class();
        foreach ($Penalties as $PenaltyRow) 
            $PenaltiesModels[] = new $ModelClassName(null, $PenaltyRow); //id не передаём, т.к. он есть в $PenaltyRow

        return $PenaltiesModels;
    }

}


?>