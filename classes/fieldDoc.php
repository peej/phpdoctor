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

/** Represents a PHP variable, constant or member variable (field).
 *
 * @package PHPDoctor
 */
class fieldDoc extends ProgramElementDoc
{

    /** The type of the variable.
     *
     * @var type
     */
    public $_type = NULL;

    /** The value of the variable if it is a constant.
     *
     * @var mixed
     */
    public $_value = NULL;

    /** Constructor
     *
     * @param str name Name of this element
     * @param ClassDoc|MethodDoc parent The parent of this element
     * @param RootDoc root The root element
     * @param str filename The filename of the source file this element is in
     * @param int lineNumber The line number of the source file this element is at
     * @param str sourcePath The source path containing the source file
     */
    public function fieldDoc($name, &$parent, &$root, $filename = NULL, $lineNumber = NULL, $sourcePath = NULL)
    {
        $this->_name = trim($name, '$\'"');
        $this->_parent =& $parent; // set reference to parent
        $this->_root =& $root; // set reference to root
        $this->_type =& new type('mixed', $root);
        $this->_filename = $filename;
        $this->_lineNumber = $lineNumber;
        $this->_sourcePath = $sourcePath;
    }

    /** Get type of this variable.
     *
     * @return Type
     */
    function &type()
    {
        return $this->_type;
    }

    /** Returns the value of the field.
     *
     * @return mixed
     */
    public function value()
    {
        return $this->_value;
    }

    /** Construct is a field.
     *
     * @return bool
     */
    public function isField()
    {
        return !$this->isGlobal();
    }

    /** Construct is a global.
     *
     * @return bool
     */
    public function isGlobal()
    {
        if (strtolower(get_class($this->_parent)) == 'rootdoc') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /** Format a field type for outputting. Recognised types are turned into
     * HTML anchor tags to the documentation page for the class defining them.
     *
     * @return str The string representation of the field type
     */
    public function typeAsString()
    {
        $myPackage =& $this->containingPackage();
        $classDoc =& $this->_type->asClassDoc();
        if ($classDoc) {
            $packageDoc =& $classDoc->containingPackage();

            return '<a href="'.str_repeat('../', $myPackage->depth() + 1).$classDoc->asPath().'">'.$classDoc->name().$this->_type->dimension().'</a>';
        } else {
            return $this->_type->typeName().$this->_type->dimension();
        }
    }

    /** Returns the value of the constant.
     *
     * @return str
     */
    public function constantValue()
    {
        if ($this->_final) {
            return $this->_value;
        } else {
            return NULL;
        }
    }

}
