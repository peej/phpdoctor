<?php

// Run forever if we need to
ini_set('max_execution_time', 0);

require_once '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'lastcraft'.DIRECTORY_SEPARATOR.'simpletest'.DIRECTORY_SEPARATOR.'unit_tester.php';
require_once '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'lastcraft'.DIRECTORY_SEPARATOR.'simpletest'.DIRECTORY_SEPARATOR.'mock_objects.php';
require_once '..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'lastcraft'.DIRECTORY_SEPARATOR.'simpletest'.DIRECTORY_SEPARATOR.'reporter.php';

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


$parser = new TestSuite('Parser');
$parser->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'parser.php');
$parser->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'config.php');
$parser->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'ignore-package-tags.php');
$parser->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'use-class-path-as-package.php');
$parser->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'namespace-syntax.php');

$standardDoclet = new TestSuite('Standard Doclet');
$standardDoclet->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'standard-doclet.php');
$standardDoclet->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'access.php');
$standardDoclet->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'access-php5.php');
$standardDoclet->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'throws-tag.php');

$fixes = new TestSuite('Bugfixes'); // these tests will work with PHP5 < 5.3
$fixes->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'linefeed.php');
$fixes->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'lastline.php');
$fixes->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'zerovalue.php');
$fixes->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'todo.php');
$fixes->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'comment-links.php');

$formatters = new TestSuite('Formatters'); // these tests will work with PHP5 < 5.3
$formatters->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'lists-ul.php');
include_once "markdown.php";
if (function_exists('Markdown')) {
    $formatters->addFile('tests'.DIRECTORY_SEPARATOR.'cases'.DIRECTORY_SEPARATOR.'markdown.php');
} else {
    $reporter->paintMessage("Not running Markdown test, Markdown not available on system");
}

$test = new TestSuite('PHPDoctor');
$test->add($parser);
$test->add($standardDoclet);
$test->add($fixes);
$test->add($formatters);

if (TextReporter::inCli()) {
	exit ($test->run($reporter) ? 0 : 1);
}
$test->run($reporter);

?>