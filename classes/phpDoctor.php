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

// $Id: phpDoctor.php,v 1.23 2006/12/16 20:58:15 peejeh Exp $

/** Undefined internal constants so we don't throw undefined constant errors later on */
if (!defined('T_DOC_COMMENT')) define('T_DOC_COMMENT',0);
if (!defined('T_ML_COMMENT')) define('T_ML_COMMENT', 0);
if (!defined('T_PRIVATE')) define('T_PRIVATE', 0);
if (!defined('T_PROTECTED')) define('T_PROTECTED', 0);
if (!defined('T_PUBLIC')) define('T_PUBLIC', 0);
if (!defined('T_ABSTRACT')) define('T_ABSTRACT', 0);
if (!defined('T_FINAL')) define('T_FINAL', 0);
if (!defined('T_INTERFACE')) define('T_INTERFACE', 0);
if (!defined('T_IMPLEMENTS')) define('T_IMPLEMENTS', 0);
if (!defined('T_CONST')) define('T_CONST', 0);
if (!defined('T_THROW')) define('T_THROW', 0);
if (!defined('GLOB_ONLYDIR')) define('GLOB_ONLYDIR', FALSE);

// load classes
require('classes/doc.php');
require('classes/rootDoc.php');
require('classes/packageDoc.php');
require('classes/programElementDoc.php');
require('classes/fieldDoc.php');
require('classes/classDoc.php');
require('classes/executableDoc.php');
require('classes/constructorDoc.php');
require('classes/methodDoc.php');
require('classes/type.php');
require('classes/tag.php');

/** This class holds the information from one run of PHPDoctor. Particularly
 * the packages, classes and options specified by the user. It is the root
 * of the parsed tokens and is passed to the doclet to be formatted into
 * output.
 *
 * @package PHPDoctor
 * @version $Revision: 1.23 $
 */
class PHPDoctor
{

	/** The version of PHPDoctor.
	 *
	 * @var str
	 */
	var $_version = '2RC2';

	/** The path PHPDoctor is running from.
	 *
	 * @var str
	 */
	var $_path = '.';

	/** The time in microseconds at the start of execution.
	 *
	 * @var int
	 */
	var $_startTime = NULL;

	/** Options from config file.
	 *
	 * @var str[]
	 */
	var $_options = array();

	/** Turn on verbose output.
	 *
	 * @var bool
	 */
	var $_verbose = FALSE;

	/** Turn off all output other than warnings and errors.
	 *
	 * @var bool
	 */
	var $_quiet = FALSE;

	/** Array of files to parse.
	 *
	 * @var str[]
	 */
	var $_files = array();
    
	/** Array of files not to parse.
	 *
	 * @var str[]
	 */
    var $_ignore = array();

	/** Directory containing files for parsing.
	 *
	 * @var str
	 */
	var $_sourcePath = './';

	/** Traverse sub-directories
	 *
	 * @var bool
	 */
	var $_subdirs = TRUE;

	/** Package to use for elements not in a package.
	 *
	 * @var str
	 */
	var $_defaultPackage = 'The Unknown Package';

	/** Overview file. The "source" file that contains the overview
	 * documentation.
	 *
	 * @var str
	 */
	var $_overview = NULL;

	/** Package comment directory. if set, PHPDoctor will look in this directory
	 * for package comment files. Otherwise it looks in a directory named after
	 * the package ala Javadoc.
	 *
	 * @var str
	 */
	var $_packageCommentDir = NULL;

	/** Parse out global variables.
	 *
	 * @var bool
	 */
	var $_globals = TRUE;

	/** Parse out global constants.
	 *
	 * @var bool
	 */
	var $_constants = TRUE;

	/** Display class tree.
	 *
	 * @var bool
	 */
	var $_tree = TRUE;

	/** Parse only public classes and members.
	 *
	 * @var bool
	 */
	var $_public = TRUE;

	/** Parse protected and public classes and members.
	 *
	 * @var bool
	 */
	var $_protected = FALSE;

	/** Parse all classes and members.
	 *
	 * @var bool
	 */
	var $_private = FALSE;

	/** Specifies the name of the class that starts the doclet used in generating
	 * the documentation.
	 *
	 * @var str
	 */
	var $_doclet = 'standard';

	/** Specifies the path to the doclet starting class file. If the doclet class
	 * is not in a file named <_doclet>/<_doclet>.php then this path should
	 * include the filename of the class file also.
	 *
	 * @var str
	 */
	var $_docletPath = 'doclets';

	/** Specifies the path to the taglets to use.
	 *
	 * @var str
	 */
	var $_tagletPath = 'taglets';

