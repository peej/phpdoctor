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

/** Represents a PHP class and provides access to information about the class,
 * the class' comment and tags, and the members of the class. A classDoc only
 * exists if it was processed in this run of PHPDoctor. References to classes
 * which may or may not have been processed in this run are referred to using
 * type (which can be converted to classDoc, if possible).
 *
 * @package PHPDoctor
 */
class classDoc extends programElementDoc {

	/** The super class.
	 *
	 * @var str
	 */
	var $_superclass = NULL;

	/** Is this an interface?
	 *
	 * @var bool
	 */
	var $_interface = FALSE;

	/** The class constructor.
	 *
	 * @var constructorDoc
	 */
	var $_constructor = NULL;

	/** The class fields.
	 *
	 * @var fieldDoc[]
	 */
	var $_fields = NULL;

	/** The class methods.
	 *
	 * @var methodDoc[]
	 */
	var $_methods = array();
	
	/** Interfaces this class implements or this interface extends.
	 *
	 * @var classDoc[]
	 */
	var $_interfaces = array();
	
	/** Is this class abstract.
	 *
	 * @var bool
	 */
	var $_abstract = FALSE;
	
	/** Constructor
	 *
	 * @param str name Name of this element
	 */
	function classDoc($name) {
		$this->_name = $name;
	}

	/** Add a field to this class.
	 *
	 * @param fieldDoc field
	 */
	function addField(&$field) {
		$this->_fields[$field->name()] =& $field;
	}

	/** Add a constructor to this class.
	 *
	 * @param methodDoc constructor
	 */
	function addConstructor(&$constructor) {
		$this->_constructor[$constructor->name()] =& $constructor;
	}

	/** Add a method to this class.
	 *
	 * @param methodDoc method
	 */
	function addMethod(&$method) {
		$this->_methods[$method->name()] =& $method;
	}
	
	/** Return the constructor for this class.
	 *
	 * @return constructorDoc
	 */
	function &constructor() {
		return $this->_constructor;
	}

	/** Return fields in this class.
	 *
	 * @return fieldDoc[]
	 */
	function &fields() {
		return $this->_fields;
	}

	/** Return the methods in this class.
	 *
	 * @return methodDoc[]
	 */
	function &methods() {
		return $this->_methods;
	}

	/** Return interfaces implemented by this class or interfaces extended by this interface.
	 *
	 * @return classDoc[]
	 */
	function &interfaces() {
		return $this->_interfaces;
	}

	/** Return true if this class is abstract.
	 *
	 * @return bool
	 */
	function isAbstract() {
		return $this->_abstract;
	}

	/** Return true if this element is an interface.
	 *
	 * @return bool
	 */
	function isInterface() {
		return $this->_interface;
	}

	/** Test whether this class is a subclass of the specified class.
	 *
	 * @param classDoc cd
	 * @return bool
	 */
	function subclassOf($cd) {
		if ($this->_superclass == $cd->name()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/** Return the superclass of this class.
	 *
	 * @return classDoc
	 */
	function superclass() {
		return $this->_superclass;
	}
	
	/** Is this construct a class. Note: interfaces are not classes.
	 *
	 * @return bool
	 */
	function isClass() {
		return !$this->_interface;
	}
	
	/** Is this construct an ordinary class (not an interface or an exception).
	 *
	 * @return bool
	 */
	function isOrdinaryClass() {
		if ($this->isClass() && !$this->isException()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/** Is this construct an exception.
	 *
	 * @return bool
	 */
	function isException() {
		if (strtolower($this->_superclass) == 'exception') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

?>