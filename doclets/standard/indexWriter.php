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
 * @package PHPDoctor\Doclets\Standard
 */
class indexWriter extends HTMLWriter
{

    /** Build the element index.
     *
     * @param Doclet doclet
     */
    public function indexWriter(&$doclet)
    {

        parent::HTMLWriter($doclet);

        //$this->_id = 'definition';

        $rootDoc =& $this->_doclet->rootDoc();

        $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
        $this->_sections[1] = array('title' => 'Namespace');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        if ($doclet->includeSource()) $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html');
        $this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
        $this->_sections[7] = array('title' => 'Todo', 'url' => 'todo-list.html');
        $this->_sections[8] = array('title' => 'Index', 'selected' => TRUE);

        $classes =& $rootDoc->classes();
        if($classes == NULL) $classes = array();

        $methods = array();
        foreach ($classes as $class) {
            foreach ($class->methods(TRUE) as $name => $method) {
                $methods[$class->name().'::'.$name] = $method;
            }
        }
        if($methods == NULL) $methods = array();

        $functions =& $rootDoc->functions();
        if($functions == NULL) $functions = array();

        $globals =& $rootDoc->globals();
        if($globals == NULL) $globals = array();

        $elements = array_merge($classes, $methods, $functions, $globals);
        uasort($elements, array($this, 'compareElements'));

        ob_start();

        $letter = 64;
        foreach ($elements as $name => $element) {
            $firstChar = strtoupper(substr($element->name(), 0, 1));
            if (is_object($element) && $firstChar != chr($letter)) {
                $letter = ord($firstChar);
                echo '<a href="#letter', chr($letter), '">', chr($letter), "</a>\n";
            }
        }

        echo "<hr>\n\n";

        $first = TRUE;
        foreach ($elements as $element) {
            if (is_object($element)) {
                if (strtoupper(substr($element->name(), 0, 1)) != chr($letter)) {
                    $letter = ord(strtoupper(substr($element->name(), 0, 1)));
                    if (!$first) {
                        echo "</dl>\n";
                    }
                    $first = FALSE;
                    echo '<h1 id="letter', chr($letter), '">', chr($letter), "</h1>\n";
                    echo "<dl>\n";
                }
                $parent =& $element->containingClass();
                if ($parent && strtolower(get_class($parent)) != 'rootdoc') {
                    $in = 'class <a href="'.$parent->asPath().'">'.$parent->qualifiedName().'</a>';
                } else {
                    $package =& $element->containingPackage();
                    $in = 'namespace <a href="'.$package->asPath().'/package-summary.html">'.$package->name().'</a>';
                }
                switch (strtolower(get_class($element))) {
                case 'classdoc':
                    if ($element->isOrdinaryClass()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Class in ', $in, "</dt>\n";
                    } elseif ($element->isInterface()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Interface in ', $in, "</dt>\n";
                    } elseif ($element->isTrait()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Trait in ', $in, "</dt>\n";
                    } elseif ($element->isException()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Exception in ', $in, "</dt>\n";
                    }
                    break;
                case 'methoddoc':
                    if ($element->isMethod()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Method in ', $in, "</dt>\n";
                    } elseif ($element->isFunction()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Function in ', $in, "</dt>\n";
                    }
                    break;
                case 'fielddoc':
                    if ($element->isGlobal()) {
                        echo '<dt><a href="', $element->asPath(), '">', $element->name(), '()</a> - Global in ', $in, "</dt>\n";
                    }
                    break;
                }
                if ($textTag =& $element->tags('@text') && $firstSentenceTags =& $textTag->firstSentenceTags($this->_doclet)) {
                    echo '<dd>';
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

        $this->_write('index-all.html', 'Index', TRUE);

    }

    public function compareElements($element1, $element2)
    {
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