	/** The path and filename of the current file being parsed.
	 *
	 * @var str
	 */
	var $_currentFilename = NULL;
	
	/** Constructor
	 *
	 * @param str config The configuration file to use for this run of PHPDoctor
	 */
	function phpDoctor($config = 'default.ini')
    {

		// record start time
		$this->_startTime = $this->_getTime();
	
		// set the path
		$this->_path = $this->fixPath(dirname($_SERVER['argv'][0]));
	
		// read config file
		if (is_file($config)) {
			$this->_options = @parse_ini_file($config);
			if (count($this->_options) == 0) {
				$this->error('Could not parse configuration file "'.$config.'"');
				exit;
			}
		} else {
			$this->error('Could not find configuration file "'.$config.'"');
			exit;
		}
		
		// set phpdoctor options
		if (isset($this->_options['verbose'])) {
			$this->_verbose = $this->_options['verbose'];
			$this->verbose('Being verbose');
		}
		if (isset($this->_options['quiet'])) $this->_quiet = $this->_options['quiet'];
				
		if (isset($this->_options['source_path'])) $this->_sourcePath = $this->fixPath($this->makeAbsolutePath($this->_options['source_path'], $this->_path));
		if (isset($this->_options['subdirs'])) $this->_subdirs = $this->_options['subdirs'];
		if (isset($this->_options['files'])) {
			$files = explode(',', $this->_options['files']);
		} else {
			$files = array('*.php');
		}
		if (isset($this->_options['ignore'])) {
			$this->_ignore = explode(',', $this->_options['ignore']);
		}
		
		$this->verbose('Searching for files to parse...');
		$this->_files = array_unique($this->_buildFileList($files, $this->_sourcePath));
		if (count($this->_files) == 0) {
			$this->error('Could not find any files to parse');
			exit;
		}

		if (isset($this->_options['default_package'])) $this->_defaultPackage = $this->_options['default_package'];
		if (isset($this->_options['overview'])) $this->_overview = $this->makeAbsolutePath($this->_options['overview'], $this->_sourcePath);
		if (isset($this->_options['package_comment_dir'])) $this->_packageCommentDir = $this->makeAbsolutePath($this->_options['package_comment_dir'], $this->_sourcePath);

		if (isset($this->_options['globals'])) $this->_globals = $this->_options['globals'];
		if (isset($this->_options['constants'])) $this->_constants = $this->_options['constants'];
		if (isset($this->_options['tree'])) $this->_tree = $this->_options['tree'];

		if (isset($this->_options['private']) && $this->_options['private']) {
			$this->_public = TRUE;
			$this->_protected = TRUE;
			$this->_private = TRUE;
		} elseif (isset($this->_options['protected']) && $this->_options['protected']) {
			$this->_public = TRUE;
			$this->_protected = TRUE;
			$this->_private = FALSE;
		} elseif (isset($this->_options['public']) && $this->_options['public']) {
			$this->_public = TRUE;
			$this->_protected = FALSE;
			$this->_private = FALSE;
		}

		if (isset($this->_options['doclet'])) $this->_doclet = $this->_options['doclet'];
		if (isset($this->_options['doclet_path'])) $this->_docletPath = $this->_options['doclet_path'];
	
	}
	
	/**
	 * Build a complete list of file to parse. Expand out wildcards and
	 * traverse directories if asked to.
	 *
	 * @param str[] files Array of filenames to expand
	 */
	function _buildFileList($files, $dir)
    {
		$list = array();

		$dir = $this->fixPath($dir);

		foreach ($files as $filename) {
            $filename = $this->makeAbsolutePath(trim($filename), $dir);
            $globResults = glob($filename); // switch slashes since old versions of glob need forward slashes
            if ($globResults) {
                foreach ($globResults as $filepath) {
                    $okay = TRUE;
                    foreach ($this->_ignore as $ignore) {
                        if (strstr($filepath, trim($ignore))) {
                            $okay = FALSE;
                        }
                    }
                    if ($okay) {
                        $list[] = realpath($filepath);
                    }
                }
            } elseif (!$this->_subdirs) {
                $this->error('Could not find file "'.$filename.'"');
                exit;
            }
		}
		
		if ($this->_subdirs) { // recurse into subdir
			$globResults = glob($dir.'*', GLOB_ONLYDIR); // get subdirs
			if ($globResults) {
				foreach ($globResults as $dirName) {
                    $okay = TRUE;
                    foreach ($this->_ignore as $ignore) {
                        if (strstr($dirName, trim($ignore))) {
                            $okay = FALSE;
                        }
                    }
					if ($okay && (GLOB_ONLYDIR || is_dir($dirName))) { // handle missing only dir support
						$list = array_merge($list, $this->_buildFileList($files, $this->makeAbsolutePath($dirName, $this->_path)));
					}
				}
			}
		}

		return $list;
	}
	
