<?php

Class Model_DateTime extends Datetime { 

    private $defaultFormat = "d.m.Y H:i";
	private $isNull = false;

    public static function createFromFormat ( $format , $time , $timezone = null) {
        if (!isset($timezone))
            $timezone = new DateTimeZone( date_default_timezone_get() );
            
        $time = str_replace('-00-00', '-01-01', $time);
/*
            var_dump($time);
            die();
*/
        $tmp =  parent::createFromFormat ( $format , $time , $timezone);
			//echo "<br><br>$format - $time<br>";

		$res = new Model_DateTime('0001-01-01 00:00:00');

		if ($tmp) {
			$res->setDate($tmp->format('Y'), $tmp->format('m'), $tmp->format('d'));
			$res->setTime($tmp->format('H'), $tmp->format('i'), $tmp->format('s'));

			if ($tmp->format('Y-m-d H:i') == '0000-01-01 00:00') 
				$res->isNull = true;
		}
		else {
			$res->isNull = true;
		}
        return $res;
    }
    
    
    public function setDefaultFormat($format) {
        $this->defaultFormat = $format;
    }
    
    
    public function format ( $format ) {
        if ($this->isNull)
            return "";
            //return "-";
        else
            return parent::format($format);
    }

    public function DateIsSet() {
        return !$this->isNull;
    }
    
    
    public function __toString() {
        $res = $this->format($this->defaultFormat);
            
        return $res;
    }
    
}

?>