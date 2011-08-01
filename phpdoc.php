#!/usr/bin/php
<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2005 Paul James <paul@peej.co.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

error_reporting(E_ALL & ~E_DEPRECATED);

// check we are running from the command line
if (!isset($argv[0])) {
    die('This program must be run from the command line using the CLI version of PHP');
    
// check we are using the correct version of PHP
} elseif (!defined('T_COMMENT') || !extension_loaded('tokenizer') || version_compare(phpversion(), '4.3.0', '<')) {
    error('You need PHP version 4.3.0 or greater with the "tokenizer" extension to run this script, please upgrade');
    exit;
}

// include PHPDoctor class
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
require('classes'.DIRECTORY_SEPARATOR.'phpDoctor.php');

// get name of config file to use
if (!isset($argv[1])) {
    if (isset($_ENV['PHPDoctor'])) {
        $argv[1] = $_ENV['PHPDoctor'];
    } elseif (is_file('default.ini')) {
        phpDoctor::warning('Using default config file "default.ini"');
        $argv[1] = 'default.ini';
    } else {
        phpDoctor::error('Usage: phpdoc.php [config_file]');
        exit;
    }
}

$phpdoc =& new phpDoctor($argv[1]);

$rootDoc =& $phpdoc->parse();

$phpdoc->execute($rootDoc);

?>
