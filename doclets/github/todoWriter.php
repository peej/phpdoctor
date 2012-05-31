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

/** This generates the todo elements index.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class TodoWriter extends HTMLWriter
{

	/** Build the todo index.
	 *
	 * @param Doclet doclet
	 */
	function todoWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		$rootDoc =& $this->_doclet->rootDoc();

        $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
        $this->_sections[1] = array('title' => 'Namespace');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        if ($doclet->includeSource()) $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html');
        $this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
        $this->_sections[7] = array('title' => 'Todo', 'selected' => TRUE);
        $this->_sections[8] = array('title' => 'Index', 'url' => 'index-all.html');
        
        $todoClasses = array();
        $classes =& $rootDoc->classes();
        $todoFields = array();
        $todoMethods = array();
        if ($classes) {
            foreach ($classes as $class) {
                if ($class->tags('@todo')) {
                    $todoClasses[] = $class;
                }
                $fields =& $class->fields();
                if ($fields) {
                    foreach ($fields as $field) {
                        if ($field->tags('@todo')) {
                            $todoFields[] = $field;
                        }
                    }
                }
                $classes =& $class->methods();
                if ($classes) {
                    foreach ($classes as $method) {
                        if ($method->tags('@todo')) {
                            $todoMethods[] = $method;
                        }
                    }
                }
            }
        }
        $todoGlobals = array();
        $globals =& $rootDoc->globals();
        if ($globals) {
            foreach ($globals as $global) {
                if ($global->tags('@todo')) {
                    $todoGlobals[] = $global;
                }
            }
        }
        $todoFunctions = array();
        $functions =& $rootDoc->functions();
        if ($functions) {
            foreach ($functions as $function) {
                if ($function->tags('@todo')) {
                    $todoFunctions[] = $function;
                }
            }
        }
        
        ob_start();
        
        echo "<hr>\n\n";
        
        echo '<h1>Todo</h1>';

        echo "<hr>\n\n";
        
        if ($todoClasses || $todoFields || $todoMethods || $todoGlobals || $todoFunctions) {
            echo "<h2>Contents</h2>\n";
            echo "<ul>\n";
            if ($todoClasses) {
                echo '<li><a href="#todo_class">Todo Classes</a></li>';
            }
            if ($todoFields) {
                echo '<li><a href="#todo_field">Todo Fields</a></li>';
            }
            if ($todoMethods) {
                echo '<li><a href="#todo_method">Todo Methods</a></li>';
            }
            if ($todoGlobals) {
                echo '<li><a href="#todo_global">Todo Globals</a></li>';
            }
            if ($todoFunctions) {
                echo '<li><a href="#todo_function">Todo Functions</a></li>';
            }
            echo "</ul>\n";
        }
        
        if ($todoClasses) {
            echo '<table id="todo_class" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Todo Classes</th></tr>', "\n";
            foreach($todoClasses as $class) {
                $todoTag =& $class->tags('@todo');
                echo '<tr><td class="name"><a href="', $class->asPath(), '">', $class->qualifiedName(), '</a></td>';
                echo '<td class="description">';
                if ($todoTag) echo strip_tags($this->_processInlineTags($todoTag, TRUE), '<a><b><strong><u><em>');
                echo "</td></tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($todoFields) {
            echo '<table id="todo_field" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Todo Fields</th></tr>', "\n";
            foreach ($todoFields as $field) {
                $todoTag =& $field->tags('@todo');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $field->asPath(), '">', $field->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($todoTag) echo strip_tags($this->_processInlineTags($todoTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($todoMethods) {
            echo '<table id="todo_method" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Todo Methods</th></tr>', "\n";
            foreach($todoMethods as $method) {
                $todoTag =& $method->tags('@todo');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $method->asPath(), '">', $method->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($todoTag) echo strip_tags($this->_processInlineTags($todoTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }
        
        if ($todoGlobals) {
            echo '<table id="todo_global" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Todo Globals</th></tr>', "\n";
            foreach($todoGlobals as $global) {
                $todoTag =& $global->tags('@todo');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $global->asPath(), '">', $global->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($todoTag) echo strip_tags($this->_processInlineTags($todoTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
		}
        
        if ($todoFunctions) {
            echo '<table id="todo_function" class="detail">', "\n";
            echo '<tr><th colspan="2" class="title">Todo Functions</th></tr>', "\n";
            foreach($todoFunctions as $function) {
                $todoTag =& $function->tags('@todo');
                echo "<tr>\n";
                echo '<td class="name"><a href="', $function->asPath(), '">', $function->qualifiedName(), "</a></td>\n";
                echo '<td class="description">';
                if ($todoTag) echo strip_tags($this->_processInlineTags($todoTag, TRUE), '<a><b><strong><u><em>');
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('todo-list.html', 'Todo', TRUE);
	
	}
  
}

?>
