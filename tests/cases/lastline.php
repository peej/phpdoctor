<?php

/**
 * @package PHPDoctor\Tests
 */
class TestLastLine extends DoctorTestCase
{
	
    var $output;
    
	function testLastLine() {
        $this->DoctorTestCase('Last line tests');
		
		$this->clearOutputDir();
		
		$this->setIniFile('lastline.ini');
		$this->runPhpDoctor();
		
		$this->output = $this->readOutputFile('phpdoctor/tests/lastline.html');
	}
	
	function testNoSlashInDescriptions() {
		// This will capture all tags without description
		$this->assertStringDoesNotContain('<dd>/</dd>', $this->output); 
		$this->assertStringDoesNotContainRx('%<dd>(\s*/\s*)+</dd>%', $this->output);
	}
	
	function testTagOnLastLine() {
		$this->assertStringContains('<dl>|<dt>Returns:</dt>|<dd>a return tag on the last line</dd>|</dl>', $this->output, true);
	}
	
	function testLastLineEmpty() {
		$this->assertStringContains('<dl>|<dt>Returns:</dt>|<dd>a return tag followed by an empty line</dd>|</dl>', $this->output, true);
	}
	
	function testLastTwoLinesEmpty() {
		$this->assertStringContains('<dl>|<dt>Returns:</dt>|<dd>a return tag followed by two empty lines</dd>|</dl>', $this->output, true);
	}
	
}

?>
