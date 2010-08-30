<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2010 Paul James <paul@peej.co.uk>

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

/** Represents a see tag.
 *
 * @package PHPDoctor\Tags
 */
class InheritDocTag extends Tag
{

	/**
	 * Constructor
	 *
	 * @param str text The contents of the tag
	 * @param str[] data Reference to doc comment data array
	 * @param RootDoc root The root object
	 */
	function inheritDocTag($text, &$data, &$root)
    {
		parent::tag('@inheritDoc', $text, $root);
	}
	
	/** Get text from super element
	 *
	 * @return str
	 */
	function text()
    {
		if ($this->_parent) {
		    if ($this->_parent->isClass() || $this->_parent->isInterface()) {
		        $superClassname = $this->_parent->superclass();
		        if ($superClassname) {
		            $superClass =& $this->_root->classNamed($superClassname);
		            if ($superClass) {
		                return $superClass->tags('@text')->text();
		            }
		        }
		    } elseif ($this->_parent->isConstructor() || $this->_parent->isMethod()) {
		        $parentClass =& $this->_parent->containingClass();
		        $superClassname = $parentClass->superclass();
		        if ($superClassname) {
		            $superClass =& $this->_root->classNamed($superClassname);
		            if ($superClass) {
		                $superMethod =& $superClass->methodNamed($this->_parent->name());
		                if ($superMethod) {
		                    return $superMethod->tags('@text')->text();
		                }
		            }
		        }
		    } elseif ($this->_parent->isField()) {
		        $parentClass =& $this->_parent->containingClass();
		        $superClassname = $parentClass->superclass();
		        if ($superClassname) {
		            $superClass =& $this->_root->classNamed($superClassname);
		            if ($superClass) {
                        $superField =& $superClass->fieldNamed($this->_parent->name());
                        if ($superField) {
                            return $superField->tags('@text')->text();
                        }
                    }
                }
		    }
		}
	}
	
	/** Return true if this Taglet is used in constructor documentation.
     *
     * @return bool
     */
	function inConstructor()
    {
		return TRUE;
	}

	/** Return true if this Taglet is used in field documentation.
     *
     * @return bool
     */
	function inField()
    {
		return TRUE;
	}

	/** Return true if this Taglet is used in method documentation.          
     *
     * @return bool
     */
	function inMethod()
    {
		return TRUE;
	}

	/** Return true if this Taglet is used in overview documentation.
     *
     * @return bool
     */
	function inOverview()
    {
		return TRUE;
	}

	/** Return true if this Taglet is used in package documentation.
     *
     * @return bool
     */
	function inPackage()
    {
		return TRUE;
	}

	/** Return true if this Taglet is used in class or interface documentation.
     *
     * @return bool
     */
	function inType()
    {
		return TRUE;
	}

	/** Return true if this Taglet is an inline tag.
     *
     * @return bool
     */
	function isInlineTag()
    {
		return TRUE;
	}

}

?>
