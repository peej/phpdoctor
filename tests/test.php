<?php

// Run forever if we need to
ini_set('max_execution_time', 0);

// lucky guess where simpletest may be
set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(dirname(__FILE__))));

require_once 'simpletest'.DIRECTORY_SEPARATOR.'unit_tester.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'mock_objects.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'reporter.php';
require_once 'doctorTestCase.php';

if (!defined('PHP')) define('PHP', 'php');
#if (!defined('PHP')) define('PHP', '~/php-5.3.2/sapi/cli/php');

exec(PHP.' -v', $versionInfo);
preg_match('/PHP ([0-9]+\.[0-9]+\.[0-9]+)/', $versionInfo[0], $versionInfo);
define('EXEC_VERSION', $versionInfo[1]);

$selected = array();
if (TextReporter::inCli()) {
    $reporter = new TextReporter();
    // take parameters as tests to run
    while (1 < count($argv)) {
        $selected[] = array_pop($argv);
    }
} else {
    $reporter = new HtmlReporter();
    // run.php?selected[]=zerovalue&selected[]=lastline
    if (isset($_GET) && array_key_exists('selected', $_GET)) {
        $selected = $_GET['selected'];
    }
}

// All tests, grouped
$allTests = array(
    'Parser' => array('parser', 'config', 'ignore-package-tags', 'use-class-path-as-package', 'namespace-syntax', 'namespace-name-overlap'),
    'Standard Doclet' => array('standard-doclet', 'access', 'access-php5', 'throws-tag'),
    // these tests will work with PHP5 < 5.3
    'Bugfixes' => array('linefeed', 'lastline', 'zerovalue', 'todo', 'comment-links'),
    'Formatters' => array('lists-ul', 'markdown')
);

$suite = new TestSuite('PHPDoctor');
foreach ($allTests as $name => $tests) {
    $group = new TestSuite($name);
    foreach ($tests as $test) {
        if (!$selected || in_array($test, $selected)) {
            $group->addFile(sprintf('tests/cases/%s.php', $test));
        }
    }
    $suite->add($group);
}

if (TextReporter::inCli()) {
    exit ($suite->run($reporter) ? 0 : 1);
}
$suite->run($reporter);
