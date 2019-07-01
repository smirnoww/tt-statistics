<?php
Class Model_PlayerRateHistory Extends Model_Base {

	protected static $TableName = 'x_PlayerRateHistory';
	protected static $PrimaryKey = 'pr_Id';

	public function __toString() {
		return $this->Data['pr_Rate'];
	}
	
    // Получим рейтинг игрока на дату (вечер) $ActualDate - экземлпяр класса DateTime
    public static function GetPlayerRate($p_Id, $ActualDate = null) {

        if (empty($ActualDate))
            $ActualDate = new Model_DateTime();

        if (!is_a($ActualDate,'DateTime'))            
            throw new Exception('Model_PlayerRateHistory::GetPlayerRate expected DateTime object as parameter. Got '.gettype($ActualDate).(is_object($ActualDate) ? '/'.get_class($ActualDate) : '').' instead');

        $ActualDate = Model_PlayerRateHistory::CastValueToDBFormat('pr_Date', $ActualDate);
        $RateArray = Model_PlayerRateHistory::GetList(
                                                        "pr_PlayerId = :PlayerId and pr_Date <= :ActualDate order by pr_Date desc limit 1", 
                                                        '*', 
                                                        array(
                                                            ':PlayerId'=> $p_Id,
                                                            ':ActualDate'=> $ActualDate->format('Y-m-d')
                                                        )
                                                    );
        return array_shift($RateArray); // return first value in array, or NULL if array is empty or is not an array
    }

    // Получим рейтинг игрока на утро указанной даты (по сути- на предыдущий день) $ActualDate - экземлпяр класса DateTime
    public static function GetPlayerRateBefore($p_Id, $ActualDate) {

      if (!is_a($ActualDate,'DateTime'))            
        throw new Exception('Model_PlayerRateHistory::GetPlayerRateBefore expected DateTime object as parameter. Got '.gettype($ActualDate).'/'.get_class($ActualDate).' instead');
        
      $ActualDate = Model_PlayerRateHistory::CastValueToDBFormat('pr_Date', $ActualDate);
        $RateArray = Model_PlayerRateHistory::GetList(
                                                        "pr_PlayerId = :PlayerId and pr_Date < :ActualDate order by pr_Date desc limit 1", 
                                                        '*', 
                                                        array(
                                                            ':PlayerId'=> $p_Id,
                                                            ':ActualDate'=> $ActualDate->format('Y-m-d')
                                                        )
                                                    );
        return array_shift($RateArray); // return first value in array, or NULL if array is empty or is not an array
    }   //  GetPlayerRateBefore($p_Id, $ActualDate)
	
}


?>