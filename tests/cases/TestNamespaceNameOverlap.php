<?php

/**
 * @package PHPDoctor\Tests
 */
class TestNamespaceNameOverlap extends DoctorTestCase {

    function TestNamespaceNameOverlap() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Test class name overlap across namespaces');
    }

    function skip() {
        $this->skipIf(!version_compare(EXEC_VERSION, '5.3.0', '>='), "Requires PHP 5.3");
    }

    function test() {
        $this->setIniFile('namespace-name-overlap.ini');
        $output = $this->runPhpDoctor();
        $this->assertTrue(strpos($output, 'a'.DIRECTORY_SEPARATOR.'b'.DIRECTORY_SEPARATOR.'c'.DIRECTORY_SEPARATOR.'package-tree.html'));
    }

}
