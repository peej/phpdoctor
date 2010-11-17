<?php

/**
 * @package PHPDoctor\Tests
 */
class TestLinefeed extends DoctorTestCase
{
	
    var $src, $cmd;
    
	function testLinefeed() {
        $this->DoctorTestCase('Linefeed tests');
		
		// The source file must be generated on the fly because the line endings are converted to Unix when
		// pulling/pushing the repo. 
		
		$src = file_get_contents('tests/cases/data/testcase-linefeed.php', true);
		
		$src = str_replace("\r\n", "\n", $src);
		$this->src = str_replace("\r", "\n", $src);	// for those ancient Macs still out there
		
		$this->setIniFile('linefeed.ini');
	}
	
	function testParagraphsLF() {
		
		$this->clearTempDir();
		$this->clearOutputDir();
		
		file_put_contents(dirname(__file__).'/tmp/testcase-linefeed.php', $this->src, FILE_USE_INCLUDE_PATH);
		
		$this->runPhpDoctor();
		$results = $this->readOutputFile('phpdoctor/tests/linefeed.html');
		
		$this->assertStringContains('<p>Testing linefeeds.</p>', $results, true);
		$this->assertStringContains('<p>This is the second paragraph.</p>', $results, true);
		$this->assertStringContains('<p>This is the third, extending into a second short line.</p>', $results, true);
	}
	
	function testParagraphsCRLF() {
		
		$this->clearTempDir();
		$this->clearOutputDir();
		
		$src = str_replace("\n", "\r\n", $this->src);
		
		file_put_contents('tests/cases/tmp/testcase-linefeed.php', $src, FILE_USE_INCLUDE_PATH);
		
		$this->runPhpDoctor();
		$results = $this->readOutputFile('phpdoctor/tests/linefeed.html');
		
		$this->assertStringContains('<p>Testing linefeeds.</p>', $results, true);
		$this->assertStringContains('<p>This is the second paragraph.</p>', $results, true);
		$this->assertStringContains('<p>This is the third, extending into a second short line.</p>', $results, true);
	}
	
	function testParagraphsCR() {
		
		$this->clearTempDir();
		$this->clearOutputDir();
		
		$src = str_replace("\n", "\r", $this->src);
		
		file_put_contents('tests/cases/tmp/testcase-linefeed.php', $src, FILE_USE_INCLUDE_PATH);
		
		$this->runPhpDoctor();
		$results = $this->readOutputFile('phpdoctor/tests/linefeed.html');
		
		$this->assertStringContains('<p>Testing linefeeds.</p>', $results, true);
		$this->assertStringContains('<p>This is the second paragraph.</p>', $results, true);
		$this->assertStringContains('<p>This is the third, extending into a second short line.</p>', $results, true);
	}
	
}

?>
