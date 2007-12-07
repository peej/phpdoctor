<?php

ini_set('max_execution_time', 0);

require_once 'simpletest'.DIRECTORY_SEPARATOR.'unit_tester.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'mock_objects.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'reporter.php';

$parser = &new GroupTest('Parser');
//*
$parser->addTestFile('tests'.DIRECTORY_SEPARATOR.'php5.php');
//*/

$test = &new GroupTest('PHPDoctor');
$test->addTestCase($parser);

if (TextReporter::inCli()) {
	exit ($test->run(new TextReporter()) ? 0 : 1);
}
$test->run(new HtmlReporter());

?>