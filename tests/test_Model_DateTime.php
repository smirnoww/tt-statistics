<?php


    class Test_Model_DateTime extends UnitTestCase {
    
		private $tmpDateTime;

        function setUp() {
            // @unlink('../temp/test.log');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
    
    
        function test_CreateFromFormat() {
            // Проверим создание нормальной даты
            $DateTimeStr = '1979-09-13';
            $tmpDateTime = Model_DateTime::createFromFormat('Y-m-d', $DateTimeStr);
            $this->assertIsA($tmpDateTime, 'Model_DateTime', "Model_DateTime created from '$DateTimeStr' [%s]");
            $this->assertEqual($tmpDateTime->format('Y'), substr($DateTimeStr,0,4), "check Year from Model_DateTime [%s]");

            // Проверим вывод даты в русском формате
            $tmpDateTime->setDefaultFormat('d.m.Y');
            $a = $tmpDateTime."";
            $this->assertIsA($a, 'string', "__toString have returned string [%s]");
            
            $pieces = explode ( '-' , $tmpDateTime );
            $pieces = array_reverse ( $pieces );
            $rusDate = implode ( '.' , $pieces );
            $this->assertEqual($a, $rusDate, "Вывод даты в русском формате, заданном по умолчанию работает. [%s]");

            // год без даты            
            $DateTimeStr = '1979-00-00';
            $tmpDateTime = Model_DateTime::createFromFormat('Y-m-d', $DateTimeStr);
            $this->assertEqual(substr($tmpDateTime,0,10), '01.01.1979', "Дата с нулевым месяцем и днём приводится к началу года [%s]");
            
            // неправильный формат
            $DateTimeStr = '1979-0g-g0';
            $tmpDateTime = Model_DateTime::createFromFormat('Y-m-d', $DateTimeStr);
            $this->assertEqual($tmpDateTime."", '', "При дате в неправильном формате возвращается пустая строка [%s]");
            $this->assertEqual($tmpDateTime->format('H:m:i'), '', "При использовании функции форматирования и дате в неправильном формате возвращается пустая строка  [%s]");

            //var_dump($tmpDateTime);
        }

        function test_IsSetForModelProperty() {
            $DateTimeStr = '1979-09-13';
            $tmpDateTime = Model_DateTime::createFromFormat('Y-m-d', $DateTimeStr);
            $this->assertTrue($tmpDateTime->DateIsSet(), "isset(Model_DateTime::createFromFormat('Y-m-d', '1979-09-13')) work fine [%s]");

            $tmpDateTime = Model_DateTime::createFromFormat('Y-m-d', 'абра кодабра');
            $this->assertFalse($tmpDateTime->DateIsSet(), "isset(Model_DateTime::createFromFormat('Y-m-d', '1979-09-13')) work fine [%s]");

        }      
    }
?>