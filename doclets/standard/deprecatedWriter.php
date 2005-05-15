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

// $Id: deprecatedWriter.php,v 1.1 2005/05/15 15:50:52 peejeh Exp $

/** This generates the deprecated elements index.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $Revision: 1.1 $
 */
class DeprecatedWriter extends HTMLWriter
{

	/** Build the deprecated index.
	 *
	 * @param Doclet doclet
	 */
	function deprecatedWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		//$this->_id = 'definition';

		$rootDoc =& $this->_doclet->rootDoc();

        $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
        $this->_sections[1] = array('title' => 'Package');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        $this->_sections[5] = array('title' => 'Deprecated', 'selected' => TRUE);
        $this->_sections[6] = array('title' => 'Index', 'url' => 'index-all.html');
        
        $classes = array();
        foreach ($rootDoc->classes() as $class) {
            if ($class->tags('@deprecated')) {
                $classes[] = $class;
            }
        }
        $fields = array();
        $methods = array();
        foreach ($rootDoc->classes() as $class) {
            foreach ($class->fields() as $field) {
                if ($field->tags('@deprecated')) {
                    $fields[] = $field;
                }
            }
            foreach ($class->methods() as $method) {
                if ($method->tags('@deprecated')) {
                    $methods[] = $method;
                }
            }
        }
        $globals = array();
        foreach ($rootDoc->globals() as $global) {
            if ($global->tags('@deprecated')) {
                $globals[] = $global;
            }
        }
        $functions = array();
        foreach ($rootDoc->functions() as $function) {
            if ($function->tags('@deprecated')) {
                $functions[] = $function;
            }
        }
        
        ob_start();
        
        echo '<h1>Deprecated API</h1>';

        echo "<hr>\n\n";
        
        if ($classes || $fields || $methods || $globals || $functions) {
            echo "<h2>Contents</h2>\n";
            echo "<ul>\n";
            if ($classes) {
                echo '<li><a href="#deprecated_class">Deprecated Classes</a></li>';
            }
            if ($fields) {
                echo '<li><a href="#deprecated_field">Deprecated Fields</a></li>';
            }
            if ($methods) {
                echo '<li><a href="#deprecated_method">Deprecated Methods</a></li>';
            }
            if ($globals) {
                echo '<li><a href="#deprecated_global">Deprecated Globals</a></li>';
            }
            if ($functions) {
                echo '<li><a href="#deprecated_function">Deprecated Functions</a></li>';
            }
            echo "</ul>\n";
        }
        
        if ($classes) {
            echo '<table id="deprecated_class" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Deprecated Classes</th></tr>', "\n";
            foreach($classes as $class) {
                $textTag =& $class->tags('@text');
                echo '<tr><td class="name"><a href="', $class->asPath(), '">', $class->qualifiedName(), '</a></td>';
                echo '<td class="description">';
                if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                echo "</td></tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($fields) {
            echo '<table id="deprecated_field" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Deprecated Fields</th></tr>', "\n";
            foreach ($fields as $field) {
                $textTag =& $field->tags('@text');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $field->asPath(), '">', $field->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($methods) {
            echo '<table id="deprecated_method" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Deprecated Methods</th></tr>', "\n";
            foreach($methods as $method) {
                $textTag =& $method->tags('@text');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $method->asPath(), '">', $method->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($globals) {
            echo '<table id="deprecated_global" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Deprecated Globals</th></tr>', "\n";
            foreach($globals as $global) {
                $textTag =& $global->tags('@text');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $global->asPath(), '">', $global->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
		}
        
        if ($functions) {
            echo '<table id="deprecated_function" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Deprecated Functions</th></tr>', "\n";
            foreach($functions as $function) {
                $textTag =& $function->tags('@text');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $function->asPath(), '">', $function->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('deprecated-list.html', 'Deprecated', TRUE);
	
	}
  
}

?>
