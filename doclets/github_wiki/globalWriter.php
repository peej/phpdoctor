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

/** This generates the HTML API documentation for each global variable.
 *
 * @package PHPDoctor\Doclets\GithubWiki
 */
class GlobalWriter extends MDWriter {

    /** Build the function definitons.
     *
     * @param Doclet doclet
     */
    function globalWriter(&$doclet) {

        parent::MDWriter($doclet);

        $this->_id = "definition";

        $rootDoc = & $this->_doclet->rootDoc();

        $packages = & $rootDoc->packages();
        ksort($packages);

        foreach ($packages as $packageName => $package) {
            $this->_depth = $package->depth() + 1;

            ob_start();

            echo "- - -\n\n";

            echo "#Globals#\n\n";

            echo "- - -\n\n";

            $globals = & $package->globals();

            if ($globals) {
                ksort($globals);
                echo "<table id=\"summary_global\" class=\"title\">", "\n";
                echo "<tr><th colspan=\"2\" class=\"title\">Global Summary</th></tr>", "\n";
                foreach ($globals as $global) {
                    $textTag = & $global->tags("@text");
                    $type = & $global->type();
                    echo "<tr>\n";
                    echo "<td>", $global->modifiers(FALSE), " ", $global->typeAsString(), "</td>\n";
                    echo "<td class=\"description\">";
                    echo "<p class=\"name\"><a href=\"#", $this->_asURL($global), "\">", $global->name(), "</a></p>";
                    if ($textTag) {
                        echo "<p class=\"description\">", strip_tags($this->_processInlineTags($textTag, TRUE), "<a><b>**<u><em>"), "</p>";
                    }
                    echo "</td>\n";
                    echo "</tr>\n";
                }
                echo "</table>\n\n";

                echo "<h2 id=\"detail_global\">Global Detail</h2>", "\n";
                foreach ($globals as $global) {
                    $textTag = & $global->tags("@text");
                    $type = & $global->type();
                    $this->_sourceLocation($global);
                    echo "<h3 id=\"", $global->name(), "\">", $global->name(), "</h3>\n";
                    echo "\n\n", $global->modifiers(), " ", $global->typeAsString(), " **";
                    echo $global->name(), "**";
                    if ($global->value())
                        echo " = ", htmlspecialchars($global->value());
                    echo "\n\n";
                    echo "<div class=\"details\">", "\n";
                    if ($textTag) {
                        echo $this->_processInlineTags($textTag), "\n";
                    }
                    echo "</div>\n\n";
                    $this->_processTags($global->tags());
                    echo "- - -\n\n";
                }
            }

            $this->_output = ob_get_contents();
            ob_end_clean();

            $this->_write($this->_normalize($package->_name) . "_package-globals.md");
        }
    }

}

?>
