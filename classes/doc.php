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

/** Abstract base class of all Doc classes. Doc item's are representations of
 * PHP language constructs (class, package, method,...) which have comments
 * and have been processed by this run of PHPDoctor.
 *
 * @package PHPDoctor
 * @abstract
 */
class doc
{
    /** The name of this construct
     *
     * @var str
     */
    public $_name = NULL;

    /** Data about the element creamed from the token stream before the object
     * for this element was created. This array contains extra data about the
     * element that occurs before the element definition in the token stream
     * (including doc comment data), it is merged with the objects fields upon
     * object completion.
     *
     * @var mixed[]
     */
    public $_data = NULL; // this must be NULL so set does not nest the arrays when $currentData is assigned

    /** The unprocessed doc comment.
     *
     * @var str
     */
    public $_docComment = '';

    /** Array of doc tags.
     *
     * @var tag[]
     */
    public $_tags = array();

    /** Whether parsing is inside this elements curly braces.
     *
     * @var int
     */
    public $inBody = 0;

    /** Reference to the root element.
     *
     * @var rootDoc
     */
    public $_root = NULL;

    /** Setter method.
     *
     * @param str member Name of the member to set
     * @param mixed value The value to set member to
     * @return bool
     */
    public function set($member, $value)
    {
        $member = '_'.$member;
        $members = get_object_vars($this);
        if (array_key_exists($member, $members)) {
            if (is_array($this->$member)) {
                $this->{$member}[] = $value;
            } else {
                $this->$member = $value;
            }

            return TRUE;
        }

        return FALSE;
    }

    /** Setter by reference method.
     *
     * @param str member Name of the member to set
     * @param mixed value The value to set member to
     * @return bool
     */
    public function setByRef($member, &$value)
    {
        $member = '_'.$member;
        $members = get_object_vars($this);
        if (array_key_exists($member, $members)) {
            if (is_array($this->$member)) {
                $this->{$member}[] =& $value;
            } else {
                $this->$member =& $value;
            }

            return TRUE;
        }

        return FALSE;
    }

    /** Return the name of this doc item.
     *
     * @return str
     */
    public function name()
    {
        return $this->_name;
    }

    /** Return tags of the specified kind in this Doc item. For example, if
     * 'tagName' has value "@serial", all tags in this Doc item of type "@serial"
     * will be returned. If NULL is given for 'tagName', all tags in this Doc
     * item are returned.
     *
     * @param str tagName Name of the tag kind to search for
     * @return Tag[] An array of Tag containing all tags of name 'tagname' or a
     * singular tag object if only one exists for the given 'tagname'
     */
    function &tags($tagName = NULL)
    {
        $return = NULL;
        if ($tagName == NULL) {
            $return =& $this->_tags;
        } elseif (isset($this->_tags[$tagName])) {
            $return =& $this->_tags[$tagName];
        }

        return $return;
    }

    /** Set a tag.
     *
     * @param str tagName Name of the tag kind to search for
     * @param Tag tag The tag to set
     */
    public function setTag($tagName, $tag)
    {
        $this->_tags[$tagName] =& $tag;
    }

    /** Return the full unprocessed text of the comment.
     *
     * @return str
     */
    public function getRawCommentText()
    {
        return $this->_docComment;
    }

    /** Is this construct a class. Note: interfaces are not classes. False until
     * overridden.
     *
     * @return bool
     */
    public function isClass()
    {
        return FALSE;
    }

    /** Is this construct a constructor. False until overridden.
     *
     * @return bool
     */
    public function isConstructor()
    {
        return FALSE;
    }

    /** Is this construct an exception. False until overridden.
     *
     * @return bool
     */
    public function isException()
    {
        return FALSE;
    }

    /** Is this construct a global variable. False until overridden.
     *
     * @return bool
     */
    public function isGlobal()
    {
        return FALSE;
    }

    /** Is this construct final. False until overridden.
     *
     * @return bool
     */
    public function isFinal()
    {
        return FALSE;
    }

    /** Is this construct a field. False until overridden.
     *
     * @return bool
     */
    public function isField()
    {
        return FALSE;
    }

    /** Is this construct a function. False until overridden.
     *
     * @return bool
     */
    public function isFunction()
    {
        return FALSE;
    }

    /** Is this construct an interface. False until overridden.
     *
     * @return bool
     */
    public function isInterface()
    {
        return FALSE;
    }

