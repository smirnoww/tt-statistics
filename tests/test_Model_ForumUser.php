<?php

    class Test_Model_ForumUser extends UnitTestCase {
    
        function setUp() {
            // @unlink('../temp/test.log');
            date_default_timezone_set('Europe/Moscow');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        function test_GetList() {
            $fu = Model_ForumUser::GetList(); 
            $this->assertIsA($fu, 'array', 'result of GetList() is array [%s]');
            $this->assertTrue(count($fu)>=4, 'array contain four forum users at least. Total '.count($fu).' users [%s]');
            $firstuser = array_shift($fu);
            $seconduser = array_shift($fu);
            $this->assertIsA($seconduser, 'Model_ForumUser', 'first element of array is Model_ForumUser. username='.$seconduser->username.' [%s]');
        }

    }
?>

