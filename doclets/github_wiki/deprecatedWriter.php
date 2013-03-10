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

/** This generates the deprecated elements index.
 *
 * @package PHPDoctor\Doclets\GithubWiki
 */
class DeprecatedWriter extends MDWriter {

    /** Build the deprecated index.
     *
     * @param Doclet doclet
     */
    function deprecatedWriter(&$doclet) {

        parent::MDWriter($doclet);

        //$this->_id = "definition";

        $rootDoc = & $this->_doclet->rootDoc();
        
        $deprecatedClasses = array();
        $classes = & $rootDoc->classes();
        $deprecatedFields = array();
        $deprecatedMethods = array();
        if ($classes) {
            foreach ($classes as $class) {
                if ($class->tags("@deprecated")) {
                    $deprecatedClasses[] = $class;
                }
                $fields = & $class->fields();
                if ($fields) {
                    foreach ($fields as $field) {
                        if ($field->tags("@deprecated")) {
                            $deprecatedFields[] = $field;
                        }
                    }
                }
                $classes = & $class->methods();
                if ($classes) {
                    foreach ($classes as $method) {
                        if ($method->tags("@deprecated")) {
                            $deprecatedMethods[] = $method;
                        }
                    }
                }
            }
        }
        $deprecatedGlobals = array();
        $globals = & $rootDoc->globals();
        if ($globals) {
            foreach ($globals as $global) {
                if ($global->tags("@deprecated")) {
                    $deprecatedGlobals[] = $global;
                }
            }
        }
        $deprecatedFunctions = array();
        $functions = & $rootDoc->functions();
        if ($functions) {
            foreach ($functions as $function) {
                if ($function->tags("@deprecated")) {
                    $deprecatedFunctions[] = $function;
                }
            }
        }

        ob_start();

        echo "- - -\n\n";

        echo "#Deprecated API#\n\n";

        echo "- - -\n\n";

        if ($deprecatedClasses || $deprecatedFields || $deprecatedMethods || $deprecatedGlobals || $deprecatedFunctions) {
            echo "##Contents##\n\n";

            if ($deprecatedClasses) {
                echo "\n* <a href=\"#deprecated_class\">Deprecated Classes</a>";
            }
            if ($deprecatedFields) {
                echo "\n* <a href=\"#deprecated_field\">Deprecated Fields</a>";
            }
            if ($deprecatedMethods) {
                echo "\n* <a href=\"#deprecated_method\">Deprecated Methods</a>";
            }
            if ($deprecatedGlobals) {
                echo "\n* <a href=\"#deprecated_global\">Deprecated Globals</a>";
            }
            if ($deprecatedFunctions) {
                echo "\n* <a href=\"#deprecated_function\">Deprecated Functions</a>";
            }
        }

        if ($deprecatedClasses) {
            echo "\n\n<table id=\"deprecated_class\" class=\"detail\">", "\n";
            echo "<tr><th colspan=\"2\">Deprecated Classes</th></tr>", "\n";
            foreach ($deprecatedClasses as $class) {
                $textTag = & $class->tags("@text");
                echo "<tr><td class=\"name\"><a href=\"", $this->_asURL($class), "\">", $class->qualifiedName(), "</a></td>";
                echo "<td class=\"description\">";
                if ($textTag)
                    echo strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>");
                echo "</td></tr>\n";
            }
            echo "</table>\n\n";
        }

        if ($deprecatedFields) {
            echo "<table id=\"deprecated_field\" class=\"detail\">", "\n";
            echo "<tr><th colspan=\"2\" class=\"title\">Deprecated Fields</th></tr>", "\n";
            foreach ($deprecatedFields as $field) {
                $textTag = & $field->tags("@text");
                echo "<tr>\n";
                echo "<td class=\"name\"><a href=\"", $this->_asURL($field), "\">", $field->qualifiedName(), "</a></td>\n";
                echo "<td class=\"description\">";
                if ($textTag)
                    echo strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>");
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        if ($deprecatedMethods) {
            echo "<table id=\"deprecated_method\" class=\"detail\">", "\n";
            echo "<tr><th colspan=\"2\" class=\"title\">Deprecated Methods</th></tr>", "\n";
            foreach ($deprecatedMethods as $method) {
                $textTag = & $method->tags("@text");
                echo "<tr>\n";
                echo "<td class=\"name\"><a href=\"", $this->_asURL($method), "\">", $method->qualifiedName(), "</a></td>\n";
                echo "<td class=\"description\">";
                if ($textTag)
                    echo strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>");
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        if ($deprecatedGlobals) {
            echo "<table id=\"deprecated_global\" class=\"detail\">", "\n";
            echo "<tr><th colspan=\"2\" class=\"title\">Deprecated Globals</th></tr>", "\n";
            foreach ($deprecatedGlobals as $global) {
                $textTag = & $global->tags("@text");
                echo "<tr>\n";
                echo "<td class=\"name\"><a href=\"", $this->_asURL($global), "\">", $global->qualifiedName(), "</a></td>\n";
                echo "<td class=\"description\">";
                if ($textTag)
                    echo strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>");
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        if ($deprecatedFunctions) {
            echo "<table id=\"deprecated_function\" class=\"detail\">", "\n";
            echo "<tr><th colspan=\"2\" class=\"title\">Deprecated Functions</th></tr>", "\n";
            foreach ($deprecatedFunctions as $function) {
                $textTag = & $function->tags("@text");
                echo "<tr>\n";
                echo "<td class=\"name\"><a href=\"", $this->_asURL($function), "\">", $function->qualifiedName(), "</a></td>\n";
                echo "<td class=\"description\">";
                if ($textTag)
                    echo strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>");
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n\n";
        }

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write("deprecated-list.md");
    }

}

?>
