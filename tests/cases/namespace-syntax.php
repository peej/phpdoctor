<?php

/**
 * @package PHPDoctor\Tests
 */
class TestNamespaceSyntax extends DoctorTestCase
{
	
	function testNamespaceSyntax() {
        
		$this->DoctorTestCase('Test use of the namespace syntaxes');
		
	}
	
	function testStandardSyntax() {
	    
		$this->setIniFile('standard-namespace-syntax.ini');
		$output = $this->runPhpDoctor();
		
		$this->assertTrue(strpos($output, 'public final static int foo\bar\ZERO'));
		$this->assertTrue(strpos($output, 'public final static int foo\bar\ONE = 1'));
	}
	
	function testAltSyntax() {
	    
		$this->setIniFile('alt-namespace-syntax.ini');
		$output = $this->runPhpDoctor();
		
		$this->assertTrue(strpos($output, 'public final static int woo\yay\ZERO'));
		$this->assertTrue(strpos($output, 'public final static int woo\yay\ONE = 1'));
	}
	
}

?>
