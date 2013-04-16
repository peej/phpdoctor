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

/** This generates the package-summary.html files that list the interfaces and
 * classes for a given package.
 *
 * @package PHPDoctor\Doclets\Modern
 */
class FrameIndexWriter extends HTMLWriter
{

    /** Build the package summaries.
     *
     * @param Doclet doclet
     */
    public function __construct(&$doclet)
    {

        parent::__construct($doclet);

        $this->_id = 'frame';

        $rootDoc = $this->_doclet->rootDoc();
        $phpdoctor = $this->_doclet->phpdoctor();

        $packages = $rootDoc->packages();
        ksort($packages);

        ob_start();

        #echo '<h1>'.$this->_doclet->_docTitle.'</h1>';

        $namespaces = array();
        foreach ($packages as $package) {
            $name = explode('\\', $package->name());
            $namespaces = $this->placeIntoNamespace($namespaces, $package, $name);
        }

        $this->outputNamespace($namespaces, $packages);

        echo <<<SCRIPT
<script>
window.onload = function () {
    var lis = document.getElementsByTagName("li");
    for (var foo = 0; foo < lis.length; foo++) {
        lis[foo].onclick = function (e) {
            e.stopPropagation();
            if (this.className == "parent open") {
                this.className = "parent";
            } else if (this.className == "parent") {
                this.className = "parent open";
            }
        };
    }
};
</script>
SCRIPT;

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('frame.html', 'Frame', true, false);

    }

    function placeIntoNamespace($namespaces, $package, $name)
    {
        $thisNamespace = array_shift($name);
        if (!isset($namespaces[$thisNamespace])) {
            $namespaces[$thisNamespace] = array();
        }
        if ($name) {
            $namespaces[$thisNamespace] = $this->placeIntoNamespace($namespaces[$thisNamespace], $package, $name);
        }
        return $namespaces;
    }

    function outputNamespace($namespaces, $packages, $fullPackageName = '', $depth = 0)
    {
        if (is_array($namespaces)) {
            echo '<ul>';
            foreach ($namespaces as $packageName => $children) {
                if ($fullPackageName) {
                    $thisFullPackageName = $fullPackageName.'\\'.$packageName;
                } else {
                    $thisFullPackageName = $packageName;
                }

                $hasChildren = isset($packages[$thisFullPackageName]) && $packages[$thisFullPackageName]->allClasses();
                $indent = (20 + (14 * $depth));

                if ($children || $hasChildren) {
                    echo '<li class="parent">';
                } else {
                    echo '<li>';
                }
                if (isset($packages[$thisFullPackageName])) {
                    echo '<a href="', $packages[$thisFullPackageName]->asPath(), '.html" target="main" style="padding-left: '.$indent.'px">', $packageName, '</a>';
                } else {
                    echo '<span style="padding-left: '.$indent.'px">'.$packageName.'</span>';
                }
                if ($children) {
                    echo $this->outputNamespace($children, $packages, $thisFullPackageName, $depth + 1);
                }
                if ($hasChildren) {
                    echo '<ul>';
                    foreach ($packages[$thisFullPackageName]->allClasses() as $class) {
                        echo '<li><a href="'.$class->asPath().'" target="main" style="padding-left: '.($indent + 14).'px">'.$class->name().'</a></li>';
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
    }

}