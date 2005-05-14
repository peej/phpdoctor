<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2005 Paul James <paul@peej.co.uk>

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

// $Id: indexWriter.php,v 1.1 2005/05/14 20:49:03 peejeh Exp $

/** This generates the element index.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $Revision: 1.1 $
 */
class IndexWriter extends HTMLWriter
{

	/** Build the element index.
	 *
	 * @param Doclet doclet
	 */
	function indexWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		//$this->_id = 'definition';

		$rootDoc =& $this->_doclet->rootDoc();

        $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
        $this->_sections[1] = array('title' => 'Package');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        //$this->_sections[5] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
        $this->_sections[6] = array('title' => 'Index', 'selected' => TRUE);
        
        $classes =& $rootDoc->classes();
        $methods = array();
        foreach ($classes as $class) {
            $methods = array_merge($methods, $class->methods());
        }
        $functions =& $rootDoc->functions();
        $globals =& $rootDoc->globals();
        
        $elements = array_merge($classes, $methods, $functions, $globals);
        ksort($elements);

        ob_start();

        $letter = 64;
        foreach ($elements as $element) {
            $firstChar = strtoupper(substr($element->name(), 0, 1));
            if (is_object($element) && $firstChar != chr($letter)) {
                $letter = ord($firstChar);
                echo '<a href="#_', chr($letter), '_">', chr($letter), '</a> ';
            }
        }

        echo "<hr>\n\n";
        
        echo '<dl>';
        foreach ($elements as $element) {
            if (is_object($element)) {
                if (strtoupper(substr($element->name(), 0, 1)) != chr($letter)) {
                    $letter = ord(strtoupper(substr($element->name(), 0, 1)));
                    echo '</dl>';
                    echo '<h1 id="_', chr($letter), '_">', chr($letter), '</h1> ';
                    echo '<dl>';
                }
                switch (get_class($element)) {
                case 'classdoc':
                    if ($element->isOrdinaryClass()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Class ', $element->qualifiedName(), '</dt>';
                    } elseif ($element->isInterface()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Interface ', $element->qualifiedName(), '</dt>';
                    } elseif ($element->isException()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Exception ', $element->qualifiedName(), '</dt>';
                    }
                    break;
                case 'methoddoc':
                    if ($element->isMethod()) {
                        $parent =& $element->containingClass();
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Method in class ', $parent->qualifiedName(), '</dt>';
                    } elseif ($element->isFunction()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Function ', $element->qualifiedName(), '</dt>';
                    }
                    break;
                case 'fielddoc':
                    if ($element->isGlobal()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Global ', $element->qualifiedName(), '</dt>';
                    }
                    break;
                }
                if ($textTag =& $element->tags('@text') && $firstSentenceTags =& $textTag->firstSentenceTags()) {
                    echo '<dd>';
                    foreach ($firstSentenceTags as $firstSentenceTag) {
                        echo $firstSentenceTag->text();
                    }
                    echo '</dd>';
                }
            }
        }
        echo '</dl>';
                
        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('index-all.html', 'Index', TRUE);
	
	}

}

?>
