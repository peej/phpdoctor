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

/** Represents a documentation tag, e.g. @since, @author, @version. Given a tag
 * (e.g. "@since 1.2"), holds tag name (e.g. "@since") and tag text (e.g.
 * "1.2"). Tags with structure or which require special processing are handled
 * by subclasses.
 *
 * @package PHPDoctor.Tags
 */
class tag {

	/** The name of the tag.
	 *
	 * @var str
	 */
	var $_name = NULL;

	/** The value of the tag.
	 *
	 * @var str
	 */
	var $_text = NULL;

	/** Constructor
	 */
	function tag($name, $text) {
		$this->_name = $name;
		foreach(explode("\n", $text) as $line) {
			if ($this->_text) {
				$this->_text .= ' '.trim($line, "\n\r\t */");
			} else {
				$this->_text = trim($line, "\n\r\t */");
			}
		}
	}

	/** Get name of this tag.
	 *
	 * @return str
	 */
	function name() {
		return $this->_name;
	}

	/** Get display name of this tag.
	 *
	 * @return str
	 */
	function displayName() {
		return ucfirst(substr($this->_name, 1));
	}

	/** Get value of this tag.
	 *
	 * @return str
	 */
	function text() {
		return $this->_text;
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