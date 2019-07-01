<?php


    class Test_Model_Meeting extends UnitTestCase {
    
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
        
        function test_getMeetings() {
            $MeetingsArray = Model_Meeting::getMeetings(-1,-1, 5); //p_Id=5 - it's me
            $this->assertIsA($MeetingsArray, 'array', 'result of getMeetings($FirstPlayerId=5) is array [%s]');
            $this->assertTrue(count($MeetingsArray)>=1, 'array with meetings contain one meeting at least. Total '.count($MeetingsArray).' meetings [%s]');
            $firstMeeting = array_shift($MeetingsArray);
            $this->assertIsA($firstMeeting, 'Model_Meeting', 'first element of array is Model_Meeting [%s]');
        }

        function test_getMeetingsForCalc() {
            $MeetingsArray = Model_Meeting::getMeetingsForCalc(321, 5); //t_Id=243 - it's NY2014 tour,p_Id=5 - it's me
            $this->assertIsA($MeetingsArray, 'array', 'result of getMeetings($FirstPlayerId=5) is array [%s]');
            $this->assertTrue(count($MeetingsArray)>=1, 'array with meetings contain one meeting at least. Total '.count($MeetingsArray).' meetings [%s]');
            $firstMeeting = array_shift($MeetingsArray);
            $this->assertIsA($firstMeeting, 'array', 'first element of array is array of data [%s]');
        }

        function test_getLoserPlayerRank() {
            $Meeting = Model_Meeting::getMeeting(78527); //m_Id=78527 - Штыкова проиграла Питиримову 28.04.2018, имея разряд КМС с 01.04.2018
            $this->assertIsA($Meeting, 'Model_Meeting', 'result of getMeeting(78527) is Model_Meeting [%s]');

            $Loser = $Meeting->m_LoserPlayerId('Model_Player');
            $this->assertIsA($Loser, 'Model_Player', "result of m_LoserPlayerId('Model_Player') is Model_Player [%s]");

            $Tour = $Meeting->m_TourId('Model_Tour');
            $this->assertIsA($Tour, 'Model_Tour', "result of m_TourId('Model_Tour') is Model_Tour [%s]");

            $Rank = $Loser->GetRank($Tour->t_DateTime);
            $this->assertIsA($Rank, 'array', 'GetRank($Meeting->m_DateTime) is array [%s]');
        }

        function test_getVictoryOverRankedPlayers() {
            $Meetings = Model_Meeting::getVictoryOverRankedPlayers(112); //112 - Питиримов выиграл у барашкина много раз
            $this->assertIsA($Meetings, 'array', 'result of getVictoryOverRankedPlayers(Питиримов) is array [%s]');
            $this->assertTrue(count($Meetings)>=5, 'result of getVictoryOverRankedPlayers(Питиримов) more or equal 5 (Питиримов выиграл у Барашкина дофигилион раз) [%s]');
        }
    }
?>

