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

/** Represents a PHP program element: global, function, class, interface,
 * field, constructor, or method. This is an abstract class dealing with
 * information common to these elements.
 *
 * @package PHPDoctor
 * @abstract
 */
class programElementDoc extends doc {

	/** Reference to the elements parent.
	 *
	 * @var mixed
	 */
	var $_parent = NULL;

	/** The elements package.
	 *
	 * @var str
	 */
	var $_package = NULL;

	/** If this element is final.
	 *
	 * @var bool
	 */
	var $_final = FALSE;

	/** Access type for this element.
	 *
	 * @var str
	 */
	var $_access = 'public';

	/** If this element is static.
	 *
	 * @var bool
	 */
	var $_static = FALSE;
	
	/** Constructor
	 */
	function programElementDoc() {}

	/** Set element to have public access */
	function makePublic() {
		$this->_access = 'public';
	}

	/** Set element to have protected access */
	function makeProtected() {
		$this->_access = 'protected';
	}

	/** Set element to have private access */
	function makePrivate() {
		$this->_access = 'private';
	}

	/** Get the containing class of this program element. If the element is in
	 * the global scope and does not have a parent class, this will return null.
	 *
	 * @return classDoc
	 */
	function containingClass() {
		return $_parent;
	}

	/** Get the package that this program element is contained in.
	 *
	 * @return packageDoc
	 */
	function containingPackage() {
		return $this->_package;
	}

	/** Get the fully qualified name.
	 *
	 * <pre>Example:
for the method bar() in class Foo in the unnamed package, return:
	Foo.bar()</pre>
	 *
	 * @return str
	 */
	function qualifiedName() {}

	/** Get modifiers string.
	 *
	 * <pre> Example, for:
	public abstract int foo() { ... }
modifiers() would return:
	'public abstract'</pre>
	 *
	 * @return str
	 */
	function modifiers($showAccess = TRUE) {
		$modifiers = '';
		if ($showAccess && isset($this->_access)) {
			$modifiers .= $this->_access.' ';
		}
		if ($this->_final) {
			$modifiers .= 'final ';
		}
		if (isset($this->_abstract) && $this->_abstract) {
			$modifiers .= 'abstract ';
		}
		if ($this->_static) {
			$modifiers .= 'static ';
		}
		return $modifiers;
	}

	/** Return true if this program element is public.
	 *
	 * @return bool
	 */	
	function isPublic() {
		if ($this->_access == 'public') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/** Return true if this program element is protected.
	 *
	 * @return bool
	 */	
	function isProtected() {
		if ($this->_access == 'protected') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/** Return true if this program element is private.
	 *
	 * @return bool
	 */	
	function isPrivate() {
		if ($this->_access == 'private') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/** Return true if this program element is final.
	 *
	 * @return bool
	 */	
	function isFinal() {
		return $this->_final;
	}

	/** Return true if this program element is static.
	 *
	 * @return bool
	 */	
	function isStatic() {
		return $this->_static;
	}

}

?>