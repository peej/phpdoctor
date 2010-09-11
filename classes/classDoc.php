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
class ClassDoc extends ProgramElementDoc
{

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
	
	/** The class constants.
	 *
	 * @var fieldDoc[]
	 */
	var $_constants = array();
	
	/** The class fields.
	 *
	 * @var fieldDoc[]
	 */
	var $_fields = array();
	
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
	 * @param RootDoc root The root element
	 * @param str filename The filename of the source file this element is in
	 * @param int lineNumber The line number of the source file this element is at
	 * @param str sourcePath The source path containing the source file
	 */
	function classDoc($name, &$root, $filename, $lineNumber, $sourcePath)
    {
		$this->_name = $name;
		$this->_root =& $root;
		$this->_filename = $filename;
		$this->_lineNumber = $lineNumber;
		$this->_sourcePath = $sourcePath;
	}
	
	/** Add a constant to this class.
	 *
	 * @param FieldDoc field
	 */
	function addConstant(&$constant)
    {
        if (!isset($this->_constants[$constant->name()])) {
            $this->_constants[$constant->name()] =& $constant;
        }
	}
	
	/** Add a field to this class.
	 *
	 * @param FieldDoc field
	 */
	function addField(&$field)
    {
        if (!isset($this->_fields[$field->name()])) {
            $this->_fields[$field->name()] =& $field;
        }
	}
	
	/** Add a method to this class.
	 *
	 * @param MethodDoc method
	 */
	function addMethod(&$method)
    {
        if (isset($this->_methods[$method->name()])) {
            $phpdoctor =& $this->_root->phpdoctor();
            echo "\n";
            $phpdoctor->warning('Found method '.$method->name().' again, overwriting previous version');
        }
		$this->_methods[$method->name()] =& $method;
	}
	
	/** Return constants in this class.
	 *
	 * @return FieldDoc[]
	 */
	function &constants()
    {
		return $this->_constants;
	}
	
	/** Return fields in this class.
	 *
	 * @return FieldDoc[]
	 */
	function &fields()
    {
		return $this->_fields;
	}
	
	/** Return a field in this class.
	 *
	 * @return FieldDoc
	 */
	function &fieldNamed($fieldName)
    {
        $return = NULL;
        if (isset($this->_fields[$fieldName])) {
            $return =& $this->_fields[$fieldName];
        }
        return $return;
	}
    
	/** Return the methods in this class.
	 *
	 * @return MethodDoc[]
	 */
	function &methods($methodName = NULL)
    {
		return $this->_methods;
	}

	/** Return a method in this class.
	 *
	 * @return MethodDoc
	 */
	function &methodNamed($methodName)
    {
        $return = NULL;
        if (isset($this->_methods[$methodName])) {
            $return =& $this->_methods[$methodName];
        }
        return $return;
	}
    
	/** Return interfaces implemented by this class or interfaces extended by this interface.
	 *
	 * @return ClassDoc[]
	 */
	function &interfaces()
    {
		return $this->_interfaces;
	}

	/** Return an interface in this class.
	 *
	 * @return ClassDoc
	 */
	function &interfaceNamed($interfaceName)
    {
        $return = NULL;
        if (isset($this->_interfaces[$interfaceName])) {
            $return =& $this->_interfaces[$interfaceName];
        }
        return $return;
	}
    
	/** Return true if this class is abstract.
	 *
	 * @return bool
	 */
	function isAbstract()
    {
		return $this->_abstract;
	}

	/** Return true if this element is an interface.
	 *
	 * @return bool
	 */
	function isInterface()
    {
		return $this->_interface;
	}

