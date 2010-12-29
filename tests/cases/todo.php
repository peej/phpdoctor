<?php

/**
 * @package PHPDoctor\Tests
 */
class TestTodoTag extends DoctorTestCase
{
	
    var $output;
    
	function testTodoTag() {
        $this->DoctorTestCase('Todo tag tests');
		
		$this->clearOutputDir();
		
		$this->setIniFile('todo.ini');
		$this->runPhpDoctor();
		
		$this->output = $this->readOutputFile('todo-list.html');
	}
	
	function testCorrectCopyIsShown() {
	    
		$expected = '<td class="name"><a href="phpdoctor/tests/todo.html">PHPDoctor\Tests\Todo</a></td><td class="description">This is a test todo message</td>';
		
		$this->assertStringContains($expected, $this->output, true); 
	}
	
}

?>
