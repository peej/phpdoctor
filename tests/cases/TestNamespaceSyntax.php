<?php

/**
 * @package PHPDoctor\Tests
 */
class TestNamespaceSyntax extends DoctorTestCase
{

    function TestNamespaceSyntax() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Test use of the namespace syntaxes');
    }

    function skip() {
        $this->skipIf(!version_compare(EXEC_VERSION, '5.3.0', '>='), "Requires PHP 5.3");
    }

    function testStandardSyntax() {
        $this->setIniFile('standard-namespace-syntax.ini');
        $output = $this->runPhpDoctor();

        $this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\foo\bar\ZERO'));
        $this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\foo\bar\ONE = 1'));
    }

    function testAltSyntax() {
        $this->setIniFile('alt-namespace-syntax.ini');
        $output = $this->runPhpDoctor();

        $this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\woo\yay\ZERO'));
        $this->assertTrue(strpos($output, 'public final static int PHPDoctor\Tests\woo\yay\ONE = 1'));

        $this->assertTrue(strpos($output, 'public class PHPDoctor\Tests\A\Bar_A extends PHPDoctor\Tests\A\Foo'));
        $this->assertTrue(strpos($output, 'public class PHPDoctor\Tests\B\Bar_B extends PHPDoctor\Tests\B\Foo'));
        $this->assertTrue(strpos($output, 'public class PHPDoctor\Tests\A\C\Bar_C extends PHPDoctor\Tests\A\C\Foo'));

    }

}
