<?php

/**
 * @package PHPDoctor\Tests
 */
class TestStandardDoclet extends UnitTestCase
{
	
	function testStandardDoclet() {
        $this->UnitTestCase('Standard doclet tests');
        exec('php phpdoc.php tests/php5-test-standard.ini');
    }
    
    function readFile($filename) {
        return file_get_contents('testdocs/'.$filename);
    }
	
	function testEscapeHTMLInVariable()
	{
		$results = $this->readFile('phpdoctor/tests/data/aclass.html');
		$this->assertTrue(strpos($results, '<code class="signature">public  mixed <strong>$varContainingHTMLToEscape</strong> = \'&lt;strong&gt;Escape me&lt;/strong&gt;\'</code>'));
	}

}

?>
