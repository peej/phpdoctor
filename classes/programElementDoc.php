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
class ProgramElementDoc extends Doc
{

	/** Reference to the elements parent.
	 *
	 * @var doc
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
	
	/** Which source file is this element in
	 *
	 * @var str
	 */
	var $_filename = NULL;
	
	/** The line in the source file this element can be found at
	 *
	 * @var int
	 */
	var $_lineNumber = NULL;
	
	/** The source path containing the source file
	 *
	 * @var str
	 */
	var $_sourcePath = NULL;
	
	/** Set element to have public access */
	function makePublic()
    {
		$this->_access = 'public';
	}

	/** Set element to have protected access */
	function makeProtected()
    {
		$this->_access = 'protected';
	}

	/** Set element to have private access */
	function makePrivate()
    {
		$this->_access = 'private';
	}

	/** Get the containing class of this program element. If the element is in
	 * the global scope and does not have a parent class, this will return null.
	 *
	 * @return ClassDoc
	 */
	function &containingClass()
    {
        $return = NULL;
        if (strtolower(get_class($this->_parent)) == 'classdoc') {
            $return =& $this->_parent;
        }
        return $return;
	}

	/** Get the package that this program element is contained in.
	 *
	 * @return PackageDoc
	 */
	function &containingPackage()
    {
		return $this->_root->packageNamed($this->_package);
	}
	
	/** Get the name of the package that this program element is contained in.
	 *
	 * @return str
	 */
	function packageName()
    {
		return $this->_package;
	}

	/** Get the fully qualified name.
	 *
	 * <pre>Example:
for the method bar() in class Foo in the package Baz, return:
	Baz\Foo\bar()</pre>
	 *
	 * @return str
	 */
	function qualifiedName()
    {
		$parent =& $this->containingClass();
		if ($parent && $parent->name() != '' && $this->_package != $parent->name()) {
			return $this->_package.'\\'.$parent->name().'\\'.$this->_name;
		} else {
			return $this->_package.'\\'.$this->_name;
		}
	}

	/** Get modifiers string.
	 *
	 * <pre> Example, for:
	public abstract int foo() { ... }
modifiers() would return:
	'public abstract'</pre>
	 *
	 * @return str
	 */
	function modifiers($showPublic = TRUE)
    {
		$modifiers = '';
		if ($showPublic || $this->_access != 'public') {
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
	function isPublic()
    {
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
	function isProtected()
    {
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
	function isPrivate()
    {
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
	function isFinal()
    {
		return $this->_final;
	}
	
	/** Return true if this program element is static.
	 *
	 * @return bool
	 */	
	function isStatic()
    {
		return $this->_static;
	}
	
	/** Get the source location of this element
	 *
	 * @return str
	 */
	function location()
	{
		return $this->sourceFilename().' at line '.$this->sourceLine();
	}
	
	function sourceFilename()
	{
		$phpdoctor = $this->_root->phpdoctor();
	    return substr($this->_filename, strlen($this->_sourcePath) + 1);
	}
	
	function sourceLine()
	{
	    return $this->_lineNumber;
	}
    
	/** Return the element path.
	 *
	 * @return str
	 */
	function asPath()
    {
		if ($this->isClass() || $this->isInterface() || $this->isException()) {
			return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/'.$this->_name.'.html');
		} elseif ($this->isField()) {
			$class =& $this->containingClass();
			if ($class) {
				return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/'.$class->name().'.html#').$this->_name;
			} else {
				return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/package-globals.html#').$this->_name;
			}
		} elseif ($this->isConstructor() || $this->isMethod()) {
			$class =& $this->containingClass();
			if ($class) {
				return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/'.$class->name().'.html#').$this->_name.'()';
			} else {
				return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/package-functions.html#').$this->_name.'()';
			}
		} elseif ($this->isGlobal()) {
			return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/package-globals.html#').$this->_name;
		} elseif ($this->isFunction()) {
			return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_package)).'/package-functions.html#').$this->_name.'()';
		}
		return NULL;
	}

}

?>
