<?php
    class Test_Model_PlayerRateHistory extends UnitTestCase {
/*    
        private $test_pId;
        private $test_pName;
        private $FirstActiveYear;
*/  
        function setUp() {
            // @unlink('../temp/test.log');
            date_default_timezone_set('Europe/Moscow');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        function test_GetPlayerRate() {
            // last rate
            $Rate = Model_Base::ExecScalarSQLWithParams("select pr_Rate from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date desc limit 1", array(':pId'=>5));
            $RateModel = Model_PlayerRateHistory::GetPlayerRate(5); 
            $this->assertIsA($RateModel, 'Model_PlayerRateHistory', 'result of GetPlayerRate() is Model_PlayerRateHistory [%s]');
            $this->assertEqual($Rate, $RateModel->pr_Rate, 'Last rate for Player(5) =  '.$RateModel->pr_Rate.' [%s]');

            // first rate
            $Rate = Model_Base::ExecScalarSQLWithParams("select pr_Rate from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date limit 1", array(':pId'=>5));
            $RateDate = Model_Base::ExecScalarSQLWithParams("select pr_Date from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date limit 1", array(':pId'=>5));
            $RateDateTimeModel = Model_DateTime::createFromFormat('Y-m-d', $RateDate);
            
            $RateModel = Model_PlayerRateHistory::GetPlayerRate(5, $RateDateTimeModel); 
            
            $this->assertIsA($RateModel, 'Model_PlayerRateHistory', "result of GetPlayerRate($RateDate) is Model_PlayerRateHistory [%s]");
            $this->assertEqual($Rate, $RateModel->pr_Rate, "First rate for Player(5) on $RateDate =  {$RateModel->pr_Rate} [%s]");
            
            // Rate by Model_DateTime
            $RateByDateTimeModel = Model_PlayerRateHistory::GetPlayerRate(5, $RateDateTimeModel);

            $this->assertIsA($RateDateTimeModel, 'Model_DateTime', "result of CastDBValueToModelFormat($RateDate) is Model_DateTime [%s]");
            $this->assertIsA($RateByDateTimeModel, 'Model_PlayerRateHistory', "result of GetPlayerRate($RateDateTimeModel) is Model_PlayerRateHistory [%s]");
            $this->assertEqual($Rate, $RateByDateTimeModel, "Rate by DateTime for Player(5) on $RateDateTimeModel = $RateByDateTimeModel [%s]");
            
            // Absent rate
            $RateModel = Model_PlayerRateHistory::GetPlayerRate(5, Model_DateTime::createFromFormat('Y-m-d','2010-01-01')); 
            $this->assertNull($RateModel, 'result of GetPlayerRate() is Model_PlayerRateHistory [%s]');

        }

    }
?>

