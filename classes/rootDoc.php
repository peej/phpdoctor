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

// $Id: rootDoc.php,v 1.7 2005/05/07 13:35:11 peejeh Exp $

/** This class holds the information from one run of PHPDoctor. Particularly
 * the packages, classes and options specified by the user. It is the root
 * of the parsed tokens and is passed to the doclet to be formatted into
 * output.
 *
 * @package PHPDoctor
 * @version $Revision: 1.7 $
 */
class rootDoc extends doc {

	/** Reference to the PHPDoctor application object.
	 *
	 * @var phpdoctor
	 */
	var $_phpdoctor = NULL;

	/** The parsed packages.
	 *
	 * @var packageDoc[]
	 */
	var $_packages = array();

	/** Constructor
	 *
	 * @param phpdoctor phpdoctor Application object
	 */
	function rootDoc(&$phpdoctor) {
	
		// set a reference to application object
		$this->_phpdoctor =& $phpdoctor;
		
		$overview = $phpdoctor->getOption('overview');
		// parse overview file
		if (isset($overview)) {
			if (is_file($overview)) {
				$phpdoctor->message('Reading overview file "'.$overview.'".');
				if ($html = $this->getHTMLContents($overview)) {
					$this->_data = $phpdoctor->processDocComment('/** '.$html.' */', $this);
					$this->mergeData();
				}
			} else {
				$phpdoctor->warning('Could not find overview file "'.$overview.'".');
			}
		}
	
	}
	
	/** Add a package to this root.
	 *
	 * @param packageDoc package
	 */
	function addPackage(&$package) {
		$this->_packages[$package->name()] =& $package;
	}

	/** Return a reference to the PHPDoctor application object.
	 *
	 * @return str[] An array of strings.
	 */
	function &phpdoctor() {
		return $this->_phpdoctor;
	}

	/** Return a reference to the set options.
	 *
	 * @return str[] An array of strings.
	 */
	function &options() {
		return $this->_phpdoctor->options();
	}

	/** Return a reference to the packages to be documented.
	 *
	 * @return packageDoc[]
	 */
	function &packages() {
		return $this->_packages;
	}

	/** Return a reference to the classes and interfaces to be documented.
	 *
	 * @return classDoc[]
	 */
	function &classes() {
		$classes = NULL;
		$packages = $this->packages(); // not by reference so as not to move the internal array pointer
		foreach ($packages as $name => $package) {
			$packageClasses =& $this->_packages[$name]->allClasses();
			if ($packageClasses) {
				foreach ($packageClasses as $key => $pack) {
					$classes[$name.'.'.$key] =& $packageClasses[$key];
				}
			}
		}
		if ($classes != NULL) ksort($classes);
		return $classes;
	}

	/** Return a reference to the functions to be documented.
	 *
	 * @return methodDoc[]
	 */
	function &functions() {
		$functions = NULL;
		$packages = $this->packages(); // not by reference so as not to move the internal array pointer
		foreach ($packages as $name => $package) {
			$packageFunctions =& $this->_packages[$name]->functions();
			if ($packageFunctions) {
				foreach ($packageFunctions as $key => $pack) {
					$functions[$name.'.'.$key] =& $packageFunctions[$key];
				}
			}
		}
		if ($functions != NULL) ksort($functions);
		return $functions;
	}

	/** Return a reference to the globals to be documented.
	 *
	 * @return fieldDoc[]
	 */
	function &globals() {
		$globals = NULL;
		$packages = $this->packages(); // not by reference so as not to move the internal array pointer
		foreach ($packages as $name => $package) {
			$packageGlobals =& $this->_packages[$name]->globals();
			if ($packageGlobals) {
				foreach ($packageGlobals as $key => $pack) {
					$globals[$name.'.'.$key] =& $packageGlobals[$key];
				}
			}
		}
		if ($globals != NULL) ksort($globals);
		return $globals;
	}
	
	/** Return a reference to a packageDoc for the specified package name. If a
	 * package of the requested name does not exist, this method will create the
	 * package object, add it to the root and return it.
	 *
	 * @param str name Package name
	 * @param bool create Create package if it does not exist
	 * @return packageDoc
	 */
	function &packageNamed($name, $create = FALSE) {
		if (isset($this->_packages[$name])) {
			return $this->_packages[$name];
		} elseif ($create) {
			$newPackage =& new packageDoc($name, $this);
			$this->addPackage($newPackage);
			return $newPackage;
		} else {
			return NULL;
		}
	}

	/** Return a reference to a classDoc for the specified class/interface name
	 *
	 * @param str name Class name
	 * @return classDoc
	 */
	function &classNamed($name) {
		$packages = $this->_packages; // we do this copy so as not to upset the internal pointer of the array outside this scope
		foreach($packages as $packageName => $package) {
			$class =& $package->findClass($name);
			if ($class != NULL) break;
		}
		return $class;
	}

}

?>