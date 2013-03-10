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

/** This generates the HTML API documentation for each global function.
 *
 * @package PHPDoctor\Doclets\Github
 */
class FunctionWriter extends MDWriter {

    /** Build the function definitons.
     *
     * @param Doclet doclet
     */
    function functionWriter(&$doclet) {

        parent::MDWriter($doclet);

        $this->_id = "definition";

        $rootDoc = & $this->_doclet->rootDoc();

        $packages = & $rootDoc->packages();
        ksort($packages);

        foreach ($packages as $packageName => $package) {
            $this->_depth = $package->depth() + 1;

            ob_start();

            echo "- - -\n\n";

            echo "#Functions#\n\n";

            echo "- - -\n\n";

            $functions = & $package->functions();

            if ($functions) {
                ksort($functions);
                echo "<table id=\"summary_function\" class=\"title\">", "\n";
                echo "<tr><th colspan=\"2\" class=\"title\">Function Summary</th></tr>", "\n";
                foreach ($functions as $function) {
                    $textTag = & $function->tags("@text");
                    echo "<tr>\n";
                    echo "<td>", $this->_methodSignature($function), "</td>\n";
                    echo "<td class=\"description\">";
                    echo "<p class=\"name\"><a href=\"#", $this->_asURL($function),"\">", $function->name(), "</a>", $this->_flatSignature($function), "</p>";
                    if ($textTag) {
                        echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                    }
                    echo "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n\n";

                echo "<h2 id=\"detail_function\">Function Detail</h2>", "\n";
                foreach ($functions as $function) {
                    $textTag = & $function->tags("@text");
                    $this->_sourceLocation($function);
                    echo "<h3 id=\"", $function->name(), "()\">", $function->name(), "</h3>\n";
                    echo $this->_methodSignature($function);
                    echo " {$function->name()} {$this->_flatSignature($function)}\n\n";
                    echo "<div class=\"details\">\n";
                    if ($textTag) {
                        echo $this->_processInlineTags($textTag), "\n";
                    }
                    $this->_processTags($function->tags());
                    echo "</div>\n\n";
                    echo "- - -\n\n";
                }
            }

            $this->_output = ob_get_contents();
            ob_end_clean();

            $this->_write($package->_name . "/package-functions.md");
        }
    }

}

?>
