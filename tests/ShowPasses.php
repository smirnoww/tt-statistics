<?php
    class ShowPasses extends HtmlReporter {
        
        private $CaseMethodCount    = 0;
        private $CasePassCount      = 0;
        private $CaseFailCount      = 0;
        private $CaseExceptionCount = 0;
        private $CaseErrorCount     = 0;
        private $CaseSignalCount    = 0;

        private $MethodPassCount      = 0;
        private $MethodFailCount      = 0;
        private $MethodExceptionCount = 0;
        private $MethodErrorCount     = 0;
        private $MethodSignalCount    = 0;

        private $CurrentCase        = '';

        function paintCaseStart($test_name) {
            parent::paintCaseStart($test_name);

            $this->CaseMethodCount    = 0;
            $this->CasePassCount      = 0;
            $this->CaseFailCount      = 0;
            $this->CaseExceptionCount = 0;
            $this->CaseErrorCount     = 0;
            $this->CaseSignalCount    = 0;

            $this->CurrentCase = $test_name;
            print "<br><br><br>
                    <h2>-= $test_name =- <input type=\"button\" onclick=\"toggle_visibility('".$test_name."_caseDIV')\" value=\"details\"></h2> \n";
            print "\n <div id=\"".$test_name."_caseDIV\" style=\"margin-left: 2cm; display:none\">";
        }
        function paintCaseEnd($test_name) {
            parent::paintCaseEnd($test_name);
            echo "</div>";
            
            $colour = ($this->CaseFailCount + $this->CaseExceptionCount + $this->CaseErrorCount> 0 ? "red" : "green");
            
            print "<div style=\"";
            print "padding: 4px; margin-top: 1em; background-color: $colour; color: white;";
            print "\">";
            print "<strong>$test_name</strong> test case complete:\n";
            print "<strong>" . $this->CaseMethodCount    . "</strong> methods, ";
            print "<strong>" . $this->CasePassCount      . "</strong> passes, ";
            print "<strong>" . $this->CaseFailCount      . "</strong> fails, ";
            print "<strong>" . $this->CaseErrorCount     . "</strong> errors, ";
            print "<strong>" . $this->CaseExceptionCount . "</strong> exceptions and ";
            print "<strong>" . $this->CaseSignalCount    . "</strong> signals.";
            print "</div>\n\n";
        }
        function paintMethodStart($test_name) {
            parent::paintMethodStart($test_name);
            
            $this->MethodPassCount      = 0;
            $this->MethodFailCount      = 0;
            $this->MethodExceptionCount = 0;
            $this->MethodErrorCount     = 0;
            $this->MethodSignalCount    = 0;
            
            print "\n<h4>$test_name: <input type=\"button\" onclick=\"toggle_visibility('".$this->CurrentCase."_".$test_name."_methodDIV')\" value=\"details\"></h4>\n";
            print "\n<div id=\"".$this->CurrentCase."_".$test_name."_methodDIV\" style=\"margin-left: 2cm; display:none\">";
        }
        function paintMethodEnd($test_name) {
            parent::paintMethodEnd($test_name);
            $this->CaseMethodCount++;
            echo "</div>\n\n";

            $colour = ($this->MethodFailCount + $this->MethodExceptionCount + $this->MethodErrorCount> 0 ? "red" : "green");
            if ($colour == 'red') {
                print "<div style=\"";
                print "padding: 4px; margin-top: 1em; background-color: $colour; color: white;";
                print "\">";
                print "<strong>$test_name</strong> test method complete:\n";
                print "<strong>" . $this->MethodPassCount      . "</strong> passes, ";
                print "<strong>" . $this->MethodFailCount      . "</strong> fails, ";
                print "<strong>" . $this->MethodErrorCount     . "</strong> errors, ";
                print "<strong>" . $this->MethodExceptionCount . "</strong> exceptions and ";
                print "<strong>" . $this->MethodSignalCount    . "</strong> signals.";
                print "</div>\n\n";
            }
        }


        function paintPass($message) {
            parent::paintPass($message);
            $this->CasePassCount++;
            $this->MethodPassCount++;
            echo "<span class=\"pass\">Pass</span>: $message<br />\n<hr>";
        }
        function paintFail($message) {
            SimpleScorer::paintFail($message);
            $this->CaseFailCount++;
            $this->MethodFailCount++;
            print "<span class=\"fail\">Fail</span>: $message<br />\n<hr>";
        }
        function paintException($message) {
            SimpleScorer::paintException($message);
            $this->CaseExceptionCount++;
            $this->MethodErrorCount++;
            print "<span class=\"fail\">Exception</span>: $message<br />\n<hr>";
        }
        function paintError($message) {
            SimpleScorer::paintError($message);
            $this->CaseErrorCount++;
            $this->MethodErrorCount++;
            print "<span class=\"fail\">Error</span>: $message<br />\n<hr>";
        }        
        function paintSignal($type, $payload) {
            $this->CaseSignalCount++;
            $this->MethodSignalCount++;
            print("<i>$type</i>: $payload\n<br>\n");
        }
    }
?>