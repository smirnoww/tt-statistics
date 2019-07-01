<?php
Class Model_Rating Extends Model_Base {

	protected static $TableName = 'x_Ratings';
	protected static $PrimaryKey = 'r_Id';

    // возвращает список турниров, которые влияют на рейтинг
    public function RatingToursList( $RatingId, $TourCount = 20) {
        $sqlres = self::ExecArraySQLWithParams(
                            "CALL `x_ToursForCalc`(:RatingId, :TourCount);",
                            array(
                                    ':RatingId' =>  $RatingId,
                                    ':TourCount'=>  $TourCount
                                )
                        );
        $res = array();
        
        foreach ($sqlres as $row) {
            $m = Model_Tour::GetOne(null, $row);
            $d = DateTime::createFromFormat('Y-m-d H:i:s', $row['t_DateTime']);
            $res[$d->format('Y-m-d')][] = $m;
        }
            
        return $res;
    }
    
    
    // Рассчитать рейтинг
    public function CalculateRate($Date) {
        return self::ExecArraySQLWithParams(
                            "CALL `x_RatingCalc`(:RatingId, :Date);",
                            array(
                                    ':RatingId' =>  $this->r_Id,
                                    ':Date'     =>  $Date
                                )
                        );
    }
    
}


?>