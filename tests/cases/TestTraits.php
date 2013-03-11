<?php

/**
 * @package PHPDoctor\Tests
 */
class TestTraits extends DoctorTestCase
{
    var $output;

    function TestTraits() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Trait test');
    }

    function skip() {
        $this->skipIf(!version_compare(EXEC_VERSION, '5.4.0', '>='), "Requires PHP 5.4");
    }

    function testTrait() {
        $this->setIniFile('traits.ini');
        $this->output = $this->runPhpDoctor();

        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\Tests\Parser\traitTestClass uses PHPDoctor\Tests\Parser\myTrait PHPDoctor\Tests\Parser\myOtherTrait'));
    }

}