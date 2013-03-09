<?php

/**
 * @package PHPDoctor\Tests
 */
class TestStandardDoclet extends DoctorTestCase
{

    function TestStandardDoclet() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Standard doclet tests');

        $this->clearOutputDir();

        $this->setIniFile('standard-doclet.ini');
        $this->runPhpDoctor();

    }

    function testEscapeHTMLInVariable() {
        $results = $this->readOutputFile('phpdoctor/tests/data/aclass.html');
        $this->assertTrue(strpos($results, '<strong>$varContainingHTMLToEscape</strong> = \'&lt;strong&gt;Escape me&lt;/strong&gt;\'</code>'));
    }

    function testExtendingClassWithSameNameInDifferentNamespace() {
        if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $results = $this->readOutputFile('phpdoctor/tests/filelevel/duplicateclass.html');
            $this->assertTrue(strpos($results, '<p class="signature">public  class <strong>duplicateClass</strong><br>extends <a href="../../../phpdoctor/tests/mynamespace/duplicateclass.html">duplicateClass</a>'));
        }
    }

    function testInterfaceImplementsLink() {
        $results = $this->readOutputFile('phpdoctor/tests/data/aclass.html');
        $this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/data/aninterface.html">anInterface</a>'));

        if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $results = $this->readOutputFile('phpdoctor/tests/filelevel/implementanexternalinterface.html');
            $this->assertTrue(strpos($results, '<a href="../../../phpdoctor/tests/mynamespace/aninterface.html">PHPDoctor\Tests\MyNamespace\anInterface</a>'));
        }
    }

    function testShortDescriptionWithAFullStop() {
        $results = $this->readOutputFile('phpdoctor/tests/data/package-summary.html');
        $this->assertStringContains('<td class="description">This is aClass that implements anInterface.|</td>', $results, true);
    }

    function testInheritance() {
        $results = $this->readOutputFile('phpdoctor/tests/filelevel/inherittestchild.html');
        $this->assertTrue(strpos($results, '<p>Test inheriting of class doccomments</p>'));
        $this->assertTrue(strpos($results, '<p class="description">Parent field comment is: Test inheriting of field doccomments</p>'));
        $this->assertTrue(strpos($results, '<p class="description">Parent method comment is: Test inheriting of method doccomments</p>'));
        $this->assertStringContains('<p>Parent field comment is: Test inheriting of field doccomments</p>', $results, TRUE);
        $this->assertTrue(strpos($results, '<p>Parent method comment is: Test inheriting of method doccomments</p>'));
    }

}
