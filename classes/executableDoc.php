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

// $Id: executableDoc.php,v 1.5 2005/05/07 13:35:11 peejeh Exp $

/** Represents a PHP function, method (member function) or constructor.
 *
 * @package PHPDoctor
 * @version $Revision: 1.5 $
 * @abstract
 */
class executableDoc extends programElementDoc {

	/** The parameters this function takes.
	 *
	 * @var fieldDoc[]
	 */
	var $_parameters = array();
	
	/** The subfunctions this function has.
	 *
	 * @var methodDoc[]
	 */
	var $_functions = array();

	/** The exceptions this function throws.
	 *
	 * @var classDoc[]
	 */
	var $_throws = array();

	/** Add a subfunction to this function.
	 *
	 * @param methodDoc function
	 */
	function addMethod(&$function) {
		$this->_functions[$function->name()] =& $function;
	}

	/** Get argument information.
	 *
	 * @return fieldDoc[] An array of parameter, one element per argument in the
	 * order the arguments are present
	 */
	function &parameters() {
		return $this->_parameters;
	}
	
	/** Get subfunctions.
	 *
	 * @return methodDoc[] An array of subfunctions.
	 */
	function &functions() {
		return $this->_functions;
	}

	/** Return exceptions this function throws.
	 *
	 * @return classDoc[]
	 */
	function &thrownExceptions() {
		return $this->_throws;
	}

	/** Return the param tags in this function.
	 *
	 * @return tag[]
	 */
	function paramTags() {
		if (isset($this->_tags['@param'])) {
			if (is_array($this->_tags['@param'])) {
				return $this->_tags['@param'];
			} else {
				return array($this->_tags['@param']);
			}
		} else {
			return NULL;
		}
	}

	/** Return the throws tags in this function.
	 *
	 * @return type
	 */
	function throwsTags() {
		if (isset($this->_tags['@throws'])) {
			if (is_array($this->_tags['@throws'])) {
				return $this->_tags['@throws'];
			} else {
				return array($this->_tags['@throws']);
			}
		} else {
			return NULL;
		}
	}

	/** Get the signature. It is the parameter list, type is qualified.
	 *
	 * <pre>for a function
	mymethod(foo x, int y)
it will return
	(bar.foo x, int y)</pre>
	 *
	 * Recognised types are turned into HTML anchor tags to the documentation
	 * page for the class defining them.
	 *
	 * @return str
	 */
	function signature() {
		$signature = '(';
		$myPackage =& $this->containingPackage();
		foreach($this->_parameters as $param) {
			$type = $param->type();
			$classDoc =& $type->asClassDoc();
			if ($classDoc) {
				$packageDoc =& $classDoc->containingPackage();
				$signature .= '<a href="'.str_repeat('../', $myPackage->depth() + 1).$packageDoc->asPath().'/'.$classDoc->name().'.html">'.$classDoc->containingPackage().'.'.$classDoc->name().'</a> '.$param->name().$type->dimension().', ';
			} else {
				$signature .= $type->typeName().$type->dimension().', ';
			}
		}
		return substr($signature, 0, -2).')';
	}

	/** Get flat signature. Return a string which is the flat signiture of this
	 * function. It is the parameter list, type is not qualified.
	 *
	 * <pre>for a function
	mymethod(foo x, int y)
it will return
	(foo x, int y)</pre>
	 *
	 * Recognised types are turned into HTML anchor tags to the documentation
	 * page for the class defining them.
	 *
	 * @return str
	 */
	function flatSignature() {
		$signature = '';
		$myPackage =& $this->containingPackage();
		foreach($this->_parameters as $param) {
			$type =& $param->type();
			$classDoc =& $type->asClassDoc();
			if ($classDoc) {
				$packageDoc =& $classDoc->containingPackage();
				$signature .= '<a href="'.str_repeat('../', $myPackage->depth() + 1).$packageDoc->asPath().'/'.$classDoc->name().'.html">'.$classDoc->name().'</a> '.$param->name().', ';
			} else {
				$signature .= $type->typeName().' '.$param->name().', ';
			}
		}
		return '('.substr($signature, 0, -2).')';
	}

}

?>