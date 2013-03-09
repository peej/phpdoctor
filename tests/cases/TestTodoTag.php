<?php

/**
 * @package PHPDoctor\Tests
 */
class TestTodoTag extends DoctorTestCase
{
    var $output;
    var $output2;

    function TestTodoTag() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Todo tag tests');

        $this->clearOutputDir();

        $this->setIniFile('todo.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('todo-list.html');
        $this->output2 = $this->readOutputFile('phpdoctor/tests/todo.html');
    }

    function testCorrectCopyIsShown() {
        $expected = '<td class="name"><a href="phpdoctor/tests/todo.html">PHPDoctor\Tests\Todo</a></td><td class="description">This is a test todo message</td>';
        $this->assertStringContains($expected, $this->output, true);

        $expected = '<dt>Todo:</dt>
<dd>This is a test todo message</dd>
<dd>This is another todo message</dd>';
        $this->assertStringContains($expected, $this->output2, true);
    }

}
