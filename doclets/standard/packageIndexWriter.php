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

/** This class generates the overview-summary.html file that lists all parsed
 * packages.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class packageIndexWriter extends HTMLWriter
{

    /** Build the package index.
     *
     * @param Doclet doclet
     */
    public function packageIndexWriter(&$doclet)
    {

        parent::htmlWriter($doclet);

        $phpdoctor =& $this->_doclet->phpdoctor();

        $this->_sections[0] = array('title' => 'Overview', 'selected' => TRUE);
        $this->_sections[1] = array('title' => 'Namespace');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        if ($phpdoctor->getOption('tree')) $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        if ($doclet->includeSource()) $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html');
        $this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
        $this->_sections[7] = array('title' => 'Todo', 'url' => 'todo-list.html');
        $this->_sections[8] = array('title' => 'Index', 'url' => 'index-all.html');

        ob_start();

        echo "<hr>\n\n";

        echo '<h1>'.$this->_doclet->docTitle()."</h1>\n\n";

        $rootDoc =& $this->_doclet->rootDoc();

        $textTag =& $rootDoc->tags('@text');
        if ($textTag) {
            $description = $this->_processInlineTags($textTag, TRUE);
            if ($description) {
                echo '<div class="comment">', $description, "</div>\n\n";
                echo '<dl><dt>See:</dt><dd><b><a href="#overview_description">Description</a></b></dd></dl>'."\n\n";
            }
        }

        echo '<table class="title">'."\n";
        echo '<tr><th colspan="2" class="title">Namespaces</th></tr>'."\n";
        $packages =& $rootDoc->packages();
        ksort($packages);
        foreach ($packages as $name => $package) {
            $textTag =& $package->tags('@text');
            echo '<tr><td class="name"><a href="'.$package->asPath().'/package-summary.html">'.$package->name().'</a></td>';
            echo '<td class="description">'.strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>').'</td></tr>'."\n";
        }
        echo '</table>'."\n\n";

        $textTag =& $rootDoc->tags('@text');
        if ($textTag) {
            $description = $this->_processInlineTags($textTag);
            if ($description) {
                echo '<div class="comment" id="overview_description">', $description, "</div>\n\n";
            }
        }

        echo "<hr>\n\n";

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('overview-summary.html', 'Overview', TRUE);

    }

}
