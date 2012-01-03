<?php

/**
 * @package PHPDoctor\Tests
 */
class TestNamespaceNameOverlap extends DoctorTestCase
{

	function TestNamespaceNameOverlap() {

		$this->DoctorTestCase('Test class name overlap across namespaces');

	}

	function test() {

		if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
			$this->setIniFile('namespace-name-overlap.ini');
			$output = $this->runPhpDoctor();

			$this->assertTrue(strpos($output, 'a\b\c\package-tree.html'));
		}
	}

}

?>