	/**
	 * Write a message to standard output.
	 *
	 * @param str msg Message to output
	 */
	function message($msg)
    {
		if (!$this->_quiet) {
			echo $msg, "\n";
		}
	}

	/**
	 * Write a message to standard output.
	 *
	 * @param str msg Message to output
	 */
	function verbose($msg)
    {
		if ($this->_verbose) {
			echo $msg, "\n";
		}
	}

	/**
	 * Write a warning message to standard error.
	 *
	 * @param str msg Warning message to output
	 */
	function warning($msg)
    {
		if (!defined('STDERR')) define('STDERR', fopen("php://stderr", "wb"));
		fwrite(STDERR, 'WARNING: '.$msg."\n");
	}

	/**
	 * Write an error message to standard error.
	 *
	 * @param str msg Error message to output
	 */
	function error($msg)
    {
		if (!defined('STDERR')) define('STDERR', fopen("php://stderr", "wb"));
		fwrite(STDERR, 'ERROR: '.$msg."\n");
	}

	/**
	 * Get the current time in microseconds.
	 *
	 * @return int
	 */
	function _getTime()
    {
		$microtime = explode(' ', microtime());
		return $microtime[0] + $microtime[1];
	}
	
	/**
	 * Turn path into an absolute path using the given prefix?
	 *
	 * @param str path Path to make absolute
	 * @param str prefix Absolute path to append to relative path
	 * @return str
	 */
	function makeAbsolutePath($path, $prefix)
    {
		if (
			substr($path, 0, 1) == '/' || // unix root
			substr($path, 1, 2) == ':\\' || // windows root
			substr($path, 0, 2) == '~/' || // unix home directory
			substr($path, 0, 2) == '\\\\' || // windows network location
			preg_match('|^[a-z]+://|', $path) // url
		) {
            //var_dump($path);
			return $path;
		} else {
			return str_replace('./', '', $this->fixPath($prefix).$path);
		}
	}
	
	/**
	 * Add a trailing slash to a path if it does not have one.
	 *
	 * @param str path Path to postfix
	 * @return str
	 */
	function fixPath($path)
    {
        if (substr($path, -1, 1) != '/' && substr($path, -1, 1) != '\\') {
			return $path.'/';
		} else {
			return $path;
		}
	}

	/** Return the path PHPDoctor is running from.
	 *
	 * @return str
	 */
	function docletPath()
    {
		return $this->makeAbsolutePath($this->fixPath($this->_docletPath).$this->fixPath($this->_doclet), $this->_path);
	}

	/** Return the source path.
	 *
	 * @return str
	 */
	function sourcePath()
    {
		return $this->_sourcePath;
	}

	/** Return the version of PHPDoctor.
	 *
	 * @return str
	 */
	function version()
    {
		return $this->_version;
	}

	/** Return the default package.
	 *
	 * @return str
	 */
	function defaultPackage()
    {
		return $this->_defaultPackage;
	}
	
	/** Return a reference to the set options.
	 *
	 * @return str[] An array of strings.
	 */
	function &options()
    {
		return $this->_options;
	}

	/** Get a configuration option.
	 *
	 * @param str option
	 * @return str
	 */
	function getOption($option)
    {
		$option = '_'.$option;
		return $this->$option;
	}

