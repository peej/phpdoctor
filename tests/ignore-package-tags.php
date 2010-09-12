<?php

/**
 * @package PHPDoctor\Tests
 */
class TestIgnorePackageTags extends UnitTestCase
{
	
    var $results;
    
	function testIgnorePackageTags() {
        $this->UnitTestCase('Test ignore package tags option');
		ob_start();
		passthru(PHP.' phpdoc.php tests/php5-test-ignore-package-tags.ini');
		$this->results = ob_get_contents();
		ob_end_clean();
    	
		#echo $this->results; die;
	}
	
	function testClass() {
		$this->assertTrue(strpos($this->results, 'public class PHPDoctor\\Tests\\aClass'));
	}
	
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
	
}

?>
