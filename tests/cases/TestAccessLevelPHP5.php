<?php

/**
 * @package PHPDoctor\Tests\Parser
 */
class TestAccessLevelPHP5 extends DoctorTestCase
{
    var $output;

    function TestAccessLevelPHP5() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Access level PHP5 tests');

        $this->clearOutputDir();

        $this->setIniFile('access-php5.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/parser/accesslevelphp5.html');
    }

    function testPublicVar() {
        $this->assertStringContains('<code class="signature">public mixed <strong>$publicVar</strong></code>', $this->output, TRUE);
    }

    function testProtectedVar() {
        $this->assertStringContains('<code class="signature">protected mixed <strong>$protectedVar</strong></code>', $this->output, TRUE);
    }

    function testPrivateVar() {
        $this->assertStringContains('<code class="signature">private mixed <strong>$privateVar</strong></code>', $this->output, TRUE);
    }

    function testPublicMethod() {
        $this->assertStringContains('<code class="signature">public void <strong>publicMethod</strong>()</code>', $this->output, TRUE);
    }

    function testProtectedMethod() {
        $this->assertStringContains('<code class="signature">protected void <strong>protectedMethod</strong>()</code>', $this->output, TRUE);
    }

    function testPrivateMethod() {
        $this->assertStringContains('<code class="signature">private void <strong>privateMethod</strong>()</code>', $this->output, TRUE);
    }

    function testProtectedConstructor() {
        $this->assertStringContains('<code class="signature">protected void <strong>__construct</strong>()</code>', $this->output, TRUE);
    }

}