	/** Parse files into tokens and create rootDoc.
	 *
	 * @return RootDoc
	 */
	function &parse()
    {

		$rootDoc =& new rootDoc($this);

		foreach ($this->_files as $filename) {
			$this->message('Reading file "'.$filename.'"');
			$fileString = @file_get_contents($filename);
			if ($fileString) {
				$this->_currentFilename = $filename;
				
				$tokens = token_get_all($fileString);

				if (!$this->_verbose) echo 'Parsing tokens';

				/* This array holds data gathered before the type of element is
				discovered and an object is created for it, including doc comment
				data. This data is stored in the object once it has been created and
				then merged into the objects data fields upon object completion. */
				$currentData = array();
				
				$currentPackage = $this->_defaultPackage; // the current package
				$currentElement = array(); // stack of element family, current at top of stack
				$ce =& $rootDoc; // reference to element at top of stack

				$open_curly_braces = FALSE;
				$in_parsed_string = FALSE;
                
                $counter = 0;

				foreach ($tokens as $key => $token) {
					if (!$in_parsed_string && is_array($token)) {
						switch ($token[0]) {

						case T_COMMENT: // read comment
						case T_ML_COMMENT: // and multiline comment (deprecated in newer versions)
						case T_DOC_COMMENT: // and catch PHP5 doc comment token too
							$currentData = $this->processDocComment($token[1], $rootDoc);
							break;

						case T_CLASS:
						// read class
							$class =& new classDoc($this->_getNext($tokens, $key, T_STRING), $rootDoc); // create class object
							$this->verbose('+ Entering '.get_class($class).': '.$class->name());
							if (isset($currentData['docComment'])) { // set doc comment
								$class->set('docComment', $currentData['docComment']);
							}
							$class->set('data', $currentData); // set data
							if (isset($currentData['package']) && $currentData['package'] != NULL) { // set package
								$class->set('package', $currentData['package']);
							} else {
								$class->set('package', $currentPackage);
							}
							$parentPackage =& $rootDoc->packageNamed($class->packageName(), TRUE); // get parent package
							$parentPackage->addClass($class); // add class to package
							$class->setByRef('parent', $parentPackage); // set parent reference
							$currentData = array(); // empty data store
							if ($this->_includeElements($class)) {
								$currentElement[count($currentElement)] =& $class; // re-assign current element
							}
							$ce =& $class;
							break;
							
						case T_INTERFACE:
						// read interface
							$interface =& new classDoc($this->_getNext($tokens, $key, T_STRING), $rootDoc); // create interface object
							$this->verbose('+ Entering '.get_class($interface).': '.$interface->name());
							if (isset($currentData['docComment'])) { // set doc comment
								$interface->set('docComment', $currentData['docComment']);
							}
							$interface->set('data', $currentData); // set data
							$interface->set('interface', TRUE); // this element is an interface
							if (isset($currentData['package']) && $currentData['package'] != NULL) { // set package
								$interface->set('package', $currentData['package']);
							} else {
								$interface->set('package', $currentPackage);
							}
							$parentPackage =& $rootDoc->packageNamed($interface->packageName(), TRUE); // get parent package
							$parentPackage->addClass($interface); // add class to package
							$interface->setByRef('parent', $parentPackage); // set parent reference
							$currentData = array(); // empty data store
							if ($this->_includeElements($interface)) {
								$currentElement[count($currentElement)] =& $interface; // re-assign current element
							}
							$ce =& $interface;
							break;

						case T_EXTENDS:
						// get extends clause
                            $superClassName = $this->_getNext($tokens, $key, T_STRING);
							$ce->set('superclass', $superClassName);
                            if ($superClass =& $rootDoc->classNamed($superClassName) && $commentTag =& $superClass->tags('@text')) {
                                $ce->setTag('@text', $commentTag);
                            }
							break;

						case T_IMPLEMENTS:
						// get implements clause
							while($tokens[++$key] != '{') {
								if ($tokens[$key][0] == T_STRING) {
                                    $interface =& $rootDoc->classNamed($tokens[$key][1]);
                                    if ($interface) {
                                        $ce->set('interfaces', $interface);
                                    }
								}
							}
							break;
							
						case T_THROW:
						// throws exception
							$className = $this->_getNext($tokens, $key, T_STRING);
							$class =& $rootDoc->classNamed($className);
							if ($class) {
								$ce->setByRef('throws', $class);
							} else {
								$ce->set('throws', $className);
							}
							break;

						case T_PRIVATE:
							$currentData['access'] = 'private';
							break;
							
						case T_PROTECTED:
							$currentData['access'] = 'protected';
							break;
							
						case T_PUBLIC:
							$currentData['access'] = 'public';
							break;
							
						case T_ABSTRACT:
							$currentData['abstract'] = TRUE;
							break;
							
						case T_FINAL:
							$currentData['final'] = TRUE;
							break;
							
						case T_STATIC:
							$currentData['static'] = TRUE;
							break;
							
						case T_VAR:
							$currentData['var'] = 'var';
							break;
							
						case T_CONST:
							$currentData['var'] = 'const';
							break;

						case T_FUNCTION:
						// read function
							$method =& new methodDoc($this->_getNext($tokens, $key, T_STRING), $ce, $rootDoc); // create method object
							$this->verbose('+ Entering '.get_class($method).': '.$method->name());
							if (isset($currentData['docComment'])) { // set doc comment
								$method->set('docComment', $currentData['docComment']); // set doc comment
							}
							$method->set('data', $currentData); // set data
                            $ceClass = strtolower(get_class($ce));
							if ($ceClass == 'rootdoc') { // global function, add to package
								$this->verbose(' is a global function');
								if (isset($currentData['package']) && $currentData['package'] != NULL) { // set package
									$method->set('package', $currentData['package']);
								} else {
									$method->set('package', $currentPackage);
								}
								$parentPackage =& $rootDoc->packageNamed($method->packageName(), TRUE); // get parent package
								$parentPackage->addFunction($method); // add method to package
							} elseif ($ceClass == 'classdoc' || $ceClass == 'methoddoc') { // class method, add to class
								$method->set('package', $ce->packageName()); // set package
								if ($method->name() == '__construct' || strtolower($method->name()) == strtolower($ce->name())) { // constructor
									$this->verbose(' is a constructor of '.get_class($ce).' '.$ce->name());
                                    // Will Gilbert (gilbert@informagen.com) - Write out the constuctor as the class's name rather than '__construct'
                                    if ($method->name() == '__construct') $method->set("name", $ce->name()); 
									$ce->addConstructor($method);
								} else {
									if ($this->_hasPrivateName($method->name())) $method->makePrivate();
									$this->verbose(' is a method of '.get_class($ce).' '.$ce->name());
									if ($this->_includeElements($method)) {
										$ce->addMethod($method);
									}
								}
							}
							$currentData = array(); // empty data store
							$currentElement[count($currentElement)] =& $method; // re-assign current element
							$ce =& $method;
							break;

						case T_STRING:
						// read global constant
							if ($token[1] == 'define' && $tokens[$key + 2][0] == T_CONSTANT_ENCAPSED_STRING) {
								$const =& new fieldDoc($tokens[$key + 2][1], $ce, $rootDoc); // create constant object
								$this->verbose('Found '.get_class($const).': global constant '.$const->name());
								$const->set('final', TRUE); // is constant
								$value = '';
								$key = $key + 4;
								while($tokens[$key] != ';') {
									if (is_array($tokens[$key])) {
										$value .= $tokens[$key][1];
									} else {
										$value .= $tokens[$key];
									}
									$key++;
								}
								$const->set('value', substr(trim($value), 0, -1));
								unset($value);
								if (isset($currentData['docComment'])) { // set doc comment
									$const->set('docComment', $currentData['docComment']);
								}
								$const->set('data', $currentData); // set data
								if (isset($currentData['package'])) { // set package
									$const->set('package', $currentData['package']);
								} else {
									$const->set('package', $currentPackage);
								}
								$const->mergeData();
								$parentPackage =& $rootDoc->packageNamed($const->packageName(), TRUE); // get parent package
								if ($this->_includeElements($const)) {
									$parentPackage->addGlobal($const); // add constant to package
								}
								$currentData = array(); // empty data store
								
							// member constant
							} elseif (isset($currentData['var']) && $currentData['var'] == 'const') {
								do {
									$key++;
									if ($tokens[$key] == '=') {
										$value = '';
									} elseif ($tokens[$key] == ',' || $tokens[$key] == ';') {
										$const =& new fieldDoc($this->_getPrev($tokens, $key, array(T_VARIABLE, T_STRING)), $ce, $rootDoc); // create field object
										$this->verbose('Found '.get_class($const).': '.$const->name());
										if ($this->_hasPrivateName($const->name())) $const->makePrivate();
										$const->set('final', TRUE);
										if (isset($value)) $const->set('value', trim($value)); // set value
										if (isset($currentData['docComment'])) { // set doc comment
											$const->set('docComment', $currentData['docComment']);
										}
										$const->set('data', $currentData); // set data
										$const->set('package', $ce->packageName()); // set package
										$this->verbose(' is a member constant of '.get_class($ce).' '.$ce->name());
										$const->mergeData();
										if ($this->_includeElements($const)) {
											$ce->addField($const);
										}
										unset($value);
									} elseif(isset($value)) { // set value
										if (is_array($tokens[$key])) {
											$value .= $tokens[$key][1];
										} else {
											$value .= $tokens[$key];
										}
									}
								} while($tokens[$key] != ';');
								$currentData = array(); // empty data store

							// function parameter
							} elseif (strtolower(get_class($ce)) == 'methoddoc' && $ce->inBody == 0) {
								do {
									$key++;
									if ($tokens[$key] == ',' || $tokens[$key] == ')') {
										unset($param);
									} elseif (is_array($tokens[$key])) {
										if ($tokens[$key][0] == T_VARIABLE && !isset($param)) {
											$param =& new fieldDoc($tokens[$key][1], $ce, $rootDoc); // create constant object
											$this->verbose('Found '.get_class($param).': '.$param->name());
											if (isset($currentData['docComment'])) { // set doc comment
												$param->set('docComment', $currentData['docComment']);
											}
											$param->set('data', $currentData); // set data
											$param->set('package', $ce->packageName()); // set package
											$this->verbose(' is a parameter of '.get_class($ce).' '.$ce->name());
											$param->mergeData();
											$ce->addParameter($param);
										} elseif(isset($param) && ($tokens[$key][0] == T_STRING || $tokens[$key][0] == T_CONSTANT_ENCAPSED_STRING)) { // set value
											$param->set('value', $tokens[$key][1]);
										}
									}
								} while($tokens[$key] != ')');
								$currentData = array(); // empty data store
							}
							break;

						case T_VARIABLE:
							// read global variable
							if (strtolower(get_class($ce)) == 'rootdoc') { // global var, add to package
								$global =& new fieldDoc($tokens[$key][1], $ce, $rootDoc); // create constant object
								$this->verbose('Found '.get_class($global).': global variable '.$global->name());
								if (isset($tokens[$key - 1][0]) && isset($tokens[$key - 2][0]) && $tokens[$key - 2][0] == T_STRING && $tokens[$key - 1][0] == T_WHITESPACE) {
									$global->set('type', new type($tokens[$key - 2][1], $rootDoc));
								}
                                while (isset($tokens[$key]) && $tokens[$key] != '=' && $tokens[$key] != ';') {
                                    $key++;
                                }
                                if (isset($tokens[$key]) && $tokens[$key] == '=') {
									$default = '';
									$key2 = $key + 1;
									do {
										if (is_array($tokens[$key2])) {
											if ($tokens[$key2][1] != '=') $default .= $tokens[$key2][1];
										} else {
											if ($tokens[$key2] != '=') $default .= $tokens[$key2];
										}
										$key2++;
									} while(isset($tokens[$key2]) && $tokens[$key2] != ';' && $tokens[$key2] != ',' && $tokens[$key2] != ')');
									$global->set('value', trim($default, ' ()')); // set value
								}
								if (isset($currentData['docComment'])) { // set doc comment
									$global->set('docComment', $currentData['docComment']);
								}
								$global->set('data', $currentData); // set data
								if (isset($currentData['package'])) { // set package
									$global->set('package', $currentData['package']);
								} else {
									$global->set('package', $currentPackage);
								}
								$global->mergeData();
								$parentPackage =& $rootDoc->packageNamed($global->packageName(), TRUE); // get parent package
								if ($this->_includeElements($global)) {
									$parentPackage->addGlobal($global); // add constant to package
								}
								$currentData = array(); // empty data store
                                
						// read member variable
							} elseif (
								(isset($currentData['var']) && $currentData['var'] == 'var') || 
								(isset($currentData['access']) && ($currentData['access'] == 'public' || $currentData['access'] == 'protected' || $currentData['access'] == 'private'))
							) {
								do {
									$key++;
									if ($tokens[$key] == '=') {
										$value = '';
									} elseif ($tokens[$key] == ',' || $tokens[$key] == ';') {
										$field =& new fieldDoc($this->_getPrev($tokens, $key, T_VARIABLE), $ce, $rootDoc); // create field object
										$this->verbose('Found '.get_class($field).': '.$field->name());
										if ($this->_hasPrivateName($field->name())) $field->makePrivate();
										if (isset($value)) $field->set('value', trim($value)); // set value
										if (isset($currentData['docComment'])) { // set doc comment
											$field->set('docComment', $currentData['docComment']);
										}
										$field->set('data', $currentData); // set data
										$field->set('package', $ce->packageName()); // set package
										$this->verbose(' is a member variable of '.get_class($ce).' '.$ce->name());
										$field->mergeData();
										if ($this->_includeElements($field)) {
											$ce->addField($field);
										}
										unset($value);
									} elseif(isset($value)) { // set value
										if (is_array($tokens[$key])) {
											$value .= $tokens[$key][1];
										} else {
											$value .= $tokens[$key];
										}
									}
								} while($tokens[$key] != ';');
								$currentData = array(); // empty data store

							}
							break;

						case T_CURLY_OPEN:
						case T_DOLLAR_OPEN_CURLY_BRACES: // we must catch this so we don't accidently step out of the current block
							$open_curly_braces = TRUE;
							break;
						}

					} else { // primitive tokens
					
						switch ($token) {
						case '{':
							if (!$in_parsed_string) {
								$ce->inBody++;
							}
							break;
						case '}':
							if (!$in_parsed_string) {
								if ($open_curly_braces) { // end of var curly brace syntax
									$open_curly_braces = FALSE;
								} else {
									$ce->inBody--;
									if ($ce->inBody == 0 && count($currentElement) > 0) {
										$ce->mergeData();
										$this->verbose('- Leaving '.get_class($ce).': '.$ce->name());
										array_pop($currentElement); // re-assign current element
										if (count($currentElement) > 0) {
											$ce =& $currentElement[count($currentElement) - 1];
										} else {
											unset($ce);
											$ce =& $rootDoc;
										}
									}
								}
							}
							break;
						case ';': // case for closing abstract functions
							if (!$in_parsed_string && $ce->inBody == 0 && count($currentElement) > 0) {
								$ce->mergeData();
								$this->verbose('- Leaving empty '.get_class($ce).': '.$ce->name());
								array_pop($currentElement); // re-assign current element
								if (count($currentElement) > 0) {
									$ce =& $currentElement[count($currentElement) - 1];
								} else {
									unset($ce);
									$ce =& $rootDoc;
								}
							}
							break;
						case '"': // catch parsed strings so as to ignore tokens within
							$in_parsed_string = !$in_parsed_string;
							break;
						}
					}
                    
                    $counter++;
                    if ($counter > 99) {
                        if (!$this->_verbose) echo '.';
                        $counter = 0;
                    }
				}
                if (!$this->_verbose) echo "\n";

			} else {
				$this->error('Could not read file "'.$filename.'"');
				exit;
			}
		}
        
        // add parent data to child elements
        $this->message('Merging superclass data');
        $this->_mergeSuperClassData($rootDoc);
		
		return $rootDoc;
	}

