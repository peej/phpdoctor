#!/usr/bin/php
<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2004 Paul James <paul@peej.co.uk>

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

// $Id: phpdoc.php,v 1.6 2005/05/08 21:53:30 peejeh Exp $

// check we are running from the command line
if (!isset($argv[0])) {
    die('This program must be run from the command line using the CLI version of PHP');
    
// check we are using the correct version of PHP
} elseif (!defined('T_COMMENT') || !extension_loaded('tokenizer') || version_compare(phpversion(), '4.3.0', '<')) {
    error('You need PHP version 4.3.0 or greater with the "tokenizer" extension to run this script, please upgrade');
    exit;
}

// include PHPDoctor class
require('classes/phpDoctor.php');

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

/* DEBUG -- WARNING: MAY CAUSE UNTRAPPED RECURSIVE LOOP IN PRINT_R
if ($fp = fopen('output.txt', 'w')) {
    ob_start();
    print_r($phpdoc);
    print_r($rootDoc);
    $output = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $output);
    fclose($fp);
}
//*/

$phpdoc->execute($rootDoc);

/*
echo "T_AND_EQUAL = ", T_AND_EQUAL, "\n";
echo "T_ARRAY = ", T_ARRAY, "\n";
echo "T_ARRAY_CAST = ", T_ARRAY_CAST, "\n";
echo "T_AS = ", T_AS, "\n";
echo "T_BAD_CHARACTER = ", T_BAD_CHARACTER, "\n";
echo "T_BOOLEAN_AND = ", T_BOOLEAN_AND, "\n";
echo "T_BOOLEAN_OR = ", T_BOOLEAN_OR, "\n";
echo "T_BOOL_CAST = ", T_BOOL_CAST, "\n";
echo "T_BREAK = ", T_BREAK, "\n";
echo "T_CASE = ", T_CASE, "\n";
echo "T_CHARACTER = ", T_CHARACTER, "\n";
echo "T_CLASS = ", T_CLASS, "\n";
echo "T_CLOSE_TAG = ", T_CLOSE_TAG, "\n";
echo "T_COMMENT = ", T_COMMENT, "\n";
echo "T_CONCAT_EQUAL = ", T_CONCAT_EQUAL, "\n";
echo "T_CONST = ", T_CONST, "\n";
echo "T_CONSTANT_ENCAPSED_STRING = ", T_CONSTANT_ENCAPSED_STRING, "\n";
echo "T_CONTINUE = ", T_CONTINUE, "\n";
echo "T_CURLY_OPEN = ", T_CURLY_OPEN, "\n";
echo "T_DEC = ", T_DEC, "\n";
echo "T_DECLARE = ", T_DECLARE, "\n";
echo "T_DEFAULT = ", T_DEFAULT, "\n";
echo "T_DIV_EQUAL = ", T_DIV_EQUAL, "\n";
echo "T_DNUMBER = ", T_DNUMBER, "\n";
echo "T_DO = ", T_DO, "\n";
echo "T_DOLLAR_OPEN_CURLY_BRACES = ", T_DOLLAR_OPEN_CURLY_BRACES, "\n";
echo "T_DOUBLE_ARROW = ", T_DOUBLE_ARROW, "\n";
echo "T_DOUBLE_CAST = ", T_DOUBLE_CAST, "\n";
echo "T_ECHO = ", T_ECHO, "\n";
echo "T_ELSE = ", T_ELSE, "\n";
echo "T_ELSEIF = ", T_ELSEIF, "\n";
echo "T_EMPTY = ", T_EMPTY, "\n";
echo "T_ENCAPSED_AND_WHITESPACE = ", T_ENCAPSED_AND_WHITESPACE, "\n";
echo "T_ENDDECLARE = ", T_ENDDECLARE, "\n";
echo "T_ENDFOR = ", T_ENDFOR, "\n";
echo "T_ENDFOREACH = ", T_ENDFOREACH, "\n";
echo "T_ENDIF = ", T_ENDIF, "\n";
echo "T_ENDSWITCH = ", T_ENDSWITCH, "\n";
echo "T_ENDWHILE = ", T_ENDWHILE, "\n";
echo "T_END_HEREDOC = ", T_END_HEREDOC, "\n";
echo "T_EVAL = ", T_EVAL, "\n";
echo "T_EXIT = ", T_EXIT, "\n";
echo "T_EXTENDS = ", T_EXTENDS, "\n";
echo "T_FILE = ", T_FILE, "\n";
echo "T_FOR = ", T_FOR, "\n";
echo "T_FOREACH = ", T_FOREACH, "\n";
echo "T_FUNCTION = ", T_FUNCTION, "\n";
echo "T_GLOBAL = ", T_GLOBAL, "\n";
echo "T_IF = ", T_IF, "\n";
echo "T_INC = ", T_INC, "\n";
echo "T_INCLUDE  = ", T_INCLUDE , "\n";
echo "T_INCLUDE_ONCE     = ", T_INCLUDE_ONCE    , "\n";
echo "T_INLINE_HTML  = ", T_INLINE_HTML , "\n";
echo "T_INT_CAST     = ", T_INT_CAST    , "\n";
echo "T_ISSET    = ", T_ISSET   , "\n";
echo "T_IS_EQUAL     = ", T_IS_EQUAL    , "\n";
echo "T_IS_GREATER_OR_EQUAL  = ", T_IS_GREATER_OR_EQUAL , "\n";
echo "T_IS_IDENTICAL     = ", T_IS_IDENTICAL    , "\n";
echo "T_IS_NOT_EQUAL     = ", T_IS_NOT_EQUAL    , "\n";
echo "T_IS_NOT_IDENTICAL     = ", T_IS_NOT_IDENTICAL    , "\n";
echo "T_SMALLER_OR_EQUAL     = ", T_SMALLER_OR_EQUAL    , "\n";
echo "T_LINE     = ", T_LINE    , "\n";
echo "T_LIST     = ", T_LIST    , "\n";
echo "T_LNUMBER  = ", T_LNUMBER , "\n";
echo "T_LOGICAL_AND  = ", T_LOGICAL_AND , "\n";
echo "T_LOGICAL_OR   = ", T_LOGICAL_OR  , "\n";
echo "T_LOGICAL_XOR  = ", T_LOGICAL_XOR , "\n";
echo "T_MINUS_EQUAL  = ", T_MINUS_EQUAL , "\n";
echo "T_ML_COMMENT   = ", T_ML_COMMENT  , "\n";
echo "T_MOD_EQUAL    = ", T_MOD_EQUAL   , "\n";
echo "T_MUL_EQUAL    = ", T_MUL_EQUAL   , "\n";
echo "T_NEW  = ", T_NEW , "\n";
echo "T_NUM_STRING   = ", T_NUM_STRING  , "\n";
echo "T_OBJECT_CAST  = ", T_OBJECT_CAST , "\n";
echo "T_OBJECT_OPERATOR  = ", T_OBJECT_OPERATOR , "\n";
echo "T_OLD_FUNCTION     = ", T_OLD_FUNCTION    , "\n";
echo "T_OPEN_TAG     = ", T_OPEN_TAG    , "\n";
echo "T_OPEN_TAG_WITH_ECHO   = ", T_OPEN_TAG_WITH_ECHO  , "\n";
echo "T_OR_EQUAL     = ", T_OR_EQUAL    , "\n";
echo "T_PAAMAYIM_NEKUDOTAYIM     = ", T_PAAMAYIM_NEKUDOTAYIM    , "\n";
echo "T_PLUS_EQUAL   = ", T_PLUS_EQUAL  , "\n";
echo "T_PRINT    = ", T_PRINT   , "\n";
echo "T_REQUIRE  = ", T_REQUIRE , "\n";
echo "T_REQUIRE_ONCE     = ", T_REQUIRE_ONCE    , "\n";
echo "T_RETURN   = ", T_RETURN  , "\n";
echo "T_SL   = ", T_SL  , "\n";
echo "T_SL_EQUAL     = ", T_SL_EQUAL, "\n";
echo "T_SR   = ", T_SR, "\n";
echo "T_SR_EQUAL     = ", T_SR_EQUAL, "\n";
echo "T_START_HEREDOC    = ", T_START_HEREDOC, "\n";
echo "T_STATIC   = ", T_STATIC, "\n";
echo "T_STRING   = ", T_STRING, "\n";
echo "T_STRING_CAST  = ", T_STRING_CAST, "\n";
echo "T_STRING_VARNAME   = ", T_STRING_VARNAME, "\n";
echo "T_SWITCH   = ", T_SWITCH, "\n";
echo "T_THROW = ", T_THROW, "\n";
echo "T_UNSET    = ", T_UNSET, "\n";
echo "T_UNSET_CAST   = ", T_UNSET_CAST, "\n";
echo "T_USE  = ", T_USE, "\n";
echo "T_VAR  = ", T_VAR, "\n";
echo "T_VARIABLE     = ", T_VARIABLE, "\n";
echo "T_WHILE    = ", T_WHILE, "\n";
echo "T_WHITESPACE   = ", T_WHITESPACE, "\n";
echo "T_XOR_EQUAL    = ", T_XOR_EQUAL, "\n";
echo "T_FUNC_C   = ", T_FUNC_C, "\n";
echo "T_CLASS_C = ", T_CLASS_C, "\n";
//*/

?>
