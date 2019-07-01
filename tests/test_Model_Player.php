<?php


    class Test_Model_Player extends UnitTestCase {
    
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
        
        function test_CreateAndSaveTestPlayer() {
            $newName = 'test ('.Date('d.M.Y H:i:s').')';
            
            $model = new Model_Player();
            $this->assertIsA($model, 'Model_Player');
            $model->p_Name = $newName;
            
            $model->Save();
            
            $this->test_pId = $model->p_Id;
            $this->test_pName = $model->p_Name;
        }
        
        function test_GetOneAndChangeOne() {
            // getOne
            $model = Model_Player::GetOne($this->test_pId);
            $this->assertIsA($model, 'Model_Player');
            $this->assertIdentical($model->p_Name, $this->test_pName);

            //changeOne
            $this->test_pName = $this->test_pName . '\'_Changed\'';
            $newBd = new DateTime('1979-09-13');

            $model->p_Name = $this->test_pName;
            $model->p_Birthdate = $newBd;
            $model->p_Photo = file_get_contents('images/nophoto.png');

            $this->signal('Trace', "changed p_Name=".$model->p_Name);
            $this->signal('Trace', "new Birthdate=".$newBd->format('d.m.Y H:i:s'));
            $this->signal('Trace', "changed p_Birthdate=".$model->p_Birthdate->format('d.m.Y H:i:s'));

            $model->Save();
           
            $this->signal('Trace', "saved p_Name=".$model->p_Name);
            $this->signal('Trace', "typeOf p_Birthdate=".gettype($model->p_Birthdate));
            $this->signal('Trace', "classOf p_Birthdate=".get_class($model->p_Birthdate));
            $this->signal('Trace', "saved p_Birthdate=".$model->p_Birthdate->format('d.m.Y H:i:s'));

           //getChanged 
            $model2 = Model_Player::GetOne($this->test_pId);
            $this->signal('Trace', "readed p_Name=".$model2->p_Name);
            $this->signal('Trace', "readed p_Birthdate=".$model2->p_Birthdate->format('d.m.Y H:i:s'));
            $this->assertIdentical($model2->p_Birthdate, $model->p_Birthdate);
            $this->assertIdentical($model2->p_Name, $model->p_Name);
            $this->assertIdentical($model2->p_Photo, $model->p_Photo,'p_Photo test [%s]');
        }

        function test_GetList() {
            $modelsArray = Model_Player::GetList("p_Id<=5 or p_Id=:pId order by p_Id desc", array('p_Name','p_Birthdate','p_City'),array(':pId'=>$this->test_pId));
//throw new Exception();            
            $this->assertIsA($modelsArray, 'array');
            $this->assertTrue(count($modelsArray)>1);
            $this->assertEqual($modelsArray[0]->p_Name, $this->test_pName);
        }

        function test_DeletePlayer() {
            $model = Model_Player::GetOne($this->test_pId);
//$this->Fail();
//$this->Fail();
            $this->assertIsA($model, 'Model_Player');
            
            $result = $model->Delete();
            $this->assertTrue($result);
            $this->assertNull($model->p_Id);

            // Проверим, что игрока больше нет
            $model2 = Model_Player::GetOne($this->test_pId);
            $this->assertNull($model2);
        }
        
        function test_GetPlayerByUsername() {
            $pModel = Model_Player::GetPlayerByUsername('smirnoww');
            $this->assertIsA($pModel,'Model_Player', 'Model_Player Instance have returned by login "smirnoww" [%s]');
            $pModel = Model_Player::GetPlayerByUsername('SmIrnoWW');
            $this->signal('trace', 'Player '.$pModel->p_Name.' have returned by login "SmIrnoWW"');
            $this->assertIsA($pModel,'Model_Player', 'Model_Player Instance have returned by login "SmIrnoWW" [%s]');
            $pModel = Model_Player::GetPlayerByUsername('smirnoww-test');
            $this->assertNull($pModel, 'Null have returned by login "smirnoww-test" [%s]');
        }

        function test_GetNoPhotoFrom_p_Photo() {
            $models = Model_Player::GetList('p_Photo is null');
            $this->assertIsA($models, 'array', 'Got array of players without photo');
            $this->assertTrue(count($models) > 0, 'Array have one player at least');
            $firstModel = array_shift($models);
            $this->assertEqual($firstModel->p_Photo, file_get_contents('images/nophoto.png'), 'Photo equal images/nophoto.png [%s]');
            // TODO: print $firstModel->p_Photo
        }

        function test_GetNoAvatarFrom_p_Avatar() {
            $models = Model_Player::GetList('p_Avatar is null');
            $this->assertIsA($models, 'array', 'Got array of players without avatar');
            $this->assertTrue(count($models) > 0, 'Array have one player at least');
            $firstModel = array_shift($models);
            $this->assertEqual($firstModel->p_Avatar, file_get_contents('images/noavatar.png'), 'Photo equal images/nophoto.png [%s]');
            // TODO: print $firstModel->p_Avatar
        }
        
        function test_GetPlayersListWithMeeting() {
            $playerWM = Model_Player::GetPlayersListWithMeeting();
            $this->assertIsA($playerWM, 'array', 'Got array of players with meetings');
            $this->assertTrue(count($playerWM) >= 2, 'Array have two element at least');
            $firstPlayer = array_shift($playerWM);
            $this->assertIsA($firstPlayer, 'Model_Player', 'First element of Array is a Model_Player');
            $secondPlayer = array_shift($playerWM);
            $this->assertIsA($secondPlayer, 'Model_Player', 'Second element of Array is a Model_Player');
            
        }

        function test_GetNearestPlayersBirthday() {
            $playersBD = Model_Player::GetNearestPlayersBirthday(30,'2015-09-01');
            $this->assertIsA($playersBD, 'array', 'Got array of players who was born in September');
            $this->assertTrue(count($playersBD) >= 1, 'Array have one (me) element at least');
            $firstPlayer = array_shift($playersBD);
            $this->assertIsA($firstPlayer, 'Model_Player', 'First element of Array is a Model_Player');

            $this->signal('Trace', $firstPlayer->p_Name." was born at ".$firstPlayer->p_Birthdate->format('d.m.Y').". ".$firstPlayer->DaysToBirthday." days to Birthdate from 01.09.2015");
            $this->assertTrue($firstPlayer->DaysToBirthday >= 0, 'Days to birthdate is positive');

            $this->assertIsA($firstPlayer->p_Birthdate, 'DateTime', 'Birthdate represented as DateTime instance [%s]');
        }
        
        function test_GetPlayerOpponents() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            $opponents = $TestPlayer->GetPlayerOpponents();
            $this->assertIsA($opponents, 'array', 'Got array of opponents of '.$TestPlayer->p_Name.' [%s]');
            $this->assertTrue(count($opponents) >= 1, 'Array have one element at least. Total '.count($opponents).' opponents [%s]');
            $opponentPlayer = array_shift($opponents);
            $this->assertIsA($opponentPlayer, 'Model_Player', 'First element of Array is a Model_Player [%s]');
            $this->signal('Trace', "First opponent of ".$TestPlayer->p_Name." is ".$opponentPlayer->p_Name);
        }
        
        function test_getPlayerActiveYears() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            $years = $TestPlayer->getPlayerActiveYears();
            $this->assertIsA($years, 'array', 'Got array of active years of '.$TestPlayer->p_Name.' [%s]');
            $this->assertTrue(count($years) >= 1, 'Array have one element at least. Total '.count($years).' years [%s]');
            $this->signal('Trace','<pre>'.json_encode($years, 128)."</pre> is active years of ".$TestPlayer->p_Name);
    
            $firstYear = array_shift($years);
            
            $this->FirstActiveYear = $firstYear['Year']; //for future use
            
            $this->assertIsA($firstYear, 'array', 'First element of Array is a array of data [%s]');
        }

        function test_getPlayerTournaments() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            $ToursOfYear = $TestPlayer->getPlayerTournaments($this->FirstActiveYear);
            $this->assertIsA($ToursOfYear, 'array', 'Got array of tours of '.$TestPlayer->p_Name.' in '.$this->FirstActiveYear.' [%s]');
            $this->assertTrue(count($ToursOfYear) >= 1, 'Array have one element at least. Total '.count($ToursOfYear).' tours  in '.$this->FirstActiveYear.' [%s]');
            $this->signal('Trace','<pre>'.json_encode($ToursOfYear, 128)."</pre> is tours of ".$TestPlayer->p_Name." in ".$this->FirstActiveYear);

            $firstTour = array_shift($ToursOfYear);
            $this->assertIsA($firstTour, 'array', 'First element of Array is a array of data [%s]');

            $AllTours = $TestPlayer->getPlayerTournaments();
            $this->assertIsA($AllTours, 'array', 'Got array of tours of '.$TestPlayer->p_Name.' in all years [%s]');
            $this->assertTrue(count($AllTours) >= 1, 'Array have one element at least. Total '.count($AllTours).' tours  in all years [%s]');
            $this->signal('Trace','<pre>'.json_encode($AllTours, 128)."</pre> is tours of ".$TestPlayer->p_Name." in all years");

            $firstTour = array_shift($AllTours);
            $this->assertIsA($firstTour, 'array', 'First element of Array is a array of data [%s]');
        }

        function test_GetForumUserInfo() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            $ForumUserInfo = $TestPlayer->GetForumUserInfo();
            $this->assertIsA($ForumUserInfo, 'array', 'Got array of forum user data of '.$TestPlayer->p_Name.' forum username = '.$ForumUserInfo['username'].' [%s]');
            $this->assertTrue(count($ForumUserInfo) >= 1, 'Array have one element at least. Total '.count($ForumUserInfo).' years [%s]');
            $this->signal('Trace','<pre>'.json_encode($ForumUserInfo, 128)."</pre> is forum user data of ".$TestPlayer->p_Name);

            // check Player without forum user
            $PlayersWOForumUser = Model_Player::GetList('p_ActivatedLogin = 0');
            $this->assertTrue(count($PlayersWOForumUser) >= 1, 'Got one Player without forum user at least. Total '.count($PlayersWOForumUser).' [%s]');
            $firstPlayerWOForumUser = array_shift($PlayersWOForumUser);
            $ForumUserInfo2 = $firstPlayerWOForumUser->GetForumUserInfo();
            $this->assertNull($ForumUserInfo2, 'GetForumUserInfo() return null if player haven\'t forum user[%s]');
        }

        function test_GetRatingHistory() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            $rh = $TestPlayer->GetRatingHistory();
            $this->assertIsA($rh, 'array', 'Got array with rating history of '.$TestPlayer->p_Name.' [%s]');
            $this->assertTrue(count($rh) >= 1, 'Array have one element at least. Total '.count($rh).' records in rating history [%s]');
            $lastRHrecord = array_pop($rh);
            $this->assertIsA($lastRHrecord, 'Model_PlayerRateHistory', 'last value of array is Model_PlayerRateHistory [%s]');
            $this->assertIsA($lastRHrecord->pr_Date, 'DateTime', '[last value]->pr_Date is DateTime and = '.$lastRHrecord->pr_Date->format('d.m.Y').' [%s]');
            $this->assertTrue(is_numeric($lastRHrecord->pr_PlayerId), '[last value]->pr_PlayerId is int and = '.$lastRHrecord->pr_PlayerId.' [%s]');
            $this->assertTrue(is_numeric($lastRHrecord->pr_Rate), '[last value]->pr_Rate is numeric and = '.$lastRHrecord->pr_Rate.' [%s]');
            
        }
        
        function test_GetRate() {
            $TestPlayer = Model_Player::GetOne(5); //5 - it's me
            
            // last rate
            $Rate = Model_Base::ExecScalarSQLWithParams("select pr_Rate from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date desc limit 1", array(':pId'=>5));
            $RateModel = $TestPlayer->GetRate();
            $this->assertIsA($RateModel, 'Model_PlayerRateHistory', 'result of GetPlayerRate() is Model_PlayerRateHistory [%s]');
            $this->assertEqual($Rate, $RateModel->pr_Rate, 'Last rate for Player(5) =  '.$RateModel->pr_Rate.' [%s]');

            // first rate
            $Rate = Model_Base::ExecScalarSQLWithParams("select pr_Rate from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date limit 1", array(':pId'=>5));
            $RateDate = Model_Base::ExecScalarSQLWithParams("select pr_Date from x_PlayerRateHistory where pr_PlayerId = :pId order by pr_Date limit 1", array(':pId'=>5));
            $RateDateTimeModel = Model_DateTime::createFromFormat('Y-m-d', $RateDate);
            
            $RateModel = $TestPlayer->GetRate($RateDateTimeModel); 
            
            $this->assertIsA($RateModel, 'Model_PlayerRateHistory', 'result of GetPlayerRate() is Model_PlayerRateHistory [%s]');
            $this->assertEqual($Rate, $RateModel->pr_Rate, 'Last rate for Player(5) =  '.$RateModel->pr_Rate.' [%s]');


            // Absent rate
            $RateModel = $TestPlayer->GetRate(Model_DateTime::createFromFormat('Y-m-d','2010-01-01')); 
            $this->assertNull($RateModel, 'result of GetPlayerRate() is Model_PlayerRateHistory [%s]');

        }
    }

?>

