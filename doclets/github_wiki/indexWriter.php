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

/** This generates the element index.
 *
 * @package PHPDoctor\Doclets\GithubWiki
 */
class IndexWriter extends MDWriter {

    /** Build the element index.
     *
     * @param Doclet doclet
     */
    function indexWriter(&$doclet) {

        parent::MDWriter($doclet);

        $phpdoctor = & $this->_doclet->phpdoctor();
        $displayTree = $phpdoctor->getOption("tree");

        $rootDoc = & $this->_doclet->rootDoc();

        $classes = & $rootDoc->classes();
        if ($classes == NULL)
            $classes = array();

        $functions = & $rootDoc->functions();
        if ($functions == NULL)
            $functions = array();

        $globals = & $rootDoc->globals();
        if ($globals == NULL)
            $globals = array();

        $elements = array_merge($classes, $functions, $globals);
        uasort($elements, array($this, "compareElements"));

        ob_start();

        echo "#INDEX#";

        if(isset($this->_doclet->phpdoctor()->_options["github_repository"])){
            echo "\n\n[View Source codes]({$this->getSourcesBaseURL()})";
        }

        if ($displayTree) {
            echo "\n\n[View class hierarchy]({$this->getDirBaseURL()}/overview-tree)";
        }

        echo "\n\n[View depreacted list]({$this->getDirBaseURL()}/deprecated-list)";

        echo "\n\n[View todo list]({$this->getDirBaseURL()}/todo-list)";

        echo "\n\n[View the list of all classes, functions and globals]({$this->getDirBaseURL()}/index-all)";

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write("README.md");

        ///------------------------------------------------------

        ob_start();

        $letter = 64;
        foreach ($elements as $name => $element) {
            $firstChar = strtoupper(substr($element->name(), 0, 1));
            if (is_object($element) && $firstChar != chr($letter)) {
                $letter = ord($firstChar);
                echo "<a href=\"#", strtolower(chr($letter)), "\">", chr($letter), "</a>\n";
            }
        }

        echo "- - -\n\n";

        $first = TRUE;
        foreach ($elements as $element) {
            if (is_object($element)) {
                if (strtoupper(substr($element->name(), 0, 1)) != chr($letter)) {
                    $letter = ord(strtoupper(substr($element->name(), 0, 1)));
                    if (!$first) {
                        echo "</dl>\n";
                    }
                    $first = FALSE;
                    echo "<h1 id=\"letter", chr($letter), "\">", chr($letter), "</h1>\n";
                    echo "<dl>\n";
                }
                $parent = & $element->containingClass();
                if ($parent && strtolower(get_class($parent)) != "rootdoc") {
                    $in = "class <a href=\"" . $this->_asURL($parent) . "\">" . $parent->qualifiedName() . "</a>";
                } else {
                    $package = & $element->containingPackage();
                    $in = "namespace <a href=\"" . $this->_asURL($package) . "/README.md\">" . $package->name() . "</a>";
                }
                switch (strtolower(get_class($element))) {
                    case "classdoc":
                        if ($element->isOrdinaryClass()) {
                            echo "<dt><a href=\"", $this->_asURL($element), "\">", $element->name(), "</a> - Class in ", $in, "</dt>\n";
                        } elseif ($element->isInterface()) {
                            echo "<dt><a href=\"", $this->_asURL($element), "\">", $element->name(), "</a> - Interface in ", $in, "</dt>\n";
                        } elseif ($element->isException()) {
                            echo "<dt><a href=\"", $this->_asURL($element), "\">", $element->name(), "</a> - Exception in ", $in, "</dt>\n";
                        }
                        break;
                    case 'methoddoc':
                        if ($element->isFunction()) {
                            echo '<dt><a href="', $this->_asURL($element), '">', $element->name(), '()</a> - Function in ', $in, "</dt>\n";
                        }
                        break;
                    case "fielddoc":
                        if ($element->isGlobal()) {
                            echo "<dt><a href=\"", $this->_asURL($element), "\">", $element->name(), "</a> - Global in ", $in, "</dt>\n";
                        }
                        break;
                }
                if ($textTag = & $element->tags("@text") && $firstSentenceTags = & $textTag->firstSentenceTags($this->_doclet)) {
                    echo "<dd>";
                    foreach ($firstSentenceTags as $firstSentenceTag) {
                        echo $firstSentenceTag->text($this->_doclet);
                    }
                    echo "</dd>\n";
                }
            }
        }
        echo "</dl>\n";

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write("index-all.md");
    }

    function compareElements($element1, $element2) {
        $e1 = strtolower($element1->name());
        $e2 = strtolower($element2->name());
        if ($e1 == $e2) {
            return 0;
        } elseif ($e1 < $e2) {
            return -1;
        } else {
            return 1;
        }
    }

}

?>
