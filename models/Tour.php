<?php
Class Model_Tour Extends Model_Base {

	protected static $TableName = 'x_Tours';
	protected static $PrimaryKey = 't_Id';

    // Возвращает TRUE, если регистрация на турнир открыта
    public function RegistrationAvailable() {
        return 
                $this->t_DateTime->DateIsSet() 
                && ($this->t_DateTime->format('U') >= (new DateTime())->format('U'));
    }
    

    // Id формулы расчёта рейтинга
    // 1 - ФНТР 
    // 2 - СЛЛНТ - отнимается в 1.5 меньше чем прибавляется. Применяется с 01.08.2013
    public function GetFormulaId() {
        return $this->t_DateTime < new DateTime('2013-08-01')? 1 : 2;
    }
    

    // Список лет, в которых проходили туриниры
    public static function GetYears() {
        $sqlres = self::ExecArraySQL("select Distinct YEAR(t_DateTime) Year from ".static::$TableName." order by t_DateTime");
        $Years = array();
        foreach ($sqlres as $sqlrow) {
            $Years[$sqlrow['Year']] = $sqlrow['Year'];
        }

        return $Years;
    }


    // Вовращает дату последнего прошедшего турнира, в котором определены призёры
    // эту дату считаем окончанием последней игровой недели для статистики
    public static function GetLastTourDate() {
        $LastTourDate = self::ExecScalarSQL('select 
												MAX(t_DateTime)  t_DateTime
											FROM
												x_Tours
												JOIN
												x_TourGroups on g_TourId = t_Id
												JOIN
												x_GroupPlayer on gp_GroupId = g_Id and gp_Place >=1 and gp_Place<=3
											WHERE
												t_DateTime < NOW()');
		return self::CastDBValueToModelFormat('t_DateTime', $LastTourDate);
    }
    
    // Список призёров за последнюю игровую неделю
    public static function GetPrizewinners() {
        $LastTourDate = self::GetLastTourDate();

        $sqlres = self::ExecArraySQLWithParams("select 
            										t_Id, 
            										DATE_FORMAT(t_DateTime, '%d.%m.%Y') t_DateTime, 
            										t_Name,
            										g_Id, 
            										g_Name,
            										gp_Id,
            										gp_Place,
            										gp_PlayerId,
            										gp_Note,
            										p_Name
            									FROM
            										x_Tours
            										JOIN
            										x_TourGroups on g_TourId = t_Id
            										JOIN
            										x_GroupPlayer on gp_GroupId = g_Id and gp_Place >=1 and gp_Place<=3
            										JOIN
            										x_Players on p_Id = gp_PlayerId
            									WHERE
            										t_DateTime < NOW()
            										and t_DateTime > (CURDATE() - INTERVAL 1 WEEK)
            									ORDER BY
            										t_DateTime DESC,
            										g_Name,
            										gp_Place
            									LIMIT 100",
            									Array());
        
        // Если за последнюю неделю не было турниров, то покажем призёров одного последнего турнира
		if (!count($sqlres))
            $sqlres = self::ExecArraySQLWithParams("select 
                										t_Id, 
                										DATE_FORMAT(t_DateTime, '%d.%m.%Y') t_DateTime, 
                										t_Name,
                										g_Id, 
                										g_Name,
                										gp_Id,
                										gp_Place,
                										gp_PlayerId,
                										gp_Note,
                										p_Name
                									FROM
                										(SELECT * 
                                                         FROM x_Tours 
                                                         WHERE
                                                            t_DateTime < NOW()
                                                         ORDER BY t_DateTime DESC
                                                         LIMIT 1
                                                        ) t
                										JOIN
                										x_TourGroups on g_TourId = t_Id
                										JOIN
                										x_GroupPlayer on gp_GroupId = g_Id and gp_Place >=1 and gp_Place<=3
                										JOIN
                										x_Players on p_Id = gp_PlayerId
                									ORDER BY
                										t_DateTime DESC,
                										g_Name,
                										gp_Place",
            								    	Array());
            

		$pw = array();
		// скомпонуем в иерархию Турнир -> Группа -> Игрок = массив всей инфы
		foreach ($sqlres as $sqlrow) 
			$pw[$sqlrow['t_DateTime'].' - '.$sqlrow['t_Name']][$sqlrow['g_Name']][$sqlrow['gp_Id']] = $sqlrow;
		
		return $pw;
	}	//	GetPrizewinners()
    
    // возвращает список рейтингов, на которые влияет турнир
    public function TourRatingsList() {
        $sqlres = self::ExecArraySQLWithParams(
                            "SELECT tr_Id, tr_TourId, tr_RatingId from x_TourRatings where tr_TourId=:tr_TourId",
                            array(':tr_TourId'=>$this->t_Id)
                        );
        $res = array();
        
        foreach ($sqlres as $row) {
            $rm = Model_Rating::GetOne($row['tr_RatingId']);
            $res[$row['tr_RatingId']] = $rm;
        }
            
        return $res;
    }   //  TourRatingsList() 

    // сохраняет список рейтингов, на которые влияет турнир
    public function SaveTourRatingsList($InflRatings) {
        self::ExecuteSQLWithParams(
                            "delete from x_TourRatings where tr_TourId=:tr_TourId", 
                            array(':tr_TourId'=>$this->t_Id)
                        );
        foreach ($InflRatings as $r_Id)
            self::ExecuteSQLWithParams(
                                "insert into x_TourRatings (tr_TourId, tr_RatingId) values (:tr_TourId, :tr_RatingId)", 
                                array(':tr_TourId'=>$this->t_Id, ':tr_RatingId'=>$r_Id)
                            );
    }


}


?>