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
 * @package PHPDoctor.Tags
 */
class seeTag extends tag {

	/** The link.
	 *
	 * @var str
	 */
	var $_link = NULL;

	/** Constructor
	 */
	function seeTag($text) {
		if (preg_match('/^<a href="(.+)">(.+)<\/a>$/', $text, $matches)) {
			$this->_link = $matches[1];
			$text = $matches[2];
		} elseif (preg_match('/^(((.+\.)?.+#)?.+) ?(.*)$/', $text, $matches)) {
			$this->_link = $matches[1];
			$text = $matches[4];
		} else {
			$this->_link = NULL;
		}
		parent::tag('@see', $text);
	}
	
	/** Return true if this Taglet is used in constructor documentation. */
	function inConstructor() {
		return TRUE;
	}

	/** Return true if this Taglet is used in field documentation. */
	function inField() {
		return TRUE;
	}

	/** Return true if this Taglet is used in method documentation. */          
	function inMethod() {
		return TRUE;
	}

	/** Return true if this Taglet is used in overview documentation. */
	function inOverview() {
		return TRUE;
	}

	/** Return true if this Taglet is used in package documentation. */
	function inPackage() {
		return TRUE;
	}

	/** Return true if this Taglet is used in class or interface documentation. */
	function inType() {
		return TRUE;
	}

	/** Return true if this Taglet is an inline tag. */
	function isInlineTag() {
		return FALSE;
	}

}

?>