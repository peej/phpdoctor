<?php

/**
 * @package PHPDoctor\Tests\Parser
 */
class TestCommentLinks extends DoctorTestCase
{
    var $output;

    function TestCommentLinks() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Comment links tests');

        $this->clearOutputDir();

        $this->setIniFile('comment-links.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/linktest/commentlinks.html');
    }

    function testFullyQualifiedClass() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html">PHPDoctor\Tests\LinkTest\linkTest</a>', $this->output, TRUE);
    }

    function testNonQualifiedClass() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html">linkTest</a>', $this->output, TRUE);
    }

    function testNonExistantClass() {
        $this->assertStringContains('<code>lonkTest</code>', $this->output, TRUE);
    }

    function testNonExistantPackageForLocalClass() {
        $this->assertStringContains('<code>PHPDoctor\linkTest</code>', $this->output, TRUE);
    }

    function testVariableInFullyQualifiedClass() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html#var">PHPDoctor\Tests\LinkTest\linkTest#var</a>', $this->output, TRUE);
    }

    function testVariableInFullyQualifiedClassAlternativeSyntax() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html#var">PHPDoctor\Tests\LinkTest\linkTest::var</a>', $this->output, TRUE);
    }

    function testVariableInFullyQualifiedClassWithDollar() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html#var">PHPDoctor\Tests\LinkTest\linkTest#$var</a>', $this->output, TRUE);
    }

    function testMethodInFullyQualifiedClass() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html#func()">PHPDoctor\Tests\LinkTest\linkTest#func</a>', $this->output, TRUE);
    }

    function testMethodInFullyQualifiedClassWithParenthesis() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html#func()">PHPDoctor\Tests\LinkTest\linkTest#func()</a>', $this->output, TRUE);
    }

    function testRootedFullyQualifiedClass() {
        $this->assertStringContains('<a href="../../../phpdoctor/tests/linktest/linktest.html">\PHPDoctor\Tests\LinkTest\linkTest</a>', $this->output, TRUE);
    }

    function testWebsiteLink() {
        $this->assertStringContains('<a href="http://www.google.com">http://www.google.com</a>', $this->output, TRUE);
    }

    function testWebsiteLinkWithName() {
        $this->assertStringContains('<a href="http://www.google.com">Google</a>', $this->output, TRUE);
    }

    function testSeeAlsoLink() {
        $this->assertStringContains('<dt>See Also:</dt>
<dd><a href="../../../phpdoctor/tests/linktest/linktest.html">Something else</a></dd>', $this->output, TRUE);
    }

}
