<?php


    class Test_Model_PlayerRank extends UnitTestCase {
    
        private $test_pId;
        private $test_pName;
        private $FirstActiveYear;
    
        function setUp() {
            // @unlink('../temp/test.log');
            date_default_timezone_set('Europe/Moscow');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        function test_getPlayerRanks() {
			// получим разряды у игрока, у которого их нет
            $RanksArray = Model_PlayerRank::getPlayerRanks(5); //p_Id=5 - it's me - разрядов нет
            $this->assertIsA($RanksArray, 'array', 'result of getPlayerRanks($PlayerId=5) is array [%s]');
            $this->assertTrue(count($RanksArray)==0, 'rank array is empty for me [%s]');
			
			// Проверить , что получили название массив моделей Model_PlayerRank
            $p_Id = Model_Base::ExecScalarSQL("select pr_PlayerId from x_PlayerRank order by pr_DateFrom desc limit 1");
            $RanksArray = Model_PlayerRank::getPlayerRanks($p_Id);
            $this->assertIsA($RanksArray, 'array', 'result of getPlayerRanks($PlayerId=5) is array [%s]');
            $this->assertTrue(count($RanksArray)>0, 'rank array is empty for me [%s]');
			
			$r = array_shift($RanksArray);
            $this->assertIsA($r, 'Model_PlayerRank', 'getPlayerRanks() return array of Model_PlayerRank [%s]');
			
        }

    }
?>

