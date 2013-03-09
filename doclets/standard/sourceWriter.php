<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2010 Paul James <paul@peej.co.uk>

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

/** This uses GeSHi to generate formatted source for each source file in the
 * parsed code.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class sourceWriter extends HTMLWriter
{

    /** Parse the source files.
     *
     * @param Doclet doclet
     */
    public function sourceWriter(&$doclet)
    {

        parent::HTMLWriter($doclet);

        $rootDoc =& $this->_doclet->rootDoc();
        $phpdoctor =& $this->_doclet->phpdoctor();

        $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
        $this->_sections[1] = array('title' => 'Namespace');
        $this->_sections[2] = array('title' => 'Class');
        //$this->_sections[3] = array('title' => 'Use');
        if ($phpdoctor->getOption('tree')) $this->_sections[4] = array('title' => 'Tree', 'url' => 'overview-tree.html');
        $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html', 'selected' => TRUE);
        $this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
        $this->_sections[7] = array('title' => 'Todo', 'url' => 'todo-list.html');
        $this->_sections[8] = array('title' => 'Index', 'url' => 'index-all.html');

        $sources =& $rootDoc->sources();

        $this->_id = 'files';

        ob_start();

        echo "<hr>\n\n";

        echo '<h1>Source Files</h1>';

        echo "<ul>\n";
        foreach ($sources as $filename => $data) {
            $url = strtolower(str_replace(DIRECTORY_SEPARATOR, '/', $filename));
            echo '<li><a href="source/', $url, '.html">', $filename, '</a></li>';
        }
        echo "</ul>\n";

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('overview-files.html', 'Overview', TRUE);

        $this->_id = 'file';

        foreach ($sources as $filename => $data) {

            $this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
            $this->_sections[1] = array('title' => 'Package');
            $this->_sections[2] = array('title' => 'Class');
            //$this->_sections[3] = array('title' => 'Use');
            if ($phpdoctor->getOption('tree')) $this->_sections[4] = array('title' => 'Tree');
            $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html');
            $this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
            $this->_sections[7] = array('title' => 'Todo', 'url' => 'todo-list.html');
            $this->_sections[8] = array('title' => 'Index', 'url' => 'index-all.html');

            $this->_depth = substr_count($filename, '/') + 1;

            if (class_exists('GeSHi')) {
                $geshi = new GeSHi($data[0], 'php');
                $source = $geshi->parse_code();
            } else {
                $source = '<pre>'.$data[0].'</pre>';
            }

            ob_start();

            echo "<hr>\n\n";
            echo '<h1>'.$filename."</h1>\n";

            if (isset($data[1]['tags']['@text'])) {
                echo '<div class="comment" id="overview_description">', $this->_processInlineTags($data[1]['tags']['@text']), "</div>\n\n";
            }

            echo "<hr>\n\n";

            foreach (explode("\n", $source) as $index => $line) {
                echo '<a name="line'.($index + 1).'"></a>'.$line."\n";
            }

            $this->_output = ob_get_contents();
            ob_end_clean();

            $this->_write('source/'.strtolower($filename).'.html', $filename, TRUE);

        }

    }
}
