<?php


Class Model_Meeting Extends Model_Base {
    protected static $TableName = 'x_Meetings';
    protected static $PrimaryKey = 'm_Id';

    // Преобразуем данные модели к формату БД
    protected static function CastValueToDBFormat($name, $value) {
        if ($name == 'm_AffectRating') {
            // echo "before: $name = $value<br>";
            if (strtolower($value)=="on" || strtolower($value)==1 )
                $value = 1;
            else
                $value = 0;
            // echo "after: $name = $value<br>";
        }

        return parent::CastValueToDBFormat($name, $value);
    }


    // сохраним модель
    public function save() {
        
        if (!key_exists('m_AffectRating', $this->Data))
            $this->Data['m_AffectRating'] = 0;
            
        if (trim($this->m_WinnerScore) == '')
            $this->m_WinnerScore=0;

        if (trim($this->m_LoserScore) == '')
            $this->m_LoserScore=0;
        
		// поменяем местами, если у победителя меньше очков
		if ($this->m_WinnerScore < $this->m_LoserScore) {
			$tmpPlayerId	= $this->m_WinnerPlayerId;
			$tmp2PlayerId	= $this->m_Winner2PlayerId;
			$tmpWinnerScore	= $this->m_WinnerScore;
			
			$this->m_WinnerPlayerId     = $this->m_LoserPlayerId;
			$this->m_Winner2PlayerId	= $this->m_Loser2PlayerId;
			$this->m_WinnerScore		= $this->m_LoserScore;
			
			$this->m_LoserPlayerId	= $tmpPlayerId;
			$this->m_Loser2PlayerId	= $tmp2PlayerId;
			$this->m_LoserScore		= $tmpWinnerScore;
		}

		// Если дата встречи не указана явно, возьмём из турнира
		if (! $this->m_DateTime->DateIsSet()) {
			$t = $this->m_TourId('Model_Tour');
			$this->m_DateTime = ''.$t->t_DateTime;
		}

        parent::save();
    }	//	function save()


	// Количество побед над игроками с разрядами за последний год
	public static function getVictoryOverRankedPlayersCount($p_Id) {
        $query = "SELECT
                        pr.pr_RankId r_Id,
                        r.r_Name,
                        r.r_Description,
                        COUNT(1) VictoryCount,
                        COUNT(DISTINCT m.m_LoserPlayerId) LoserCount
                    FROM
                    	x_Meetings m 
                        JOIN x_PlayerRank pr ON pr.pr_PlayerId = m.m_LoserPlayerId
                        JOIN x_Players pWinner ON pWinner.p_Id = m.m_WinnerPlayerId
                        JOIN x_Players pLoser ON pLoser.p_Id = m.m_LoserPlayerId AND pLoser.p_Sex = pWinner.p_Sex
                        JOIN x_Tours t ON t.t_Id = m.m_TourId AND t.t_DateTime BETWEEN pr.pr_DateFrom AND IFNULL(pr.pr_DateTo,'2100-01-01')
                        JOIN x_Ranks r ON r.r_Id = pr.pr_RankId
                    WHERE 
                    	m.m_WinnerPlayerId = :WinnerPlayerId
                        AND m.m_AffectRating
                        AND t.t_DateTime BETWEEN DATE_SUB(curdate(), INTERVAL 1 YEAR) AND CURDATE()
					GROUP BY                      
                    	pr.pr_RankId,
                        r.r_Name
                    ORDER BY
                        pr.pr_RankId";
                        
        $params = array( 
                        ':WinnerPlayerId'   => $p_Id 
                        );
                        
	    $RanksList = self::ExecArraySQLWithParams($query, $params);

	    return $RanksList;
	}	//	function getVictoryOverRankedPlayersCount

	
	// список побед над игроками с разрядами
	public static function getVictoryOverRankedPlayers($p_Id, $from=null, $to=null){
	    if (!$from)
	        $from = '2000-01-01';
	    if (!$to)
	        $to = '2100-01-01';
	        
        $query = "SELECT 
                    	m.m_Id
                    FROM
                    	x_Meetings m 
                        JOIN x_PlayerRank pr ON pr.pr_PlayerId = m.m_LoserPlayerId
                        JOIN x_Players pWinner ON pWinner.p_Id = m.m_WinnerPlayerId
                        JOIN x_Players pLoser ON pLoser.p_Id = m.m_LoserPlayerId AND pLoser.p_Sex = pWinner.p_Sex
                        JOIN x_Tours t ON t.t_Id = m.m_TourId AND t.t_DateTime BETWEEN pr.pr_DateFrom AND IFNULL(pr.pr_DateTo,'2100-01-01')
                    WHERE 
                    	m.m_WinnerPlayerId = :WinnerPlayerId
                        AND m.m_AffectRating
                        AND t.t_DateTime BETWEEN :from AND :to
                    ORDER BY
                        t.t_DateTime DESC,
                        m.m_Id";
                        
        $params = array( 
                        ':WinnerPlayerId'   => $p_Id ,
                        ':from'             => $from ,
                        ':to'               => $to 
                        );
                        
	    $sqlres = self::ExecArraySQLWithParams($query, $params);

        $MeetingsList = array();

        foreach ($sqlres as $sqlRow) 
            $MeetingsList[$sqlRow['m_Id']] = self::getMeeting($sqlRow['m_Id']);

	    return $MeetingsList;
	}	//	function getVictoryOverRankedPlayers


	// Возвращает встречу или NULL
	public static function getMeeting($m_Id) {
	    $query = "select 
				    m.m_Id, 
				    m.m_TourId, 
				    m.m_GroupId, 
				    m.m_WinnerPlayerId,
				    m.m_Winner2PlayerId,
				    m.m_LoserPlayerId,
				    m.m_Loser2PlayerId,
				    IFNULL(m_TourId,-1) m_TourId_notnull,

					w.p_Name WinnerName,
					w2.p_Name Winner2Name,

                    m.m_WinnerScore,
                    m.m_LoserScore,
                    
                    case 
                        when m_WinnerScore>0 or m_LoserScore>0 
                        then concat(m_WinnerScore, ':', m_LoserScore)
                        else 'W:L'
                    end Score,
					  
					l.p_Name    LoserName,
					l2.p_Name   Loser2Name,
					
					m.m_AffectRating,
					m.m_VideoURL,
					m.m_Note,
					DATE_FORMAT(IFNULL(m_DateTime,t_DateTime),'%d.%m.%Y') MeetingDate,
					t.t_Id,
					IFNULL(t.t_Name,'Квалификационные встречи') t_Name,
					c.c_Name,
					t.t_URL,
					t.t_DateTime,
					tt.ttype_Name
				from 
				    x_Meetings m 
					join x_Players w on w.p_Id = m.m_WinnerPlayerId 
					join x_Players l on l.p_Id = m.m_LoserPlayerId 
					left join x_Players w2 on w2.p_Id = m.m_Winner2PlayerId 
					left join x_Players l2 on l2.p_Id = m.m_Loser2PlayerId 
				    left join x_Tours t on t.t_Id = m.m_TourId
				    left join x_Courts c on c_Id = t_CourtId
				    left join x_TournamentTypes tt on tt.ttype_Id = t.t_TourTypeId
                where 
                   m_Id = :m_Id
		        order by
                    IFNULL(t.t_DateTime, m.m_DateTime),
                    m_Id";
// die($query);
//echo "<br>$query<hr>";	    
        $params = array(
                        ':m_Id'           => $m_Id
                    );

	    $sqlres = self::ExecArraySQLWithParams($query, $params);

        
        $firstRow = array_shift($sqlres); // получим первую строку результата или NULL
        
        if (is_array($firstRow)) {
            $ModelClassName = get_called_class();
            $m = new $ModelClassName(null, $firstRow); //id не передаём, т.к. он есть в $firstRow
	    }
        else
            $m = null;
        
	    return $m; 
	}    // function getMeeting($m_Id)
	
	
	// Список встреч
	public static function getMeetings($TourId=-1, $GroupId=-1, $FirstPlayerId=-1, $SecondPlayerId=-1) {
//echo "<hr>call getMeetings($TourId, $FirstPlayerId, $SecondPlayerId)";	    
	    
	    if ($TourId<0 && $GroupId<0 && $FirstPlayerId<0 && $SecondPlayerId<0)
	        throw new Exception('Model_Meeting::getMeetings(...) need one criteria for meetings selectig at least');
	        
	    $query = "select 
				    m.m_Id
				from 
				    x_Meetings m 
				    left join x_Tours t ON t.t_Id = m.m_TourId
                where 
                   :TourId in (m_TourId, -1) and
                   :GroupId in (m_GroupId, -1) and
                   (
                    (:FirstPlayerId1 in (m_WinnerPlayerId, m_Winner2PlayerId, -1) and :SecondPlayerId1 in (m_LoserPlayerId, m_Loser2PlayerId, -1)) or
                    (:SecondPlayerId2 in (m_WinnerPlayerId, m_Winner2PlayerId, -1) and :FirstPlayerId2 in (m_LoserPlayerId, m_Loser2PlayerId, -1)) 
                   )
		        order by
                    IFNULL(m.m_DateTime,t.t_DateTime) DESC,
                    m.m_Id";

        $params = array(
                        ':TourId'           => $TourId,
                        ':GroupId'          => $GroupId,
                        ':FirstPlayerId1'   => $FirstPlayerId,
                        ':FirstPlayerId2'   => $FirstPlayerId,
                        ':SecondPlayerId1'  => $SecondPlayerId,
                        ':SecondPlayerId2'  => $SecondPlayerId
                    );
                    
// echo "query-$query<br>\n";
// echo "params:<br>\n <pre>".json_encode($params, JSON_PRETTY_PRINT)."</pre>";
// die();
	    $sqlres = self::ExecArraySQLWithParams($query, $params);

        $MeetingsList = array();

        foreach ($sqlres as $sqlRow) 
            $MeetingsList[$sqlRow['m_Id']] = self::getMeeting($sqlRow['m_Id']);

	    return $MeetingsList;
	}

	// Список встреч
	public static function getMeetingsForCalc( $TourId, $PlayerId ) {
		$MeetingsQuery = "select 
							  case 
							  when m_WinnerPlayerId = :PlayerId1
							  then 
								(select pr_Rate from x_PlayerRateHistory where pr_PlayerId = m_LoserPlayerId and pr_Date<left(m_DateTime,10) order by pr_Date desc limit 1)
							  else
								(select pr_Rate from x_PlayerRateHistory where pr_PlayerId = m_WinnerPlayerId and pr_Date<left(m_DateTime,10) order by pr_Date desc limit 1)
							  end OpponentRate,

							  case 
								when m_WinnerPlayerId = :PlayerId2
								then pl.p_Name
								else pw.p_Name
							  end OpponentName, 

							  case 
								when m_WinnerPlayerId = :PlayerId3 
								then true
								else false
							  end IWon
							from 
							  x_Meetings 
							  left join 
							  x_Players pl on pl.p_Id = m_LoserPlayerId
							  left join 
							  x_Players pw on pw.p_Id = m_WinnerPlayerId 
							where 
							  m_TourId = :TourId and 
							  m_AffectRating = 1 and 
							  (m_WinnerPlayerId = :PlayerId4 or m_LoserPlayerId = :PlayerId5)";
		
        $params = array(
                        ':TourId'       => $TourId,
                        ':PlayerId1'    => $PlayerId,
                        ':PlayerId2'    => $PlayerId,
                        ':PlayerId3'    => $PlayerId,
                        ':PlayerId4'    => $PlayerId,
                        ':PlayerId5'    => $PlayerId
                    );

	    $MeetingsArray = self::ExecArraySQLWithParams($MeetingsQuery, $params);
		
		return $MeetingsArray;
	}

}

?>