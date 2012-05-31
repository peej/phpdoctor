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
 * @package PHPDoctor\Doclets\Standard
 */
class FunctionWriter extends HTMLWriter
{

	/** Build the function definitons.
	 *
	 * @param Doclet doclet
	 */
	function functionWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		$this->_id = 'definition';

		$rootDoc =& $this->_doclet->rootDoc();
        
        $packages =& $rootDoc->packages();
        ksort($packages);

		foreach($packages as $packageName => $package) {

			$this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
			$this->_sections[1] = array('title' => 'Namespace', 'url' => $package->asPath().'/package-summary.html');
			$this->_sections[2] = array('title' => 'Function', 'selected' => TRUE);
			//$this->_sections[3] = array('title' => 'Use');
			$this->_sections[4] = array('title' => 'Tree', 'url' => $package->asPath().'/package-tree.html');
			if ($doclet->includeSource()) $this->_sections[5] = array('title' => 'Files', 'url' => 'overview-files.html');
			$this->_sections[6] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
			$this->_sections[7] = array('title' => 'Todo', 'url' => 'todo-list.html');
			$this->_sections[8] = array('title' => 'Index', 'url' => 'index-all.html');
		
			$this->_depth = $package->depth() + 1;

			ob_start();

			echo "<hr>\n\n";

			echo "<h1>Functions</h1>\n\n";
					
			echo "<hr>\n\n";
					
			$functions =& $package->functions();
				
			if ($functions) {
                ksort($functions);
				echo '<table id="summary_function" class="title">', "\n";
				echo '<tr><th colspan="2" class="title">Function Summary</th></tr>', "\n";
				foreach($functions as $function) {
					$textTag =& $function->tags('@text');
					echo "<tr>\n";
					echo '<td class="type">', $function->modifiers(FALSE), ' ', $function->returnTypeAsString(), "</td>\n";
					echo '<td class="description">';
					echo '<p class="name"><a href="#', $function->name(), '()">', $function->name(), '</a>', $function->flatSignature(), '</p>';
					if ($textTag) {
						echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
					}
					echo "</td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n\n";

				echo '<h2 id="detail_function">Function Detail</h2>', "\n";
				foreach($functions as $function) {
					$textTag =& $function->tags('@text');
					$this->_sourceLocation($function);
					echo '<h3 id="', $function->name(),'()">', $function->name(), "</h3>\n";
					echo '<code class="signature">', $function->modifiers(), ' ', $function->returnTypeAsString(), ' <strong>';
					echo $function->name(), '</strong>', $function->flatSignature();
					echo "</code>\n";
                    echo '<div class="details">', "\n";
					if ($textTag) {
						echo $this->_processInlineTags($textTag), "\n";
					}
					$this->_processTags($function->tags());
                    echo "</div>\n\n";
					echo "<hr>\n\n";
				}
			}

			$this->_output = ob_get_contents();
			ob_end_clean();

			$this->_write($package->asPath().'/package-functions.html', 'Functions', TRUE);
		}
	
	}

}

?>
