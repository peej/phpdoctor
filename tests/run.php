<?php

/**
 * Start the testsuite here.
 *
 * Will adjust error reporting. For clean results, this has to be done outside of the file where the testsuite
 * is actually built (test.php).
 */

error_reporting(error_reporting() & ~2048 & ~8192); // Make sure E_STRICT and E_DEPRECATED are disabled

$baseDir = dirname(__FILE__).DIRECTORY_SEPARATOR;

// make sure the phpDoctor dirs are in the include path
set_include_path(
    dirname($baseDir).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'lastcraft'.PATH_SEPARATOR.
    $baseDir.PATH_SEPARATOR.
    dirname($baseDir).PATH_SEPARATOR.
    get_include_path()
);

chdir($baseDir);

include 'test.php';
