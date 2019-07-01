<?php

    class Test_Model_Role extends UnitTestCase {
    
        private $test_r;

        function setUp() {
            // @unlink('../temp/test.log');
            date_default_timezone_set('Europe/Moscow');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        function test_GetCountGetList() {
            $RoleCount = Model_Role::GetCount(); 
            $this->assertEqual($RoleCount, 4 , 'We have 4 instance of Model_Role [%s]');
            
            $Roles = Model_Role::GetList(); 
            $this->assertIsA($Roles, 'array', 'GetList have returned Array [%s]');
            $this->assertEqual($RoleCount, count($Roles) , 'Count of GetList equal GetCount result [%s]');

            $this->test_r = array_shift($Roles);
            $this->assertTrue(is_numeric($this->test_r->r_Id), 'r_Id of first role is_numeric [%s]');
        }

        function test_GetOne() {
          
            $R = Model_Role::GetOne(1);
            $this->assertIsA($R, 'Model_Role', 'GetOne(0) have returned Model_Role [%s]');

            $this->assertIsA($R->r_Id, 'Int', 'GetOne(0)->r_Id is int='.$R->r_Id.' [%s]');
            $this->assertIsA($R->r_Name, 'string', 'GetOne(0)->r_Name is string='.$R->r_Name.' [%s]');
        
        }
    }
?>

