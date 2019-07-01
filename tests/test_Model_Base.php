<?php


    Class Model_PlayerTest Extends Model_Base {
        protected static $TableName = 'x_Players';
        protected static $PrimaryKey = 'p_Id';
        
        public static function GetFieldsForLoad($infields=3) {
            return parent::GetFieldsForLoad($infields);
        }
        public static function GetFields($infields=3) {
            return parent::GetFields($infields);
        }

    }
    
    Class Model_CourtTest Extends Model_Base {
        protected static $TableName = 'x_Courts';
        protected static $PrimaryKey = 'c_Id';
        
        static function clearFields() {
            unset(static::$Fields[static::$TableName]);
        }
    
    }
    class Test_Model_Base extends UnitTestCase {
    
        private $test_pId;
        private $test_pName;
    
        function setUp() {
            // @unlink('../temp/test.log');
        }
    
        function tearDown() {
            // @unlink('../temp/test.log');
        }
        
        
        function test_CreateModelWithPreloadedDataAndReadOne() {
         //   $PlayerCountBefore = count(Model_PlayerTest::GetList());
         
            $newName = 'test ('.Date('d.M.Y H:i:s').')';
         
            $PreloadedData = array('c_Name'=>$newName);
         
            $model = new Model_CourtTest(null, $PreloadedData);
         
            $model->Save();
            
            $this->assertTrue($model->c_Id > 0, 'c_Id of created Court greate then 0 <p>[%s]</p>');
            
            $model2 = Model_CourtTest::GetOne($model->c_Id);
       
            $this->signal('saved', $model->c_Id.': '.$model->c_Name);
            $this->signal('read', $model2->c_Id.': '.$model2->c_Name);
            
            $this->assertEqual($model->c_Name, $model2->c_Name, 'c_Name of readed model equal c_Name of save model which created with preloaded data <p>[%s]</p>');
            $model2->Delete();
            // try to read deleted model
            $model3 = Model_CourtTest::GetOne($model->c_Id);
            $this->assertNull($model3, 'test model not exists (deleted from db) <p>[%s]</p>');
        }

        function test_CreateAndSaveTestPlayer() {
            $newName = 'test ('.Date('d.M.Y H:i:s').')';
            
            $model = new Model_PlayerTest();
            $this->assertIsA($model, 'Model_PlayerTest');
            $model->p_Name = $newName;
            $model->p_EMail = "test e-mail";
            
            $model->Save();
            
            $this->test_pId = $model->p_Id;
            $this->test_pName = $model->p_Name;
        }
        
        function test_GetOne() {
            $model = Model_PlayerTest::GetOne($this->test_pId);
            $this->assertIsA($model, 'Model_PlayerTest');
            $this->assertIdentical($model->p_Name, $this->test_pName);
        }

        function test_LazyLoad() {
            $model = Model_PlayerTest::GetOne($this->test_pId);
            $this->assertIsA($model, 'Model_PlayerTest');
            $this->assertIdentical($model->p_EMail, "test e-mail", 'email lazy loaded<p>[%s]</p>');
        }

        function test_GetList() {
    
            $modelsArray = Model_PlayerTest::GetList("p_Id<=5 or p_Id=:pId order by p_Id desc", array('p_Name','p_Birthdate','p_City'),array(':pId'=>$this->test_pId));
            
            $this->assertIsA($modelsArray, 'array');
            $this->assertTrue(count($modelsArray)>1);
            $this->assertEqual($modelsArray[0]->p_Name, $this->test_pName);

        }
        
        function test_GetCount() {
            $TotalPlayersCount = Model_PlayerTest::GetCount();
            $tpc = Model_Base::ExecScalarSQL('select count(1) PlayersCount from x_Players');
            $this->assertEqual($TotalPlayersCount, $tpc, "GetCount() have returned $TotalPlayersCount players equal 'select count(1) from x_Players'. <p>[%s]</p>");
            
            $per_page = 100;
            $page = ceil($TotalPlayersCount / $per_page);
            
            $offset = ($page-1)*$per_page;
            $this->signal('Trace', "per_page = $per_page <p>[%s]</p>");
            $this->signal('Trace', "page = $page <p>[%s]</p>");
            $this->signal('Trace', "offset = $offset <p>[%s]</p>");
            $params = array(
                            ':per_page'=>array(
                                                'value'=>$per_page,
                                                'type'=>PDO::PARAM_INT
                                            ) ,
                            ':offset'=>array(
                                                'value'=>$offset,
                                                'type'=>PDO::PARAM_INT
                                            )
                            );
            $PlayersCount = Model_PlayerTest::GetCount("true limit :per_page offset :offset", $params);
            $this->assertTrue($PlayersCount > 0, 'We have got one player at least on last page. <p>[%s]</p>');
            $this->assertTrue($PlayersCount <= $per_page, "We have got $PlayersCount players at high limit $per_page on last page. <p>[%s]</p>");
        }
        
        function test_DeletePlayer() {
    
            $model = Model_PlayerTest::GetOne($this->test_pId);
            $this->assertIsA($model, 'Model_PlayerTest');
            
            $result = $model->Delete();
            $this->assertTrue($result);
            $this->assertNull($model->p_Id);

            // Проверим, что игрока больше нет
            $model2 = Model_PlayerTest::GetOne($this->test_pId);
            $this->assertNull($model2);
            
        }

        function test_ArrayAccessInterface() {
			$test_p_Name = 'test name';
		
            $model = new Model_PlayerTest();
			$model['p_Name'] = $test_p_Name;
			$model->p_Birthdate = new DateTime();
            $this->assertIsA($model['p_Birthdate'], 'DateTime', 'instance of Model_PlayerTest is array and index of p_Birthdate returned DateTime instance <p>[%s]</p>');
            $this->signal('Trace','$model["p_Birthdate"] = '.$model['p_Birthdate']->format('d.m.Y'));
            $this->assertIdentical($model->p_Name, $test_p_Name, 'p_Name of Model_PlayerTest setted via arrayAccess are identical p_Name getted via class property <p>[%s]</p>');
        }


        function test_GettingModelByLink() {

            //success scenario
            $tour = Model_Tour::GetOne(536);

            $this->assertIsA($tour, 'Model_Tour', "Got a Model_Tour by id <p>[%s]</p>");
            $court = $tour->t_CourtId('Model_Court');
            $this->assertIsA($court, 'Model_Court', "Got a Model_Court by Model_Tour->t_CourtId<=".$tour->t_CourtId.">('Model_Court') <p>[%s]</p>");
            $this->signal('Trace',"Court name = ".$court->c_Name);

            //get model of wrong class name
            $this->expectException(new PatternExpectation("/Class /i"));
            $c = $tour->t_CourtId("Model_Courtttt");
            $this->signal('Trace',"!!!");
            $this->assertIsA($c, 'Model_Court');
            
            $this->expectException(new PatternExpectation("/must be an instance of/i"));
            //get model by string id
            $p = $tour->t_Name("Model_Player");
        }


        function test_GetFieldsForLoadINT() {
            $fields = Model_PlayerTest::GetFields();
//            echo "<br><br>".json_encode($fields)."<br><br>";
            $fieldsCount = count($fields); // количество полей
            
            // Проверим что при передаче числового параметра не отбираются blob поля
            $forLoad = Model_PlayerTest::GetFieldsForLoad($fieldsCount); 
            foreach ($forLoad as $field)
                $this->assertTrue((stripos($fields[$field]['Type'],'blob') === false) && (stripos($fields[$field]['Type'],'text') === false), "$field arrived from GetFieldsForLoad(INT) is not blob and is not text <p>[%s]</p>");
        }

        function test_GetFieldsForLoadStar() {
            $fields = Model_PlayerTest::GetFields();
            // Проверим что при передаче '*' не отбираются blob поля
            $forLoad = Model_PlayerTest::GetFieldsForLoad('*'); 
            foreach ($forLoad as $field)
                $this->assertTrue((stripos($fields[$field]['Type'],'blob') === false) && (stripos($fields[$field]['Type'],'text') === false), "$field arrived from GetFieldsForLoad('*') is not blob and is not text <p>[%s]</p>");
        }

        function test_GetFieldsForLoadTwoStar() {
            // Проверим что при передаче '**' отбирается больше полей (дополнительно blob поля)
            $forLoad1 = Model_PlayerTest::GetFieldsForLoad('*'); 
            $forLoad2 = Model_PlayerTest::GetFieldsForLoad('**'); 
            $this->assertTrue(count($forLoad1) < count($forLoad2), "GetFieldsForLoad('*') have returned less fields than GetFieldsForLoad('**') <p>[%s]</p>");
        }

    }

?>

