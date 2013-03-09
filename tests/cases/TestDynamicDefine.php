<?php

/**
 * @package PHPDoctor\Tests
 */
class TestDynamicDefine extends DoctorTestCase
{
    var $output;

    function TestDynamicDefine() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Dynamic define tests');

        $this->clearOutputDir();

        $this->setIniFile('dynamic-define.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/parser/package-globals.html');
    }

    function testNoGlobals() {
        $expected = '<h1>Globals</h1>

<hr>

<div class="header">
<h1>Unknown</h1>';
        $this->assertStringContains($expected, $this->output, true);
    }

}
