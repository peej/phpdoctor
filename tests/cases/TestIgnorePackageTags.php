<?php

/**
 * @package PHPDoctor\Tests
 */
class TestIgnorePackageTags extends DoctorTestCase
{
    var $output;

    function TestIgnorePackageTags() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Ignore package tags option');

        $this->setIniFile('ignore-package-tags.ini');
        $this->output = $this->runPhpDoctor();
    }

    function testClass() {
        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\aClass'));
    }

    function testInterface() {
        $this->assertTrue(strpos($this->output, 'public interface PHPDoctor\\Tests\\anInterface'));
    }

    function testMemberVariable() {
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\$aVar'));
    }

    function testMemberFunction() {
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\aMethod()'));
    }

    function testConstant() {
        $this->assertTrue(strpos($this->output, 'final int PHPDoctor\\Tests\\THREE'));
    }

    function testFunction() {
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\aFunction()'));
    }

    function testVariable() {
        $this->assertTrue(strpos($this->output, 'mixed PHPDoctor\\Tests\\$one'));
    }

}
