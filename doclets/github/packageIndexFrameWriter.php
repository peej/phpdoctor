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

/** This generates the overview-frame.md file used for displaying the list
 * of package links in the upper-left frame in the frame-formatted default
 * output.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class PackageIndexFrameWriter extends HTMLWriter
{

	/** Build the package frame index.
	 *
	 * @param Doclet doclet
	 */
	function packageIndexFrameWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		ob_start();
		
		echo '<body id="frame">', "\n\n";
		
		echo '# '.$this->_doclet->getHeader()." #\n\n";
		
		echo "<ul>\n";
		echo '<li><a href="allitems-frame.md" target="index">All Items</a></li>'."\n";
		echo "</ul>\n\n";
		
		echo "# Namespaces #\n\n";

		$rootDoc =& $this->_doclet->rootDoc();

		echo "<ul>\n";
        $packages =& $rootDoc->packages();
        ksort($packages);
		foreach($packages as $name => $package) {
			echo '<li><a href="'.$package->asPath().'/package-frame.md" target="index">'.$package->name().'</a></li>'."\n";
		}
		echo "</ul>\n\n";
		
		echo '</body>', "\n\n";

		$this->_output = ob_get_contents();
		ob_end_clean();
		
		$this->_write('overview-frame.md', 'Overview', FALSE);
	
	}

}

?>