	/** Loads and runs the doclet.
	 *
	 * @param RootDoc rootDoc
	 * @return bool
	 */
	function execute(&$rootDoc)
    {
		$docletFile = $this->fixPath($this->_docletPath).$this->_doclet.'/'.$this->_doclet.'.php';
		if (is_file($docletFile)) { // load doclet
			$this->message('Loading doclet "'.$this->_doclet.'"');
			require_once($docletFile);
			$doclet =& new $this->_doclet($rootDoc);
		} else {
			$this->error('Could not find doclet "'.$docletFile.'"');
		}
		$this->message('Done ('.round($this->_getTime() - $this->_startTime, 2).' seconds)');
	}
    
    /**
     * @param rootDoc rootDoc
     * @param str parent
     */
    function _mergeSuperClassData(&$rootDoc, $parent = NULL)
    {
        $classes =& $rootDoc->classes();
        foreach ($classes as $name => $class) {
            if ($classes[$name]->superclass() == $parent) {
                $classes[$name]->mergeSuperClassData();
                $this->_mergeSuperClassData($rootDoc, $classes[$name]->name());
            }
        }
    }

	/**
	 * Recursively merge two arrays into a single array. This differs
	 * from the PHP function array_merge_recursive as it replaces values
	 * with the same index from the first array with items from the
	 * second.
	 *
	 * @param mixed[] one Array one
	 * @param mixed[] two Array two
	 *
	 * @return mixed[] Merged array
	 */
	function _mergeArrays($one, $two)
    {
		foreach ($two as $key => $item) {
			if (isset($one[$key]) && is_array($one[$key]) && is_array($item)) {
				$one[$key] = $this->_mergeArrays($one[$key], $item);
			} else {
				$one[$key] = $item;
			}
		}
		return $one;
	}
	
