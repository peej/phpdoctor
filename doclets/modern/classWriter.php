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

/** This generates the HTML API documentation for each individual interface
 * and class.
 *
 * @package PHPDoctor\Doclets\Modern
 */
class ClassWriter extends HTMLWriter
{

    /** Build the class definitons.
     *
     * @param Doclet doclet
     */
    public function __construct(&$doclet)
    {

        parent::HTMLWriter($doclet);

        $this->_id = 'definition';

        $rootDoc =& $this->_doclet->rootDoc();
        $phpdoctor =& $this->_doclet->phpdoctor();

        $packages =& $rootDoc->packages();
        ksort($packages);

        foreach ($packages as $packageName => $package) {

            $this->_depth = $package->depth() + 1;

            $classes =& $package->allClasses();

            if ($classes) {
                ksort($classes);
                foreach ($classes as $name => $class) {

                    ob_start();

                    echo '<header>';
                    echo '<h1>'.$this->_doclet->_docTitle.'</h1>';
                    if ($class->isInterface()) {
                        echo "<span>Interface</span>\n\n";
                    } elseif ($class->isTrait()) {
                        echo "<span>Trait</span>\n\n";
                    } else {
                        echo "<span>Class</span>\n\n";
                    }
                    echo '<h2>', $class->qualifiedName(), "</h2>\n\n";
                    echo '</header>';

                    $implements =& $class->interfaces();
                    if (count($implements) > 0) {
                        echo "<dl>\n";
                        echo "<dt>All Implemented Interfaces:</dt>\n";
                        echo '<dd>';
                        foreach ($implements as $interface) {
                            echo '<a href="', str_repeat('../', $this->_depth), $interface->asPath(), '">';
                            if ($interface->packageName() != $class->packageName()) {
                                echo $interface->packageName(), '\\';
                            }
                            echo $interface->name(), '</a> ';
                        }
                        echo "</dd>\n";
                        echo "</dl>\n\n";
                    }

                    $traits =& $class->traits();
                    if (count($traits) > 0) {
                        echo "<dl>\n";
                        echo "<dt>All Used Traits:</dt>\n";
                        echo '<dd>';
                        foreach ($traits as $trait) {
                            echo '<a href="', str_repeat('../', $this->_depth), $trait->asPath(), '">';
                            if ($trait->packageName() != $class->packageName()) {
                                echo $trait->packageName(), '\\';
                            }
                            echo $trait->name(), '</a> ';
                        }
                        echo "</dd>\n";
                        echo "</dl>\n\n";
                    }

                    $subclasses = $class->subclasses();
                    if ($subclasses) {
                        echo "<dl>\n";
                        echo "<dt>All Known Subclasses:</dt>\n";
                        echo '<dd>';
                        foreach ($subclasses as $subclass) {
                            echo '<a href="', str_repeat('../', $this->_depth), $subclass->asPath(), '">';
                            if ($subclass->packageName() != $class->packageName()) {
                                echo $subclass->packageName(), '\\';
                            }
                            echo $subclass->name(), '</a> ';
                        }
                        echo "</dd>\n";
                        echo "</dl>\n\n";
                    }

                    if ($class->isInterface()) {
                        echo '<p class="signature">', $class->modifiers(), ' interface <strong>', $class->name(), '</strong>';
                    } elseif ($class->isTrait()) {
                        echo '<p class="signature">', $class->modifiers(), ' trait <strong>', $class->name(), '</strong>';
                    } else {
                        echo '<p class="signature">', $class->modifiers(), ' class <strong>', $class->name(), '</strong>';
                    }
                    if ($class->superclass()) {
                        $superclass =& $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            echo ' extends <a href="', str_repeat('../', $this->_depth), $superclass->asPath(), '">', $superclass->name(), "</a>\n\n";
                        } else {
                            echo ' extends ', $class->superclass(), "\n\n";
                        }
                    }
                    echo "</p>\n\n";

                    $textTag =& $class->tags('@text');
                    if ($textTag) {
                        echo '<div class="comment">', $this->_processInlineTags($textTag), "</div>\n\n";
                    }

                    $this->_processTags($class->tags());

                    $constants =& $class->constants();
                    ksort($constants);
                    $fields =& $class->fields();
                    ksort($fields);
                    $methods =& $class->methods();
                    ksort($methods);

                    $isSomething = false;

                    if ($constants) {
                        $isSomething = true;
                        echo '<h3>Constants</h3>', "\n";
                        echo '<table>', "\n";
                        foreach ($constants as $field) {
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

                    if ($fields) {
                        $isSomething = true;
                        echo '<h3>Fields</h3>', "\n";
                        echo '<table>', "\n";
                        foreach ($fields as $field) {
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

                    if ($class->superclass()) {
                        $superclass =& $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $isSomething = true;
                            $this->inheritFields($superclass, $rootDoc, $package);
                        }
                    }

                    if ($methods) {
                        $isSomething = true;
                        echo '<h3>Methods</h3>', "\n";
                        echo '<table>', "\n";
                        foreach ($methods as $method) {
                            $textTag =& $method->tags('@text');
                            echo "<tr>\n";
                            echo '<td class="type">', $method->modifiers(FALSE), ' ', $method->returnTypeAsString(), "</td>\n";
                            echo '<td class="description">';
                            echo '<p class="name"><a href="#', $method->name(), '()">', $method->name(), '</a>', $method->flatSignature(), '</p>';
                            if ($textTag) {
                                echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
                            }
                            echo "</td>\n";
                            echo "</tr>\n";
                        }
                        echo "</table>\n\n";
                    }

                    if ($class->superclass()) {
                        $superclass =& $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $isSomething = true;
                            $this->inheritMethods($superclass, $rootDoc, $package);
                        }
                    }

                    if ($isSomething) {
                        echo '<h3>Details</h3>', "\n";

                        if ($constants) {
                            foreach ($constants as $field) {
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

                        if ($fields) {
                            foreach ($fields as $field) {
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

                        if ($methods) {
                            foreach ($methods as $method) {
                                $textTag =& $method->tags('@text');
                                echo '<code class="signature" id="'.$method->name().'">', $method->modifiers(), ' ', $method->returnTypeAsString(), ' <strong>';
                                echo $method->name(), '</strong>', $method->flatSignature();
                                echo "</code>\n";
                                echo '<div class="details">', "\n";
                                if ($textTag) {
                                    echo $this->_processInlineTags($textTag);
                                }
                                $this->_processTags($method->tags());
                                echo "</div>\n\n";
                            }
                        }
                    }

                    $this->_output = ob_get_contents();
                    ob_end_clean();

                    $this->_write($package->asPath().'/'.strtolower($class->name()).'.html', $class->name(), TRUE);
                }
            }
        }
    }

    /** Display the inherited fields of an element. This method calls itself
     * recursively if the element has a parent class.
     *
     * @param ProgramElementDoc element
     * @param RootDoc rootDoc
     * @param PackageDoc package
     */
    public function inheritFields(&$element, &$rootDoc, &$package)
    {
        $fields =& $element->fields();
        if ($fields) {
            ksort($fields);
            $num = count($fields); $foo = 0;
            echo '<h3>Fields inherited from ', $element->qualifiedName(), "</h3>\n";
            echo '<table>', "\n";
            echo '<tr><td>';
            foreach ($fields as $field) {
                echo '<a href="', str_repeat('../', $this->_depth), $field->asPath(), '">', $field->name(), '</a>';
                if (++$foo < $num) {
                    echo ', ';
                }
            }
            echo '</td></tr>';
            echo "</table>\n\n";
            if ($element->superclass()) {
                $superclass =& $rootDoc->classNamed($element->superclass());
                if ($superclass) {
                    $this->inheritFields($superclass, $rootDoc, $package);
                }
            }
        }
    }

    /** Display the inherited methods of an element. This method calls itself
     * recursively if the element has a parent class.
     *
     * @param ProgramElementDoc element
     * @param RootDoc rootDoc
     * @param PackageDoc package
     */
    public function inheritMethods(&$element, &$rootDoc, &$package)
    {
        $methods =& $element->methods();
        if ($methods) {
            ksort($methods);
            $num = count($methods); $foo = 0;
            echo '<h3>Methods inherited from ', $element->qualifiedName(), "</h3>\n";
            echo '<table>', "\n";
            echo '<tr><td>';
            foreach ($methods as $method) {
                echo '<a href="', str_repeat('../', $this->_depth), $method->asPath(), '">', $method->name(), '</a>';
                if (++$foo < $num) {
                    echo ', ';
                }
            }
            echo '</td></tr>';
            echo "</table>\n\n";
            if ($element->superclass()) {
                $superclass =& $rootDoc->classNamed($element->superclass());
                if ($superclass) {
                    $this->inheritMethods($superclass, $rootDoc, $package);
                }
            }
        }
    }

}
