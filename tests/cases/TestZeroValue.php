<?php

/**
 * @package PHPDoctor\Tests
 */
class TestZeroValue extends DoctorTestCase
{
    var $output;

    function TestZeroValue() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Zero Value tests');

        $this->clearOutputDir();

        $this->setIniFile('zerovalue.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/zerovalue.html');
    }

    function testConstIntZero() {
        $this->assertStringDoesNotContain('<p class="name">|<a href="#ZERO">$ZERO</a>|</p>', $this->output, true);
        $this->assertStringDoesNotContain('<code class="signature">public final static  int <strong>$ZERO</strong></code>', $this->output, true);

        $this->assertStringContains('<p class="name">|<a href="#ZERO">ZERO</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="ZERO">ZERO</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public final static  int <strong>ZERO</strong> = 0</code>', $this->output, true);
    }

    function testConstIntOne() {
        $this->assertStringContains('<p class="name">|<a href="#ONE">ONE</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="ONE">ONE</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public final static  int <strong>ONE</strong> = 1</code>', $this->output, true);
    }

    function testConstEmptyString() {
        $this->assertStringContains('<p class="name">|<a href="#EMPTY_STRING">EMPTY_STRING</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="EMPTY_STRING">EMPTY_STRING</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public final static  str <strong>EMPTY_STRING</strong> = \'\'</code>', $this->output, true);
    }

    function testConstNonEmptyString() {
        $this->assertStringContains('<p class="name">|<a href="#SOME_STRING">SOME_STRING</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="SOME_STRING">SOME_STRING</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public final static  str <strong>SOME_STRING</strong> = \'whatever const\'</code>', $this->output, true);
    }

    function testConstNull() {
        $this->assertStringContains('<p class="name">|<a href="#NULL_CONST">NULL_CONST</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="NULL_CONST">NULL_CONST</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public final static  mixed <strong>NULL_CONST</strong> = null</code>', $this->output, true);
    }

    function testPropIntZero() {

        $this->assertStringDoesNotContain('<code class="signature">public  mixed <strong>$prop_zero</strong></code>', $this->output, true);

        $this->assertStringContains('<p class="name">|<a href="#prop_zero">$prop_zero</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_zero">prop_zero</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_zero</strong> = 0</code>', $this->output, true);
    }

    function testPropIntOne() {
        $this->assertStringContains('<p class="name">|<a href="#prop_one">$prop_one</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_one">prop_one</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_one</strong> = 1</code>', $this->output, true);
    }

    function testPropEmptyString() {
        $this->assertStringContains('<p class="name">|<a href="#prop_emptystring">$prop_emptystring</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_emptystring">prop_emptystring</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_emptystring</strong> = \'\'</code>', $this->output, true);
    }

    function testPropNonEmptyString() {
        $this->assertStringContains('<p class="name">|<a href="#prop_somestring">$prop_somestring</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_somestring">prop_somestring</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_somestring</strong> = \'whatever string\'</code>', $this->output, true);
    }

    function testPropNull() {
        $this->assertStringContains('<p class="name">|<a href="#prop_null">$prop_null</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_null">prop_null</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_null</strong> = null</code>', $this->output, true);
    }

    function testPropUndefined() {
        $this->assertStringContains('<p class="name">|<a href="#prop_undefined">$prop_undefined</a>|</p>', $this->output, true);
        $this->assertStringContains('<h3 id="prop_undefined">prop_undefined</h3>', $this->output, true);
        $this->assertStringContains('<code class="signature">public  mixed <strong>$prop_undefined</strong></code>', $this->output, true);
    }

}