	/**
	 * Get next token of a certain type from token array
	 *
	 * @param str[] tokens Token array to search
	 * @param int key Key to start searching from
	 * @param int whatToGet Type of token to look for
	 * @return str Value of found token
	 */
	function _getNext(&$tokens, $key, $whatToGet)
    {
		$key++;
		if (!is_array($whatToGet)) $whatToGet = array($whatToGet);
		while(!is_array($tokens[$key]) || !in_array($tokens[$key][0], $whatToGet)) {
			$key++;
			if (!isset($tokens[$key])) return FALSE;
		}
		return $tokens[$key][1];
	}

	/**
	 * Get previous token of a certain type from token array
	 *
	 * @param str[] tokens Token array to search
	 * @param int key Key to start searching from
	 * @param int whatToGet Type of token to look for
	 * @return str Value of found token
	 */
	function _getPrev(&$tokens, $key, $whatToGet)
    {
		$key--;
		if (!is_array($whatToGet)) $whatToGet = array($whatToGet);
		while(!is_array($tokens[$key]) || !in_array($tokens[$key][0], $whatToGet)) {
			$key--;
			if (!isset($tokens[$key])) return FALSE;
		}
		return $tokens[$key][1];
	}

	/**
	 * Process a doc comment into a doc tag array.
	 *
	 * @param str comment The comment to process
	 * @param RootDoc root The root object
	 * @return mixed[] Array of doc comment data
	 */
	function processDocComment($comment, &$root)
    {
		if (substr(trim($comment), 0, 3) != '/**') return FALSE; // not doc comment, abort

		$data = array(
			'docComment' => $comment,
			'tags' => array()
		);
		
		$explodedComment = preg_split('/[\n|\r][ \r\n\t\/]*\*[ \t]*@/', "\n".$comment);
		
		$text = trim(array_shift($explodedComment), "\r\n \t/*");
		if ($text != '') {
			$data['tags']['@text'] = $this->createTag('@text', $text, $data, $root);
		}
		
		foreach ($explodedComment as $tag) { // process tags
			$tag = trim($tag, "\n\r \t/*"); // strip whitespace and asterix's from beginning
			
			$pos = strpos($tag, ' ');
			if ($pos !== FALSE) {
				$name = trim(substr($tag, 0, $pos));
				$text = trim(substr($tag, $pos + 1), "\n\r \t");
			} else {
				$name = $tag;
				$text = NULL;
			}
			switch ($name) {
			case 'package': // place current element in package
				$data['package'] = $text;
				break;
			case 'var': // set variable type
				$data['type'] = $text;
				break;
			case 'access': // set access permission
				$data['access'] = $text;
				break;
			case 'final': // element is final
				$data['final'] = TRUE;
				break;
			case 'abstract': // element is abstract
				$data['abstract'] = TRUE;
				break;
			case 'static': // element is static
				$data['static'] = TRUE;
				break;
			default: //create tag
				$name = '@'.$name;
				if (isset($data['tags'][$name])) {
					if (is_array($data['tags'][$name])) {
						$data['tags'][$name][] = $this->createTag($name, $text, $data, $root);
					} else {
						$data['tags'][$name] = array($data['tags'][$name], $this->createTag($name, $text, $data, $root));
					}
				} else {
					$data['tags'][$name] =& $this->createTag($name, $text, $data, $root);
				}
			}
		}
		return $data;
	}