    /** Is this construct an trait. False until overridden.
     *
     * @return bool
     */
    public function isTrait()
    {
        return FALSE;
    }

    /** Is this construct a method. False until overridden.
     *
     * @return bool
     */
    public function isMethod()
    {
        return FALSE;
    }

    /** Is this construct an ordinary class (not an interface or an exception).
     * False until overridden.
     *
     * @return bool
     */
    public function isOrdinaryClass()
    {
        return FALSE;
    }

    /** Merge the contents of the doc comment into the element object. */
    public function mergeData()
    {
        if (isset($this->_data) && is_array($this->_data)) {
            // merge primitive types
            foreach ($this->_data as $member => $value) {
                if (!is_array($value)) {
                    if ($member == 'type') {
                        $this->set('type', new type($value, $this->_root));
                    } else {
                        $this->set($member, $value);
                    }
                }
            }
            // merge tags array
            if (isset($this->_data['tags']) && is_array($this->_data['tags'])) {
                $thisClass = strtolower(get_class($this));
                foreach ($this->_data['tags'] as $name => $tag) {
                    if (is_array($tag)) {
                        foreach ($this->_data['tags'][$name] as $key => $tag) {
                            if (
                                ($thisClass == 'rootdoc' && $this->_data['tags'][$name][$key]->inOverview()) ||
                                ($thisClass == 'packagedoc' && $this->_data['tags'][$name][$key]->inPackage()) ||
                                ($thisClass == 'classdoc' && $this->_data['tags'][$name][$key]->inType()) ||
                                ($thisClass == 'methoddoc' && $this->_data['tags'][$name][$key]->inMethod()) ||
                                ($thisClass == 'fielddoc' && $this->_data['tags'][$name][$key]->inField())
                            ) {
                                $this->_tags[$name][$key] =& $this->_data['tags'][$name][$key];
                                $this->_tags[$name][$key]->setParent($this);
                            }
                        }
                    } else {
                        if (
                            ($thisClass == 'rootdoc' && $this->_data['tags'][$name]->inOverview()) ||
                            ($thisClass == 'packagedoc' && $this->_data['tags'][$name]->inPackage()) ||
                            ($thisClass == 'classdoc' && $this->_data['tags'][$name]->inType()) ||
                            ($thisClass == 'methoddoc' && $this->_data['tags'][$name]->inMethod()) ||
                            ($thisClass == 'fielddoc' && $this->_data['tags'][$name]->inField())
                        ) {
                            $this->_tags[$name] =& $this->_data['tags'][$name];
                            $this->_tags[$name]->setParent($this);
                        }
                    }
                }
            }
            // merge parameter types
            if (isset($this->_parameters) && isset($this->_data['parameters'])) {
                foreach ($this->_data['parameters'] as $name => $param) {
                    if (substr($name, 0, 9) == '__unknown') {
                        $index = substr($name, 9);
                        $parameters = array_values($this->_parameters);
                        if (isset($parameters[$index])) {
                            $parameters[$index]->set('type', new type($param['type'], $this->_root));
                        }
                    } else {
                        if (!isset($this->_parameters[$name])) {
                            //phpdoctor::warning('Unknown parameter "'.$name.'" found for method "'.$this->_package.'.'.$this->_parent->name().'::'.$this->_name.'".');
                            $this->_parameters[$name] =& new fieldDoc($name, $this, $this->_root);
                            if (isset($this->_package)) $this->_parameters[$name]->set('package', $this->_package);
                        }
                        $this->_parameters[$name]->set('type', new type($param['type'], $this->_root));
                    }
                }
            }
            // merge return type
            if (isset($this->_returnType) && isset($this->_data['return'])) {
                $this->_returnType =& new type($this->_data['return'], $this->_root);
            }
            // merge exceptions
            if (isset($this->_throws) && isset($this->_data['throws'])) {
                foreach ($this->_data['throws'] as $name => $exception) {
                    $this->_throws[$name] =& $this->_data['throws'][$name];
                }
            }
        }
        // remove data array since we no longer need it
        #unset($this->_data);
    }

    /**
     * Get body of a text document
     *
     * @param str filename
     * @return str
     */
    public function getFileContents($filename)
    {
        if ($contents = file_get_contents($filename)) {
            if (preg_match('/<body ?.*?>(.+)<\/body>/s', $contents, $matches)) {
                return $matches[1];
            } else { // it's not HTML, so output it as plain text
                return $contents;
            }
        }
        return FALSE;
    }

}
