<?php


    class Test_Model_Base_DatabaseFunctions extends UnitTestCase {
    
		private $PlayerId;
		private $PlayerName;
		
        function setUp() {
            // @unlink('../temp/test.log');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
    
    
        function test_GetInstance() {
    //  expect exception of type [Exception] with message [DB not configured!] 
    //        $nonamePDOInstance = Model_Base::getDBInstance('');
    //        $this->assertIsA($nonamePDOInstance, 'PDO');
    
            $statPDOInstance = Model_Base::getDBInstance('stat');
            $this->assertIsA($statPDOInstance, 'PDO');
            
            $forumPDOInstance = Model_Base::getDBInstance('forum');
            $this->assertIsA($forumPDOInstance, 'PDO');
        }
        
		// select existing record
        function test_ExecuteSQLWithParams_SelectExistingRecord() {
            $p_stm = Model_Base::ExecuteSQLWithParams("select p_Name from x_Players where p_Id = :pId", array(':pId'=>5));
            $this->assertIsA($p_stm,'PDOStatement','Result of exec SQL with params is PDOStatement [%s]');
            
            $Result = $p_stm->fetchAll(PDO::FETCH_ASSOC);
            $this->assertEqual(count($Result),1,'ExecuteSQLWithParams have returned one player by Id [%s]');
		}

            
        // добавим игрока        
        function test_ExecuteSQLWithParams_AddNewPlayer() {
            $this->PlayerName = 'test ('.Date('d.M.Y H:i:s').')';

            $this->PlayerId = Model_Base::ExecuteSQLWithParams("replace into x_Players (p_Name) values (:pName)", array(':pName'=>$this->PlayerName));
            $this->assertTrue(is_numeric($this->PlayerId));
		}
		
		
        // изменим игрока
        function test_ExecuteSQLWithParams_ChangeExistingPlayer() {
            $this->PlayerName .= '+changed';
            $ChangedPlayerId = Model_Base::ExecuteSQLWithParams("replace into x_Players (p_Id, p_Name) values (:pId, :pName)", array(':pId'=>$this->PlayerId, ':pName'=>$this->PlayerName));
            $this->assertTrue(is_numeric($ChangedPlayerId));
            $this->assertEqual($ChangedPlayerId, $this->PlayerId);
        }

		// Запросим изменения
        function test_ExecArraySQL_GetChangedPlayer() {
            $pArray = Model_Base::ExecArraySQL("select p_Id, p_Name from x_Players where p_Id = ".$this->PlayerId);
            $this->assertIsA($pArray, 'array');
            $this->assertIsA($pArray[0], 'array');
            $this->assertNotNull($pArray[0]['p_Id']);
            $this->assertEqual($pArray[0]['p_Name'], $this->PlayerName);
		}
      
		// Запросим изменения через запрос с параметром
        function test_ExecArraySQLWithParams_GetChangedPlayer() {
			$this->assertEqual(gettype($this->PlayerId), 'integer');
			
            $pArray = Model_Base::ExecArraySQLWithParams("select p_Id, p_Name from x_Players where p_Id = :pId", array(':pId'=>$this->PlayerId) );
            $this->assertIsA($pArray, 'array');
            $this->assertIsA($pArray[0], 'array');
            $this->assertNotNull($pArray[0]['p_Id']);
            $this->assertEqual($pArray[0]['p_Name'], $this->PlayerName);
		}
      
		// Запросим имя игрока
		function test_ExecScalarSQL() {
            $pName = Model_Base::ExecScalarSQL("select p_Name from x_Players where p_Id = ".$this->PlayerId);
            $this->assertEqual($pName, $this->PlayerName);
        }  
        
		// Запросим имя игрока через запрос с параметром
        function test_ExecScalarSQLWithParams() {
            $pName = Model_Base::ExecScalarSQLWithParams("select p_Name from x_Players where p_Id = :pId", array(':pId'=>$this->PlayerId));
            $this->assertEqual($pName, $this->PlayerName);
        }
		
        // удалим игрока
        function test_ExecuteSQLWithParams_DeletePlayer() {
            $stm = Model_Base::ExecuteSQLWithParams("delete from x_Players where p_Id = :pId", array(':pId'=>$this->PlayerId));
            $this->assertEqual($stm->rowCount(),1);
		}
    }
?>