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

require_once('seeTag.php');

/** Represents a throws tag.
 *
 * @package PHPDoctor\Tags
 */
class ThrowsTag extends SeeTag
{

	/**
	 * Constructor
	 *
	 * @param str text The contents of the tag
	 * @param str[] data Reference to doc comment data array
	 * @param RootDoc root The root object
	 * @param TextFormatter formatter The formatter used for processing text
	 */
	function throwsTag($text, &$data, &$root, &$formatter)
    {
		$explode = preg_split('/[ \t]+/', $text);
		$this->_link = array_shift($explode);
		$data['throws'][$this->_link] = $this->_link;
		parent::tag('@throws', join(' ', $explode), $root, $formatter);
	}

	/** Get display name of this tag.
	 *
	 * @return str
	 */
	function displayName()
    {
		return 'Throws';
	}

	/** Get the plain text value of the tag.
	 *
	 * @return str
	 */
	function plainText()
    {
		return $this->_addLink($this->_plainText);
	}
	
	/** Get the value of this tag, as formatted text.
	 *
	 * @return str
	 */
	function formattedText()
    {
		return $this->_addLink($this->_formattedText);
	}
	
	/** Get the value of the tag as raw data, without any text processing applied.
	 *
	 * @return str
	 */
	function rawText()
    {
		return $this->_addLink($this->_rawText);
	}
	
	/** Get value of this tag.
	 *
	 * @param str text 	the text value, without the link
	 * @return str
	 */
	function _addLink($text)
    {
		$link = $this->_link;
		$res = '';

		$element =& $this->_resolveLink();
		if ($element && $this->_parent) {
			$package =& $this->_parent->containingPackage();
			$path = str_repeat('../', $package->depth() + 1).$element->asPath();
			$res = '<a href="'.$path.'">'.$link.'</a>';
		} elseif (preg_match('/^(https?|ftp):\/\//', $this->_link) === 1) {
			$res = '<a href="'.$this->_link.'">'.$link.'</a>';
		} else {
			$res =  $link;
		}
		
		return $res . ($text ? ' ' . $text : '');
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
		return FALSE;
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
		return FALSE;
	}

	/** Return true if this Taglet is used in package documentation.
     *
     * @return bool
     */
	function inPackage()
    {
		return FALSE;
	}

	/** Return true if this Taglet is used in class or interface documentation.
     *
     * @return bool
     */
	function inType()
    {
		return FALSE;
	}

	/** Return true if this Taglet is an inline tag.
     *
     * @return bool
     */
	function isInlineTag()
    {
		return FALSE;
	}

}

?>
