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
	    if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $results = $this->readFile('phpdoctor/tests/filelevel/duplicateclass.html');
            $this->assertTrue(strpos($results, '<p class="signature">public  class <strong>duplicateClass</strong><br>extends <a href="../../../phpdoctor/tests/mynamespace/duplicateclass.html">duplicateClass</a>'));
        }
	}
	
	function testInterfaceImplementsLink() {
	    $results = $this->readFile('phpdoctor/tests/data/aclass.html');
		$this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/data/aninterface.html">anInterface</a>'));
		
		if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $results = $this->readFile('phpdoctor/tests/filelevel/implementanexternalinterface.html');
            $this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/mynamespace/aninterface.html">PHPDoctor\Tests\MyNamespace\anInterface</a>'));
        }
		
	}
	
	function testShortDescriptionWithAFullStop() {
		$results = $this->readFile('phpdoctor/tests/data/package-summary.html');
		$this->assertTrue(strpos($results, '<td class="description">This is aClass that implements anInterface. </td>'));
	}
	
	function testInheritance() {
		$results = $this->readFile('phpdoctor/tests/filelevel/inherittestchild.html');
		$this->assertTrue(strpos($results, '<p>Test inheriting of class doccomments</p>'));
		$this->assertTrue(strpos($results, '<p class="description">Parent field comment is: Test inheriting of field doccomments</p>'));
		$this->assertTrue(strpos($results, '<p class="description">Parent method comment is: Test inheriting of method doccomments</p>'));
		$this->assertTrue(strpos($results, '<p>Parent field comment is: Test inheriting of field doccomments</p>'));
		$this->assertTrue(strpos($results, '<p>Parent method comment is: Test inheriting of method doccomments</p>'));
	}
	
}

?>