	/** Test whether this class is a subclass of the specified class.
	 *
	 * @param ClassDoc cd
	 * @return bool
	 */
	function subclassOf($cd)
    {
		if ($this->_superclass == $cd->name()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/** Return the superclass of this class.
	 *
	 * @return ClassDoc
	 */
	function superclass()
    {
		return $this->_superclass;
	}
	
	/** Is this construct a class. Note: interfaces are not classes.
	 *
	 * @return bool
	 */
	function isClass()
    {
		return !$this->_interface;
	}
	
	/** Is this construct an ordinary class (not an interface or an exception).
	 *
	 * @return bool
	 */
	function isOrdinaryClass()
    {
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
	function isException()
    {
		if (strtolower($this->_superclass) == 'exception') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
    

    /**
     * Merge the details of the superclass with this class.
     * @param str superClassName
     */
	function mergeSuperClassData($superClassName = NULL)
    {
        if (!$superClassName) {
            $superClassName = $this->superclass();
        }
        if ($superClassName) {
           $parent =& $this->_root->classNamed($superClassName);
           if ($parent->superclass()) { // merge parents superclass data first by recursing
               $this->mergeSuperClassData($parent->superclass());
           }
        }
  
        if (isset($parent)) {
            $phpdoctor = $this->_root->phpdoctor();

			// merge class tags array
            $tags =& $parent->tags();
			if ($tags) {
				foreach ($tags as $name => $tag) {
                    if (!isset($this->_tags[$name])) {
                        $phpdoctor->verbose('> Merging class '.$this->name().' with tags from parent '.$parent->name());
                        if (is_array($tag)) {
                            foreach ($tags[$name] as $key => $tag) {
                                $this->_tags[$name][$key] =& $tags[$name][$key];
                                $this->_tags[$name][$key]->setParent($this);
                            }
                        } else {
                            $this->_tags[$name] =& $tags[$name];
                            $this->_tags[$name]->setParent($this);
                        }
                    }
				}
			}
            
            // merge method data
            $methods =& $this->methods();
            foreach ($methods as $name => $method) {
                $parentMethod =& $parent->methodNamed($name);
                if ($parentMethod) {
                    // tags
                    $tags =& $parentMethod->tags();
                    if ($tags) {
                        foreach ($tags as $tagName => $tag) {
                            if (!isset($methods[$name]->_tags[$tagName])) {
                                $phpdoctor->verbose('> Merging method '.$this->name().':'.$name.' with tag '.$tagName.' from parent '.$parent->name().':'.$parentMethod->name());
                                if (is_array($tag)) {
                                    foreach ($tags[$tagName] as $key => $tag) {
                                        $methods[$name]->_tags[$tagName][$key] =& $tags[$tagName][$key];
                                        $methods[$name]->_tags[$tagName][$key]->setParent($this);
                                    }
                                } else {
                                    $methods[$name]->_tags[$tagName] =& $tags[$tagName];
                                    $methods[$name]->_tags[$tagName]->setParent($this);
                                }
                            }
                        }
                    }
                    // method parameters
                    foreach($parentMethod->parameters() as $paramName => $param) {
                        if (isset($methods[$name]->_parameters[$paramName])) {
                            $type =& $methods[$name]->_parameters[$paramName]->type();
                        }
                        if (!isset($methods[$name]->_parameters[$paramName]) || $type->typeName() == 'mixed') {
                            $phpdoctor->verbose('> Merging method '.$this->name().':'.$name.' with parameter '.$paramName.' from parent '.$parent->name().':'.$parentMethod->name());
                            $paramType =& $param->type();
                            $methods[$name]->_parameters[$paramName] =& new fieldDoc($paramName, $methods[$name], $this->_root);
                            $methods[$name]->_parameters[$paramName]->set('type', new type($paramType->typeName(), $this->_root));
                        }
                    }
                    // method return type
                    if ($parentMethod->returnType() && $methods[$name]->_returnType->typeName() == 'void') {
                        $phpdoctor->verbose('> Merging method '.$this->name().':'.$name.' with return type from parent '.$parent->name().':'.$parentMethod->name());
                        $methods[$name]->_returnType = $parentMethod->returnType();
                    }
                    // method thrown exceptions
                    foreach($parentMethod->thrownExceptions() as $exceptionName => $exception) {
                        if (!isset($methods[$name]->_throws[$exceptionName])) {
                            $phpdoctor->verbose('> Merging method '.$this->name().':'.$name.' with exception '.$exceptionName.' from parent '.$parent->name().':'.$parentMethod->name());
                            $methods[$name]->_throws[$exceptionName] =& $exception;
                        }
                    }
                }
            }
            
        }
    }

}

?>
