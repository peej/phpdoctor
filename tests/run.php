<?php
    
    /**
     * Start the testsuite here.
     *
     * Will adjust error reporting. For clean results, this has to be done outside of the file where the testsuite
     * is actually built (test.php).
     *
     * Makes sure the test dirs are in the include path.
     */
    
    error_reporting(error_reporting() & ~2048 & ~8192); // Make sure E_STRICT and E_DEPRECATED are disabled
    
    $testdir = dirname(__FILE__).DIRECTORY_SEPARATOR;
    $casedir = $testdir.'cases'.DIRECTORY_SEPARATOR;
    
    set_include_path(get_include_path().PATH_SEPARATOR.$testdir.PATH_SEPARATOR.dirname($testdir)); // make sure the phpDoctor dirs are in the include path
    chdir($testdir);
    
    if (!file_exists($casedir.'tmp')) mkdir( $casedir.'tmp');
    if (!file_exists($casedir.'output')) mkdir( $casedir.'output');
    
    include('test.php');
    
?>