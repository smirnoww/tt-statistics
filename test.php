<?php
    //test git-spaced
    error_reporting (E_ALL);
    ini_set('display_errors', 1);

    include('includes/startup.php');

    header('Content-Type: text/html; charset=utf-8');

    require_once(dirname(__FILE__) . '/../simpletest/simpletest.php'); 


    if (isset($_GET['detail'])) {
        require_once('tests/ShowPasses.php'); 
        SimpleTest::prefer(new ShowPasses());
        echo "<script type=\"text/javascript\">
                <!--
                    function toggle_visibility(id) {
                       var e = document.getElementById(id);
                       if(e.style.display == 'block')
                          e.style.display = 'none';
                       else
                          e.style.display = 'block';
                    }
                //-->
                </script>
            ";
    }
    
    require_once(dirname(__FILE__) . '/../simpletest/autorun.php'); 

    echo "<div>php version: ".phpversion()."</div>\n";
/* ======================================================================= */

    class AllTests extends TestSuite {
        function __construct() {
            parent::__construct();
            
            // Если тесты переданы явно, то подключаем то, что передано
            if (isset($_GET['cases'])) {
                $cases = json_decode($_GET['cases']);

                foreach($cases as $testCase)
                    $this->addFile("tests/test_$testCase.php");
                    
            }
            else { // Если тесты не указаны, то загружаем все из папки tests/test_*.php
                $tests = scandir('tests');
                
                //echo json_encode($tests)."<br><br>";
                
                foreach ($tests as $testfile) {
                    if (preg_match ("/^test_\w\w*.php/" , $testfile) )
                        $this->addFile("tests/$testfile");
                }
            }
        }   
    }

?>