	/**
	 * Create a tag. This method first tries to load a Taglet for the given tag
	 * name, upon failing it then tries to load a PHPDoctor specialised tag
	 * class (e.g. classes/paramtag.php), if it still has not found a tag class
	 * it uses the standard tag class.
	 *
	 * @param str name The name of the tag
	 * @param str text The contents of the tag
	 * @param str[] data Reference to doc comment data array
	 * @param RootDoc root The root object
	 * @return Tag
	 */
	function &createTag($name, $text, &$data, &$root)
    {
		$class = substr($name, 1);
		if ($class) {
			$tagletFile = $this->fixPath($this->_tagletPath).substr($name, 1).'.php';
			if (is_file($tagletFile)) { // load taglet for this tag
				if (!class_exists($class)) require_once($tagletFile);
				$tag =& new $class($name, $text, $root);
				return $tag;
			} else {
				$tagFile = 'classes/'.$class.'Tag.php';
				if (is_file($tagFile)) { // load class for this tag
					$class .= 'Tag';
					if (!class_exists($class)) require_once($tagFile);
					$tag =& new $class($text, $data, $root);
					return $tag;
				} else { // create standard tag
					$tag =& new tag($name, $text, $root);
					return $tag;
				}
			}
		}
	}
	
	/**
	 * Is an element private and we are including private elements, or element is
	 * protected and we are including protected elements.
	 *
	 * @param ProgramElementDoc element The element to check
	 * @return bool
	 */
	function _includeElements(&$element)
    {
		if ($element->isGlobal() && !$element->isFinal() && !$this->_globals) {
			return FALSE;
		} elseif ($element->isGlobal() && $element->isFinal() && !$this->_constants) {
			return FALSE;
		} elseif ($this->_private) {
			return TRUE;
		} elseif ($this->_protected && ($element->isPublic() || $element->isProtected())) {
			return TRUE;
		} elseif ($this->_public && $element->isPublic()) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Does the given element name conform to the format that is used for private
	 * elements?
	 *
	 * @param str name The name to check
	 * @return bool
	 */
	function _hasPrivateName($name)
	{
		return substr($name, 0, 1) == '_';
	}
}

?>
