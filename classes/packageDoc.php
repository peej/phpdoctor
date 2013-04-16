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

/** Represents a PHP package. Provides access to information about the package,
 * the package's comment and tags, and the classes in the package.
 *
 * @package PHPDoctor
 */
class packageDoc extends Doc
{

    /** The classes in this package
     *
     * @var classDoc[]
     */
    public $_classes = array();

    /** The globals in this package
     *
     * @var fieldDoc[]
     */
    public $_globals = array();

    /** The functions in this package
     *
     * @var methodDoc[]
     */
    public $_functions = array();

    /** Constructor
     *
     * @param str name
     * @param RootDoc root
     */
    public function packageDoc($name, &$root)
    {
        $this->_name = $name;
        $this->_root =& $root;

        $phpdoctor =& $root->phpdoctor();

        // parse overview file
        $packageCommentDir = $phpdoctor->getOption('packageCommentDir');
        $packageCommentFilename = strtolower(str_replace('/', '.', $this->_name)).'.html';
        if (isset($packageCommentDir) && is_file($packageCommentDir.$packageCommentFilename)) {
            $overviewFile = $packageCommentDir.$packageCommentFilename;
        } else {
            $pos = strrpos(str_replace('\\', '/', $phpdoctor->_currentFilename), '/');
            if ($pos !== FALSE) {
                $overviewFile = substr($phpdoctor->_currentFilename, 0, $pos).'/package.html';
            } else {
                $overviewFile = $phpdoctor->sourcePath().$this->_name.'.html';
            }
        }
        if (is_file($overviewFile)) {
            $phpdoctor->message("\n".'Reading package overview file "'.$overviewFile.'".');
            if ($text = $this->getFileContents($overviewFile)) {
                $this->_data = $phpdoctor->processDocComment('/** '.$text.' */', $this->_root);
                $this->mergeData();
            }
        }

    }

    /** Return the package path.
     *
     * @return str
     */
    public function asPath()
    {
        return strtolower(str_replace('.', '/', str_replace('\\', '/', $this->_name)));
    }

    /** Calculate the depth of this package from the root.
     *
     * @return int
     */
    public function depth()
    {
        $depth = substr_count($this->_name, '.');
        $depth += substr_count($this->_name, '\\');
        $depth += substr_count($this->_name, '/');

        return $depth;
    }

    /** Add a class to this package.
     *
     * @param ClassDoc class
     */
    public function addClass(&$class)
    {
        if (isset($this->_classes[$class->name()])) {
            $phpdoctor =& $this->_root->phpdoctor();
            echo "\n";
            $phpdoctor->warning('Found class '.$class->name().' again, overwriting previous version');
        }
        $this->_classes[$class->name()] =& $class;
    }

    /** Add a global to this package.
     *
     * @param FieldDoc global
     */
    public function addGlobal(&$global)
    {
        if (!isset($this->_globals[$global->name()])) {
            $this->_globals[$global->name()] =& $global;
        }
    }

    /** Add a function to this package.
     *
     * @param MethodDoc function
     */
    public function addFunction(&$function)
    {
        if (isset($this->_functions[$function->name()])) {
            $phpdoctor =& $this->_root->phpdoctor();
            echo "\n";
            $phpdoctor->warning('Found function '.$function->name().' again, overwriting previous version');
        }
        $this->_functions[$function->name()] =& $function;
    }

    /** Get all included classes (including exceptions and interfaces).
     *
     * @return ClassDoc[] An array of classes
     */
    function &allClasses()
    {
        return $this->_classes;
    }

    /** Get exceptions in this package.
     *
     * @return ClassDoc[] An array of exceptions
     */
    function &exceptions()
    {
        $exceptions = NULL;
        foreach ($this->_classes as $name => $exception) {
            if ($exception->isException()) {
                $exceptions[$name] =& $this->_classes[$name];
            }
        }

        return $exceptions;
    }

    /** Get interfaces in this package.
     *
     * @return ClassDoc[] An array of interfaces
     */
    function &interfaces()
    {
        $interfaces = NULL;
        foreach ($this->_classes as $name => $interface) {
            if ($interface->isInterface()) {
                $interfaces[$name] =& $this->_classes[$name];
            }
        }

        return $interfaces;
    }

    /** Get traits in this package.
     *
     * @return ClassDoc[] An array of traits
     */
    function &traits()
    {
        $traits = NULL;
        foreach ($this->_classes as $name => $trait) {
            if ($trait->isTrait()) {
                $traits[$name] =& $this->_classes[$name];
            }
        }

        return $traits;
    }

    /** Get ordinary classes (excluding exceptions and interfaces) in this package.
     *
     * @return ClassDoc[] An array of classes
     */
    function &ordinaryClasses()
    {
        $classes = NULL;
        foreach ($this->_classes as $name => $class) {
            if ($class->isOrdinaryClass()) {
                $classes[$name] =& $this->_classes[$name];
            }
        }

        return $classes;
    }

    /** Get globals in this package.
     *
     * @return FieldDoc[] An array of globals
     */
    function &globals()
    {
        return $this->_globals;
    }

    /** Get functions in this package.
     *
     * @return MethodDoc[] An array of functions
     */
    function &functions()
    {
        return $this->_functions;
    }

    /** Lookup for a class within this package.
     *
     * @param str className Name of the class to lookup
     * @return ClassDoc A class
     */
    function &findClass($className)
    {
        $return = NULL;
        if (isset($this->_classes[$className])) {
            $return =& $this->_classes[$className];
        }

        return $return;
    }

}
