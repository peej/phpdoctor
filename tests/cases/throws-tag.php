<?php

/**
 * @package PHPDoctor\Tests\Parser
 */
class TestThrowsTag extends DoctorTestCase
{
	
    var $output;
    
	function testThrowsTag() {
        $this->DoctorTestCase('Throws tag tests');
		
		$this->clearOutputDir();
		
		$this->setIniFile('throws-tag.ini');
		$this->runPhpDoctor();
		
		$this->output = $this->readOutputFile('phpdoctor/tests/parser/throwstag.html');
		#die($this->output);
	}
	
	function testThrowsTagContent() {
		$this->assertStringContains('<dd><a href="../../../phpdoctor/tests/testexception.html">testException</a> - Some kind of exception occurred</dd>', $this->output, TRUE);
	}
	
}
