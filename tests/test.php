<?php

// Run forever if we need to
ini_set('max_execution_time', 0);

require_once 'simpletest'.DIRECTORY_SEPARATOR.'unit_tester.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'mock_objects.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'reporter.php';

require_once 'doctorTestCase.php';

if (!defined('PHP')) define('PHP', 'php');
#if (!defined('PHP')) define('PHP', '~/php-5.3.2/sapi/cli/php');

exec(PHP.' -v', $versionInfo);
preg_match('/PHP ([0-9]+\.[0-9]+\.[0-9]+)/', $versionInfo[0], $versionInfo);
define('EXEC_VERSION', $versionInfo[1]);

if (TextReporter::inCli()) {
	$reporter = new TextReporter();
} else {
    $reporter = new HtmlReporter();
}


$parser = new GroupTest('Parser');
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'parser.php');
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'config.php');
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'ignore-package-tags.php');
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'use-class-path-as-package.php');
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'namespace-syntax.php');

$standardDoclet = new GroupTest('Standard Doclet');
$standardDoclet->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'standard-doclet.php');
$standardDoclet->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'access.php');
$standardDoclet->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'access-php5.php');
$standardDoclet->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'throws-tag.php');

$fixes = new GroupTest('Bugfixes'); // these tests will work with PHP5 < 5.3
$fixes->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'linefeed.php');
$fixes->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'lastline.php');
$fixes->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'zerovalue.php');
$fixes->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'todo.php');

$formatters = new GroupTest('Formatters'); // these tests will work with PHP5 < 5.3
$formatters->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'lists-ul.php');
include_once "markdown.php";
if (function_exists('Markdown')) {
    $formatters->addTestFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'markdown.php');
} else {
    $reporter->paintMessage("Not running Markdown test, Markdown not available on system");
}

$test = new GroupTest('PHPDoctor');
$test->addTestCase($parser);
$test->addTestCase($standardDoclet);
$test->addTestCase($fixes);
$test->addTestCase($formatters);

if (TextReporter::inCli()) {
	exit ($test->run($reporter) ? 0 : 1);
}
$test->run($reporter);

?>