<?php

/**
 * @package PHPDoctor\Tests
 */
class TestUseClassPathAsPackage extends DoctorTestCase
{
    var $output;

    function TestUseClassPathAsPackage() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Test use class path as package option');

        $this->setIniFile('use-class-path-as-package.ini');
        $this->output = $this->runPhpDoctor();
    }

    function testClass() {
        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\Package\\Subpackage\\testClass'));
    }

/*
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
*/

}
