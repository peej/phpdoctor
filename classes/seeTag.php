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

/** Represents a see tag.
 *
 * @package PHPDoctor\Tags
 */
class seeTag extends Tag
{

    /** The link.
     *
     * @var str
     */
    public $_link = NULL;

    /**
     * Constructor
     *
     * @param str text The contents of the tag
     * @param str[] data Reference to doc comment data array
     * @param RootDoc root The root object
     */
    public function seeTag($text, &$data, &$root)
    {
        if (preg_match('/^<a href="(.+)">(.+)<\/a>$/', $text, $matches)) {
            $this->_link = $matches[1];
            $text = $matches[2];
        } elseif (preg_match('/^([^ ]+)([ \t](.*))?$/', $text, $matches)) {
            $this->_link = $matches[1];
            if (isset($matches[3])) {
                $text = $matches[3];
            }
        } else {
            $this->_link = NULL;
        }
        parent::tag('@see', $text, $root);
    }

    /** Get display name of this tag.
     *
     * @return str
     */
    public function displayName()
    {
        return 'See Also';
    }

    /** Get value of this tag.
     *
     * @param Doclet doclet
     * @return str
     */
    public function text($doclet)
    {
        $link = parent::text($doclet);
        if (!$link || $link == "\n") {
            $link = $this->_link;
        }

        return $this->_linkText($link, $doclet);
    }

    /**
     * Generate the text to go into the seeTag link
     *
     * @param str link
     * @param Doclet doclet
     */
    public function _linkText($link, $doclet)
    {
        $element =& $this->_resolveLink();
        if ($element && $this->_parent) {
            $package =& $this->_parent->containingPackage();
            $path = str_repeat('../', $package->depth() + 1).$element->asPath();

            return $doclet->formatLink($path, $link);
        } elseif (preg_match('/^(https?|ftp):\/\//', $this->_link) === 1) {
            return $doclet->formatLink($this->_link, $link);
        } else {
            return $link;
        }
    }

    /**
     * Turn the objects link text into a link to the element it refers to.
     *
     * @return ProgramElementDoc
     */
    function &_resolveLink()
    {
        $phpdoctor = $this->_root->phpdoctor();
        $matches = array();
        $return = NULL;
        $packageRegex = '[a-zA-Z0-9_\x7f-\xff .\\\\-]+';
        $labelRegex = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
        $regex = '/^\\\\?(?:('.$packageRegex.')[.\\\\])?(?:('.$labelRegex.')(?:#|::))?\$?('.$labelRegex.')(?:\(\))?$/';
        if (preg_match($regex, $this->_link, $matches)) {
            $packageName = $matches[1];
            $className = $matches[2];
            $elementName = $matches[3];

            if ($packageName) { // get package
                $package =& $this->_root->packageNamed($packageName);
                if (!$package) {
                    return $return;
                }
            }
            if ($className) { // get class
                if (isset($package)) {
                    $classes =& $package->allClasses();
                } else {
                    $classes =& $this->_root->classes();
                }
                if ($classes) {
                    foreach ($classes as $key => $class) {
                        if ($class->name() == $className) {
                            break;
                        }
                    }
                    $class =& $classes[$key];
                }
            }
            if ($elementName) { // get element
                if (isset($class)) { // from class
                    $methods =& $class->methods();
                    if ($methods) {
                        foreach ($methods as $key => $method) {
                            if ($method->name() == $elementName) {
                                $element =& $methods[$key];
                                break;
                            }
                        }
                    }
                    if (!isset($element)) {
                        $fields =& $class->fields();
                        if ($fields) {
                            foreach ($fields as $key => $field) {
                                if ($field->name() == $elementName) {
                                    $element =& $fields[$key];
                                    break;
                                }
                            }
                        }
                    }
                } elseif (isset($package)) { // from package
                    $classes =& $package->allClasses();
                    foreach ($classes as $key => $class) {
                        if ($class->name() == $elementName) {
                            $element =& $classes[$key];
                            break;
                        }
                        $methods =& $class->methods();
                        if ($methods) {
                            foreach ($methods as $key => $method) {
                                if ($method->name() == $elementName) {
                                    $element =& $methods[$key];
                                    break 2;
                                }
                            }
                        }
                        if (!isset($element)) {
                            $fields =& $class->fields();
                            if ($fields) {
                                foreach ($fields as $key => $field) {
                                    if ($field->name() == $elementName) {
                                        $element =& $fields[$key];
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                    if (!isset($element)) {
                        $functions =& $package->functions();
                        if ($functions) {
                            foreach ($functions as $key => $function) {
                                if ($function->name() == $elementName) {
                                    $element =& $functions[$key];
                                    break;
                                }
                            }
                        }
                        if (!isset($element)) {
                            $globals =& $package->globals();
                            if ($globals) {
                                foreach ($globals as $key => $global) {
                                    if ($global->name() == $elementName) {
                                        $element =& $globals[$key];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } else { // from anywhere
                    $classes =& $this->_root->classes();
                    if ($classes) {
                        foreach ($classes as $key => $class) {
                            if ($class->name() == $elementName) {
                                $element =& $classes[$key];
                                break;
                            }
                            $methods =& $class->methods();
                            if ($methods) {
                                foreach ($methods as $key => $method) {
                                    if ($method->name() == $elementName) {
                                        $element =& $methods[$key];
                                        break 2;
                                    }
                                }
                            }
                            if (!isset($element)) {
                                $fields =& $class->fields();
                                if ($fields) {
                                    foreach ($fields as $key => $field) {
                                        if ($field->name() == $elementName) {
                                            $element =& $fields[$key];
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!isset($element)) {
                        $functions =& $this->_root->functions();
                        if ($functions) {
                            foreach ($functions as $key => $function) {
                                if ($function->name() == $elementName) {
                                    $element =& $functions[$key];
                                    break;
                                }
                            }
                        }
                        if (!isset($element)) {
                            $globals =& $this->_root->globals();
                            if ($globals) {
                                foreach ($globals as $key => $global) {
                                    if ($global->name() == $elementName) {
                                        $element =& $globals[$key];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $return =& $element;
        }

        return $return;
    }

    /** Return true if this Taglet is used in constructor documentation.
     *
     * @return bool
     */
    public function inConstructor()
    {
        return TRUE;
    }

    /** Return true if this Taglet is used in field documentation.
     *
     * @return bool
     */
    public function inField()
    {
        return TRUE;
    }

    /** Return true if this Taglet is used in method documentation.
     *
     * @return bool
     */
    public function inMethod()
    {
        return TRUE;
    }

    /** Return true if this Taglet is used in overview documentation.
     *
     * @return bool
     */
    public function inOverview()
    {
        return TRUE;
    }

    /** Return true if this Taglet is used in package documentation.
     *
     * @return bool
     */
    public function inPackage()
    {
        return TRUE;
    }

    /** Return true if this Taglet is used in class or interface documentation.
     *
     * @return bool
     */
    public function inType()
    {
        return TRUE;
    }

    /** Return true if this Taglet is an inline tag.
     *
     * @return bool
     */
    public function isInlineTag()
    {
        return FALSE;
    }

}
