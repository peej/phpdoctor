<?php

/**
 * @package PHPDoctor\Tests
 */
class TestUseClassPathAsPackage extends UnitTestCase
{
	
    var $results;
    
	function testUseClassPathAsPackage() {
        $this->UnitTestCase('Test use class path as package option');
		ob_start();
		passthru(PHP.' phpdoc.php tests/php5-use-class-path-as-package.ini');
		$this->results = ob_get_contents();
		ob_end_clean();
    	
		echo $this->results; die;
	}
	
	function testClass() {
		$this->assertTrue(strpos($this->results, 'public class PHPDoctor\\Tests\\Package\\Subpackage\\testClass'));
	}
	
	/*
	function testInterface() {
		$this->assertTrue(strpos($this->results, 'public interface PHPDoctor\\Tests\\anInterface'));
	}
	
	function testMemberVariable() {
		$this->assertTrue(strpos($this->results, 'public mixed PHPDoctor\\Tests\\$aVar'));
	}
	
	function testMemberFunction() {
		$this->assertTrue(strpos($this->results, 'public void PHPDoctor\\Tests\\aMethod()'));
	}
	
	function testConstant() {
	    $this->assertTrue(strpos($this->results, 'final int PHPDoctor\\Tests\\THREE'));
	}
    
	function testFunction() {
	    $this->assertTrue(strpos($this->results, 'public void PHPDoctor\\Tests\\aFunction()'));
	}
	
	function testVariable() {
	    $this->assertTrue(strpos($this->results, 'mixed PHPDoctor\\Tests\\$one'));
	}
	*/
}

?>
