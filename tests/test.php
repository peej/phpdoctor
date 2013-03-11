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
        $selected = (array)$_GET['selected'];
    }
}

// All tests, grouped
$allTests = array(
    'Base' => array(
        'parser',
        'config',
        'ignorePackageTags',
        'useClassPathAsPackage',
        'namespaceSyntax',
        'namespaceNameOverlap',
        'traits',
        #'dynamicDefine'
    ),
    'Standard Doclet' => array(
        'standardDoclet',
        'accessLevel',
        'accessLevelPHP5',
        'throwsTag'
    ),
    // these tests will work with PHP5 < 5.3
    'Bugfixes' => array(
        'linefeed',
        'lastLine',
        'zeroValue',
        'todoTag',
        'commentLinks'
    ),
    'Formatters' => array(
        'listsUl',
        'markdown'
    )
);

$suite = new TestSuite('PHPDoctor');
foreach ($allTests as $name => $tests) {
    $group = new TestSuite($name);
    foreach ($tests as $test) {
        if (!$selected || in_array($test, $selected)) {
            $group->addFile(sprintf('tests/cases/Test%s.php', ucwords($test)));
        }
    }
    $suite->add($group);
}

if (TextReporter::inCli()) {
    exit ($suite->run($reporter) ? 0 : 1);
}
$suite->run($reporter);
