<?php
// git test 2
    class Test_Model_Tour extends UnitTestCase {
    
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
        
        function test_GetYears() {
            $YearsArray = Model_Tour::GetYears(); 
            $this->assertIsA($YearsArray, 'array', 'result of GetYears() is array [%s]');
            $this->assertTrue(count($YearsArray)>=4, 'array with Years contain four years at least (from 2014). Total '.count($YearsArray).' years [%s]');
            $firstYear = array_shift($YearsArray);
            $this->assertTrue(is_numeric($firstYear), 'first element of array is_numeric - year [%s]');
        }

        function test_GetLastTourDate() {
            $LastTourDate = Model_Tour::GetLastTourDate(); 
            
            $this->assertIsA($LastTourDate, 'Model_DateTime', 'result of GetLastTourDate() is Model_DateTime [%s]');
            $this->assertTrue($LastTourDate<=(new DateTime()), "LastTourDate = $LastTourDate was in the Past (less or equal NOW) [%s]");
        }

    }
?>

