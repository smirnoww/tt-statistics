<?php
Class Model_CallForTour Extends Model_Base {

	protected static $TableName = 'x_CallForTour';
	protected static $PrimaryKey = 'cft_Id';


	public static function GetTourCalls ($TourId, $OrderBy = 'cft_CallDateTime') {
	   
	    $calls =  Model_Base::ExecArraySQLWithParams(
                        "select 
                            cft_Id,
                            cft_TourId,                     
                            cft_CallDateTime,
                            cft_PlayerId,
                            cft_PlayerRating,
                            cft_AssistToPlayerId,
                            cft_Comment,
                            p_Name PlayerName,
                            GroupId
                        from
                            x_CallForTour cft
                            join
                            x_Players p on cft_PlayerId = p_Id
                            left join
                            (
                                select
                                    gp_PlayerId,
                                    GROUP_CONCAT(gp_GroupId SEPARATOR ',') GroupId
                                    /*GROUP_CONCAT(CONCAT('{\"GroupId\":',gp_GroupId,',\"GroupColor\":\"',g_Color,'\"}') SEPARATOR ',') GroupId*/
                                from 
                                    x_TourGroups g
                                    join
                                    x_GroupPlayer gp on gp.gp_GroupId = g.g_Id
                                where
                                    g_TourId = :TourId1
                                group by
                                    gp_PlayerId 
                            ) gp on gp_PlayerId = cft_PlayerId 
                        where 
                            cft_TourId = :TourId2
                        order by
                            $OrderBy",
	                   array(
	                        ':TourId1'=>$TourId,
	                        ':TourId2'=>$TourId
	                        )
	            );

        $ModelsArray = array();
        foreach ($calls as $key => $call) {
            if (!empty($call['GroupId']))
                $call['GroupId'] = explode(',',$call['GroupId']);
            
            $call['ActualRate'] = Model_PlayerRateHistory::GetPlayerRate($call['cft_PlayerId']);
            $ModelsArray[] = new Model_CallForTour(null,$call);
        }
        return $ModelsArray;
	    
	}
}


?>