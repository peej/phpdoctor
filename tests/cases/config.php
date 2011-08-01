<?php

require_once 'classes'.DIRECTORY_SEPARATOR.'phpDoctor.php';

/**
 * @package PHPDoctor\Tests
 */
class TestConfig extends UnitTestCase
{
	
	function testConfig() {
        $this->UnitTestCase('Configuration option tests');
	}
	
	function testMakeAbsolutePath() {
	    
	    $phpdoctor = new PHPDoctor('cases/ini/parser.ini');
	    
	    // regular
	    $this->assertEqual($phpdoctor->makeAbsolutePath('woo', '/var/yay'), '/var/yay/woo');
	    // local
	    $this->assertEqual($phpdoctor->makeAbsolutePath('./woo', '/var/yay'), '/var/yay/woo');
	    // unix root
	    $this->assertEqual($phpdoctor->makeAbsolutePath('/var/woo', '/var/yay'), '/var/woo');
	    // windows root
	    $this->assertEqual($phpdoctor->makeAbsolutePath('C:\\woo', '/var/yay'), 'C:\\woo');
	    // unix home directory
	    $this->assertEqual($phpdoctor->makeAbsolutePath('~/woo', '/var/yay'), '~/woo');
	    // windows network location
	    $this->assertEqual($phpdoctor->makeAbsolutePath('\\\\somewhere\\woo', '/var/yay'), '\\\\somewhere\\woo');
	    // url
	    $this->assertEqual($phpdoctor->makeAbsolutePath('http://somewhere/woo', '/var/yay'), 'http://somewhere/woo');
	    
	    // parent dir
	    $this->assertEqual($phpdoctor->makeAbsolutePath('../woo', '/var/yay'), '/var/woo');
	    $this->assertEqual($phpdoctor->makeAbsolutePath('../../../woo', '/var/foo/bar/baz'), '/var/woo');
	    
	}
	
}
