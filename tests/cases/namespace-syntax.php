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
	    
		if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
			$this->setIniFile('standard-namespace-syntax.ini');
			$output = $this->runPhpDoctor();
			
			$this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\foo\bar\ZERO'));
			$this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\foo\bar\ONE = 1'));
		}
	}
	
	function testAltSyntax() {
	    if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
			$this->setIniFile('alt-namespace-syntax.ini');
			$output = $this->runPhpDoctor();
			
			$this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\woo\yay\ZERO'));
			$this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\woo\yay\ONE = 1'));
		}
	}
	
}

?>
