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
 * @package PHPDoctor\Doclets\Github
 */
class ClassWriter extends MDWriter {

    /**
     *
     * @param Doclet $doclet 
     */
    function classWriter(&$doclet) {

        parent::MDWriter($doclet);

        $this->_id = "definition";

        $rootDoc = & $this->_doclet->rootDoc();
        $phpdoctor = & $this->_doclet->phpdoctor();

        $packages = & $rootDoc->packages();
        ksort($packages);

        foreach ($packages as $packageName => $package) {
            $this->_depth = $package->depth() + 1;

            $classes = & $package->allClasses();

            if ($classes) {
                ksort($classes);
                foreach ($classes as $name => $class) {

                    ob_start();

                    echo "\n\n- - -\n\n";

                    echo "**", $class->qualifiedName(), "**\n\n";
                    $this->_sourceLocation($class);

                    if ($class->isInterface()) {
                        echo "#Interface {$class->name()}#\n\n";
                    } else {
                        echo "#Class {$class->name()}#\n\n";
                    }

                    $result = $this->_buildTree($rootDoc, $classes[$name]);
                    echo $result[0];
                    echo "\n\n";

                    $implements = & $class->interfaces();
                    if (count($implements) > 0) {
                        echo "<dl>\n";
                        echo "<dt>All Implemented Interfaces:</dt>\n";
                        echo "<dd>";
                        foreach ($implements as $interface) {
                            echo "<a href=\"", $this->_asURL($interface), "\">";
                            if ($interface->packageName() != $class->packageName()) {
                                echo $interface->packageName(), "\\";
                            }
                            echo $interface->name(), "</a> ";
                        }
                        echo "</dd>\n";
                        echo "</dl>\n\n";
                    }

                    $subclasses = & $class->subclasses();
                    if ($subclasses) {
                        echo "<dl>\n";
                        echo "<dt>All Known Subclasses:</dt>\n";
                        echo "<dd>";
                        foreach ($subclasses as $subclass) {
                            echo "<a href=\"", $this->_asURL($subclass), "\">";
                            if ($subclass->packageName() != $class->packageName()) {
                                echo $subclass->packageName(), "\\";
                            }
                            echo $subclass->name(), "</a> ";
                        }
                        echo "</dd>\n";
                        echo "</dl>\n\n";
                    }

                    echo "\n\n- - -\n\n";

                    if ($class->isInterface()) {
                        echo "<p><strong>", $class->modifiers(), " interface</strong> <span>{$class->name()}</span>";
                    } else {
                        echo "<p><strong>", $class->modifiers(), " class</strong> <span>{$class->name()}</span>";
                    }
                    if ($class->superclass()) {
                        $superclass = & $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            echo "\n<strong>extends</strong> <a href=\"", $this->_asURL($superclass), "\">", $superclass->name(), "</a>\n\n";
                        } else {
                            echo "\n<strong>extends</strong> ", $class->superclass(), "\n\n";
                        }
                    }
                    echo "</p>\n\n";

                    $textTag = & $class->tags("@text");
                    if ($textTag) {
                        echo "<div class=\"comment\" id=\"overview_description\">", $this->_processInlineTags($textTag), "</div>\n\n";
                    }

                    $this->_processTags($class->tags());

                    echo "\n\n<hr />\n\n";

                    $constants = & $class->constants();
                    ksort($constants);
                    $fields = & $class->fields();
                    ksort($fields);
                    $constructor = & $class->constructor();
                    $methods = & $class->methods(TRUE);
                    ksort($methods);

                    if ($constants) {
                        echo "\n\n<table id=\"summary_field\">\n";
                        echo "<tr><th colspan=\"2\">Constant Summary</th></tr>\n";
                        foreach ($constants as $field) {
                            $textTag = & $field->tags("@text");
                            echo "<tr>\n";
                            echo "<td>
                                    {$this->_fieldSignature($field)}
                                  </td>\n";
                            echo "<td class=\"description\">";
                            echo "<p class=\"name\" ><a href=\"#", $this->_asURL($field), "\">";
                            if (is_null($field->constantValue()))
                                echo " $";
                            echo $field->name(), "</a>
                                </p>";
                            if ($textTag) {
                                echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                            }
                            echo "</td>\n";
                            echo "</tr>\n";
                        }
                        echo "</table>\n\n";
                    }

                    if ($fields) {
                        echo "\n\n<table id=\"summary_field\">\n";
                        echo "<tr><th colspan=\"2\">Field Summary</th></tr>\n";
                        foreach ($fields as $field) {
                            $textTag = & $field->tags("@text");
                            echo "<tr>\n";
                            echo "<td>{$this->_fieldSignature($field)}</td>\n";
                            echo "<td class=\"description\">";
                            echo "<p class=\"name\" ><a href=\"", $this->_asURL($field), "\">";
                            if (is_null($field->constantValue()))
                                echo " $";
                            echo $field->name(), "</a>
                                </p>";
                            if ($textTag) {
                                echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                            }
                            echo "</td>\n";
                            echo "</tr>\n";
                        }
                        echo "</table>\n\n";
                    }

                    if ($class->superclass()) {
                        $superclass = & $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $this->inheritFields($superclass, $rootDoc, $package);
                        }
                    }

                    if ($constructor) {
                        echo "<table id=\"summary_constructor\">", "\n";
                        echo "<tr><th colspan=\"2\">Constructor Summary</th></tr>", "\n";
                        $textTag = & $constructor->tags("@text");
                        echo "<tr>\n";
                        echo "<td>{$this->_methodSignature($constructor)}</td>\n";
                        echo "<td class=\"description\">";
                        echo "<p class=\"name\"><a href=\"#{$constructor->name()}\">", $constructor->name(), "</a>", $this->_flatSignature($constructor), "</p>";
                        if ($textTag) {
                            echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                        }
                        echo "</td>\n";
                        echo "</tr>\n";
                        echo "</table>\n\n";
                    }

                    if ($methods) {
                        echo "<table id=\"summary_method\">", "\n";
                        echo "<tr><th colspan=\"2\">Method Summary</th></tr>", "\n";
                        foreach ($methods as $method) {
                            $textTag = & $method->tags("@text");
                            echo "<tr>\n";
                            echo "<td>{$this->_methodSignature($method)}</td>\n";
                            echo "<td class=\"description\">";
                            echo "<p class=\"name\"><a href=\"#", strtolower($method->name()), "\">", $method->name(), "</a>", $this->_flatSignature($method), "</p>";
                            if ($textTag) {
                                echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                            }
                            echo "</td>\n";
                            echo "</tr>\n";
                        }
                        echo "</table>\n\n";
                    }

                    if ($class->superclass()) {
                        $superclass = & $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $this->inheritMethods($superclass, $rootDoc, $package);
                        }
                    }

                    if ($constants) {
                        echo "##Constant Detail##", "\n";
                        foreach ($constants as $field) {
                            $textTag = & $field->tags("@text");
                            $type = & $field->type();
                            $this->_sourceLocation($field);
                            echo "<h3 id=\"", $field->name(), "\">", $field->name(), "</h3>\n";
                            echo $this->_fieldSignature($field);
                            echo "<span class='no'>";
                            if (is_null($field->constantValue()))
                                echo " $";
                            echo $field->name(), "</span>";

                            if (!is_null($field->value()))
                                echo "<span class='o'> = ", htmlspecialchars($field->value()), "</span>\n\n";

                            echo "<div class=\"details\">", "\n";

                            if ($textTag) {
                                echo $this->_processInlineTags($textTag);
                            }
                            $this->_processTags($field->tags());
                            echo "\n</div>";

                            echo "\n\n- - -\n\n";
                        }
                    }

                    if ($fields) {
                        echo "##Field Detail##", "\n";
                        foreach ($fields as $field) {
                            $textTag = & $field->tags("@text");
                            $type = & $field->type();
                            $this->_sourceLocation($field);
                            echo "<h3 id=\"", $field->name(), "\">", $field->name(), "</h3>\n";
                            echo $this->_fieldSignature($field);
                            echo "<span class='no'>";
                            if (is_null($field->constantValue()))
                                echo " $";
                            echo $field->name(), "</span>";

                            if (!is_null($field->value()))
                                echo "<span class='o'> = ", htmlspecialchars($field->value()), "</span>\n\n";

                            echo "<div class=\"details\">", "\n";

                            if ($textTag) {
                                echo $this->_processInlineTags($textTag);
                            }
                            $this->_processTags($field->tags());
                            echo "\n</div>";
                            echo "\n\n- - -\n\n";
                        }
                    }

                    if ($constructor) {
                        echo "<h2>Constructor Detail</h2>\n\n";
                        $textTag = & $constructor->tags("@text");
                        $this->_sourceLocation($constructor);
                        echo "<h3 id=\"{$constructor->name()}\">{$constructor->name()}</h3>\n";
                        echo $this->_methodSignature($constructor);
                        echo " <span class='nf'>{$constructor->name()}</span> {$this->_flatSignature($constructor)}";
                        echo "\n\n";
                        echo "<div class=\"details\">", "\n";
                        if ($textTag) {
                            echo $this->_processInlineTags($textTag);
                        }
                        $this->_processTags($constructor->tags());
                        echo "\n</div>";
                        echo "\n\n- - -\n\n";
                    }

                    if ($methods) {
                        echo "<h2 id=\"detail_method\">Method Detail</h2>", "\n";
                        foreach ($methods as $method) {
                            $textTag = & $method->tags("@text");
                            $this->_sourceLocation($method);
                            echo "<h3 id=\"", $method->name(), "()\">", $method->name(), "</h3>\n";
                            echo $this->_methodSignature($method);
                            echo " <span class='nf'>{$method->name()}</span> {$this->_flatSignature($method)}";
                            echo "\n\n";
                            echo "<div class=\"details\">", "\n";
                            if ($textTag) {
                                echo $this->_processInlineTags($textTag);
                            }
                            $this->_processTags($method->tags());
                            echo "\n</div>";
                            echo "\n\n- - -\n\n";
                        }
                    }

                    $this->_output = ob_get_contents();
                    ob_end_clean();

                    $this->_write($this->_asPath($class));
                }
            }
        }
    }

    /** Build the class hierarchy tree which is placed at the top of the page.
     *
     * @param RootDoc rootDoc The root doc
     * @param ClassDoc class Class to generate tree for
     * @param int depth Depth of recursion
     * @return mixed[]
     */
    function _buildTree(&$rootDoc, &$class, $depth = NULL) {
        if ($depth === NULL) {
            $start = TRUE;
            $depth = 0;
        } else {
            $start = FALSE;
        }
        $output = "";
        $undefinedClass = FALSE;
        if ($class->superclass()) {
            $superclass = & $rootDoc->classNamed($class->superclass());
            if ($superclass) {
                $result = $this->_buildTree($rootDoc, $superclass, $depth);
                $output .= $result[0];
                $depth = ++$result[1];
            } else {
                $output .= $class->superclass();
                $output .= " &gt; ";
                $depth++;
                $undefinedClass = TRUE;
            }
        }
        if ($depth > 0 && !$undefinedClass) {
            $output .= " &gt; ";
        }
        if ($start) {
            $output .= "**" . $class->name() . "**\n";
        } else {
            $output .= "<a href=\"" . $this->_asURL($class) . "\">" . $class->name() . "</a>\n";
        }
        return array($output, $depth);
    }

    /** Display the inherited fields of an element. This method calls itself
     * recursively if the element has a parent class.
     *
     * @param ProgramElementDoc element
     * @param RootDoc rootDoc
     * @param PackageDoc package
     */
    function inheritFields(&$element, &$rootDoc, &$package) {
        $fields = & $element->fields();
        if ($fields) {
            ksort($fields);
            $num = count($fields);
            $foo = 0;
            echo "<table class=\"inherit\">", "\n";
            echo "<tr><th colspan=\"2\">Fields inherited from ", $element->qualifiedName(), "</th></tr>\n";
            echo "<tr><td>";
            foreach ($fields as $field) {
                echo "<a href=\"", $this->_asURL($field), "\">", $field->name(), "</a>";
                if (++$foo < $num) {
                    echo ", ";
                }
            }
            echo "</td></tr>";
            echo "</table>\n\n";
            if ($element->superclass()) {
                $superclass = & $rootDoc->classNamed($element->superclass());
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
    function inheritMethods(&$element, &$rootDoc, &$package) {
        $methods = & $element->methods();
        if ($methods) {
            ksort($methods);
            $num = count($methods);
            $foo = 0;
            echo "<table class=\"inherit\">", "\n";
            echo "<tr><th colspan=\"2\">Methods inherited from ", $element->qualifiedName(), "</th></tr>\n";
            echo "<tr><td>";
            foreach ($methods as $method) {
                echo "<a href=\"", $this->_asURL($method), "\">", $method->name(), "</a>";
                if (++$foo < $num) {
                    echo ", ";
                }
            }
            echo "</td></tr>";
            echo "</table>\n\n";
            if ($element->superclass()) {
                $superclass = & $rootDoc->classNamed($element->superclass());
                if ($superclass) {
                    $this->inheritMethods($superclass, $rootDoc, $package);
                }
            }
        }
    }

}

?>
