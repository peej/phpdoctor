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

if (TextReporter::inCli()) {
    $reporter = new TextReporter();
} else {
    $reporter = new HtmlReporter();
}

// All tests, grouped
$allTests = array(
    'Parser' => array('parser.php', 'config.php', 'ignore-package-tags.php', 'use-class-path-as-package.php', 'namespace-syntax.php', 'namespace-name-overlap.php'),
    'Standard Doclet' => array('standard-doclet.php', 'access.php', 'access-php5.php', 'throws-tag.php'),
    // these tests will work with PHP5 < 5.3
    'Bugfixes' => array('linefeed.php', 'lastline.php', 'zerovalue.php', 'todo.php', 'comment-links.php'),
    'Formatters' => array('lists-ul.php', 'markdown.php')
);

$suite = new TestSuite('PHPDoctor');
foreach ($allTests as $name => $files) {
    $group = new TestSuite($name);
    foreach ($files as $file) {
        $group->addFile(sprintf('tests/cases/%s', $file));
    }
    $suite->add($group);
}

if (TextReporter::inCli()) {
    exit ($suite->run($reporter) ? 0 : 1);
}
$suite->run($reporter);
