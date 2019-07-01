<?php

    class Test_Model_Auth extends UnitTestCase {
    

        function setUp() {
            // @unlink('../temp/test.log');
            date_default_timezone_set('Europe/Moscow');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        function test_Registry_Auth() {
            $r = Registry::getInstance();
            $this->assertIsA($r['Auth'], 'Model_Auth', 'Model_Auth instance exists');
            $this->signal('trace',$r['Auth']->username. ' have logon into phpbb3 forum');
            $this->signal('trace','group_id of ' . $r['Auth']->username.' is '.$r['Auth']->group_id);
            $this->signal('trace','player of ' . $r['Auth']->username.' is '.$r['Auth']->AuthPlayer->p_Name.' (id: '.$r['Auth']->AuthPlayer->p_Id.')');
        }

    }
?>

