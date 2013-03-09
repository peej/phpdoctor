<?php

/**
 * @package PHPDoctor\Tests\Parser
 */
class TestThrowsTag extends DoctorTestCase
{
    var $output;

    function TestThrowsTag() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Throws tag tests');

        $this->clearOutputDir();

        $this->setIniFile('throws-tag.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/parser/throwstag.html');
    }

    function testThrowsTagContent() {
        $this->assertStringContains('<dt>Throws:</dt>
<dd><a href="../../../phpdoctor/tests/parser/testexception.html">testException</a> - Some kind of exception occurred</dd>', $this->output, TRUE);
    }

}
