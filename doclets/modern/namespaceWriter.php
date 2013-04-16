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

/** This generates the summary of each namespace.
 *
 * @package PHPDoctor\Doclets\Modern
 */
class NamespaceWriter extends HTMLWriter
{

    /** Build the namespace summaries.
     *
     * @param Doclet doclet
     */
    public function __construct(&$doclet)
    {

        parent::__construct($doclet);

        $this->_id = 'namespace';

        $rootDoc =& $this->_doclet->rootDoc();
        $phpdoctor =& $this->_doclet->phpdoctor();

        $packages =& $rootDoc->packages();
        ksort($packages);

        foreach ($packages as $packageName => $package) {

            $this->_depth = $package->depth();

            ob_start();

            echo '<header>';
            echo '<h1>'.$this->_doclet->_docTitle.'</h1>';
            echo "<span>Namespace</span>\n\n";
            echo '<h2>', $package->name(), "</h2>\n\n";
            echo '</header>';

            $classes =& $package->allClasses();
            if ($classes) {
                ksort($classes);
                echo '<table>', "\n";
                foreach ($classes as $name => $class) {
                    $textTag =& $classes[$name]->tags('@text');
                    echo '<tr><td><a href="', str_repeat('../', $this->_depth), $classes[$name]->asPath(), '">', $classes[$name]->name(), '</a></td>';
                    echo '<td>';
                    if ($textTag) echo strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>');
                    echo "</td></tr>\n";
                }
                echo "</table>\n\n";
            }

            $isSomething = false;

            $globals = $package->globals();
            if ($globals) {
                $isSomething = true;
                echo '<h3>Globals</h3>', "\n";
                echo '<table>', "\n";
                foreach ($globals as $field) {
                    $textTag =& $field->tags('@text');
                    echo "<tr>\n";
                    echo '<td class="type">', $field->modifiers(FALSE), ' ', $field->typeAsString(), "</td>\n";
                    echo '<td class="description">';
                    echo '<p class="name"><a href="#', $field->name(), '">';
                    if (is_null($field->constantValue())) echo '$';
                    echo $field->name(), '</a></p>';
                    if ($textTag) {
                        echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
                    }
                    echo "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n\n";
            }

            $functions = $package->functions();
            if ($functions) {
                $isSomething = true;
                echo '<h3>Functions</h3>', "\n";
                echo '<table>', "\n";
                foreach ($functions as $function) {
                    $textTag =& $function->tags('@text');
                    echo "<tr>\n";
                    echo '<td class="type">', $function->modifiers(FALSE), ' ', $function->returnTypeAsString(), "</td>\n";
                    echo '<td class="description">';
                    echo '<p class="name"><a href="#', $function->name(), '()">', $function->name(), '</a>', $function->flatSignature(), '</p>';
                    if ($textTag) {
                        echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
                    }
                    echo "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n\n";
            }

            if ($isSomething) {
                echo '<h3>Details</h3>', "\n";

                if ($globals) {
                    foreach ($globals as $field) {
                        $textTag =& $field->tags('@text');
                        $type =& $field->type();
                        echo '<code class="signature" id="'.$field->name().'">', $field->modifiers(), ' ', $field->typeAsString(), ' <strong>';
                        if (is_null($field->constantValue())) echo '$';
                        echo $field->name(), '</strong>';
                        if (!is_null($field->value())) echo ' = ', htmlspecialchars($field->value());
                        echo "</code>\n";
                        echo '<div class="details">', "\n";
                        if ($textTag) {
                            echo $this->_processInlineTags($textTag);
                        }
                        $this->_processTags($field->tags());
                        echo "</div>\n\n";
                    }
                }

                if ($functions) {
                    foreach ($functions as $function) {
                        $textTag =& $function->tags('@text');
                        echo '<code class="signature" id="'.$function->name().'">', $function->modifiers(), ' ', $function->returnTypeAsString(), ' <strong>';
                        echo $function->name(), '</strong>', $function->flatSignature();
                        echo "</code>\n";
                        echo '<div class="details">', "\n";
                        if ($textTag) {
                            echo $this->_processInlineTags($textTag);
                        }
                        $this->_processTags($function->tags());
                        echo "</div>\n\n";
                    }
                }
            }

            $textTag =& $package->tags('@text');
            if ($textTag) {
                $description = $this->_processInlineTags($textTag);
                if ($description) {
                    echo '<h3>Description</h3>';
                    echo '<div class="comment">', $description, "</div>\n\n";
                }
            }

            $this->_output = ob_get_contents();
            ob_end_clean();

            $this->_write($package->asPath().'.html', $package->name(), TRUE);

        }

    }

    /**
     * Build the class tree branch for the given element
     *
     * @param ClassDoc[] tree
     * @param ClassDoc element
     */
    public function _buildTree(&$tree, &$element)
    {
        $tree[$element->name()] = $element;
        if ($element->superclass()) {
            $rootDoc =& $this->_doclet->rootDoc();
            $superclass =& $rootDoc->classNamed($element->superclass());
            if ($superclass) {
                $this->_buildTree($tree, $superclass);
            }
        }
    }

    /**
     * Build the class tree branch for the given element
     *
     * @param ClassDoc[] tree
     * @param str parent
     */
    public function _displayTree($tree, $parent = NULL)
    {
        $outputList = TRUE;
        foreach ($tree as $name => $element) {
            if ($element->superclass() == $parent) {
                if ($outputList) echo "<ul>\n";
                echo '<li><a href="', str_repeat('../', $this->_depth), $element->asPath(), '">', $element->qualifiedName(), '</a>';
                $this->_displayTree($tree, $name);
                echo "</li>\n";
                $outputList = FALSE;
            }
        }
        if (!$outputList) echo "</ul>\n";
    }

}
