<?php

/**
 * @package PHPDoctor\Tests
 */
class TestStandardDoclet extends UnitTestCase
{
	
    var $results;
    
	function testStandardDoclet() {
        $this->UnitTestCase('Standard doclet tests');
        ob_start();
        passthru(PHP.' phpdoc.php tests/php5-test-standard.ini');
        $this->results = ob_get_contents();
        ob_end_clean();
    }
    
    function readFile($filename) {
        return file_get_contents('testdocs/'.$filename);
    }
	
	function testEscapeHTMLInVariable() {
		$results = $this->readFile('phpdoctor/tests/data/aclass.html');
		$this->assertTrue(strpos($results, '<strong>$varContainingHTMLToEscape</strong> = \'&lt;strong&gt;Escape me&lt;/strong&gt;\'</code>'));
	}
	
	function testExtendingClassWithSameNameInDifferentNamespace() {
		$results = $this->readFile('phpdoctor/tests/filelevel/duplicateclass.html');
		$this->assertTrue(strpos($results, '<p class="signature">public  class <strong>duplicateClass</strong><br>extends <a href="../../../phpdoctor/tests/mynamespace/duplicateclass.html">duplicateClass</a>'));
	}
	
	function testInterfaceImplementsLink() {
	    $results = $this->readFile('phpdoctor/tests/data/aclass.html');
		$this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/data/aninterface.html">anInterface</a>'));
		
	    $results = $this->readFile('phpdoctor/tests/filelevel/implementanexternalinterface.html');
		$this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/mynamespace/aninterface.html">PHPDoctor\Tests\MyNamespace\anInterface</a>'));
		
	}

}

?>
