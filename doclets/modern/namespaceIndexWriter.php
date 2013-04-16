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

/** This generates the namespaces.html files that list the contents of
 * a namespace.
 *
 * @package PHPDoctor\Doclets\Modern
 */
class NamespaceIndexWriter extends HTMLWriter
{

    /** Build the project overview page.
     *
     * @param Doclet doclet
     */
    public function __construct(&$doclet)
    {

        parent::__construct($doclet);

        $this->_id = 'namespaces';

        $rootDoc =& $this->_doclet->rootDoc();
        $phpdoctor =& $this->_doclet->phpdoctor();

        $packages =& $rootDoc->packages();
        ksort($packages);

        ob_start();

        echo '<header>';
        echo '<h1>'.$this->_doclet->_docTitle.'</h1>';
        echo '<h2>Overview</h2>';
        echo '</header>';

        echo '<table>';
        foreach ($packages as $packageName => $package) {
            echo '<tr><td><a href="'.$package->asPath().'.html">'.$package->name().'</a></td></tr>';
        }
        echo '</table>';

        $textTag =& $rootDoc->tags('@text');
        if ($textTag) {
            $description = $this->_processInlineTags($textTag);
            if ($description) {
                echo '<h3>Description</h3>';
                echo '<div class="comment">', $description, "</div>\n\n";
            }
        }

        $this->_output = ob_get_contents();
        ob_end_clean();

        $this->_write('namespaces.html', 'Namespaces', true);

    }

}