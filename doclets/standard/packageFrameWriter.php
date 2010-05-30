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

/** This generates the package-frame.html file that lists the interfaces and
 * classes in a given package for displaying in the lower-left frame of the
 * frame-formatted default output.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class PackageFrameWriter extends HTMLWriter
{

	/** Build the package frame index.
	 *
	 * @param Doclet doclet
	 */
	function packageFrameWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		$rootDoc =& $this->_doclet->rootDoc();
		
		$this->_output =& $this->_allItems($rootDoc);
		$this->_write('allitems-frame.html', 'All Items', FALSE);

        $packages =& $rootDoc->packages();
        ksort($packages);
        
		foreach($packages as $packageName => $package) {
		
			$this->_depth = $package->depth() + 1;
			
			$this->_output =& $this->_buildFrame($package);
			$this->_write($package->asPath().'/package-frame.html', $package->name(), FALSE);
			
		}
	
	}
	
	/** Build package frame
	 *
	 * @return str
	 */
	function &_buildFrame(&$package)
    {
		ob_start();

		echo '<body id="frame">', "\n\n";

		echo '<h1><a href="package-summary.html" target="main">', $package->name(), "</a></h1>\n\n";

		$classes =& $package->ordinaryClasses();
		if ($classes && is_array($classes)) {
            ksort($classes);
			echo "<h2>Classes</h2>\n";
			echo "<ul>\n";
			foreach($classes as $name => $class) {
				echo '<li><a href="', str_repeat('../', $package->depth() + 1), $classes[$name]->asPath(), '" target="main">', $classes[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		$interfaces =& $package->interfaces();
		if ($interfaces && is_array($interfaces)) {
            ksort($interfaces);
			echo "<h2>Interfaces</h2>\n";
			echo "<ul>\n";
			foreach($interfaces as $name => $interface) {
				echo '<li><a href="', str_repeat('../', $package->depth() + 1), $interfaces[$name]->asPath(), '" target="main">', $interfaces[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		$exceptions =& $package->exceptions();
		if ($exceptions && is_array($exceptions)) {
            ksort($exceptions);
			echo "<h2>Exceptions</h2>\n";
			echo "<ul>\n";
			foreach($exceptions as $name => $exception) {
				echo '<li><a href="', str_repeat('../', $package->depth() + 1), $exceptions[$name]->asPath(), '" target="main">', $exceptions[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		$functions =& $package->functions();
		if ($functions && is_array($functions)) {
            ksort($functions);
			echo "<h2>Functions</h2>\n";
			echo "<ul>\n";
			foreach($functions as $name => $function) {
				echo '<li><a href="', str_repeat('../', $package->depth() + 1), $functions[$name]->asPath(), '" target="main">', $functions[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		$globals =& $package->globals();
		if ($globals && is_array($globals)) {
            ksort($globals);
			echo "<h2>Globals</h2>\n";
			echo "<ul>\n";
			foreach($globals as $name => $global) {
				echo '<li><a href="', str_repeat('../', $package->depth() + 1), $globals[$name]->asPath(), '" target="main">', $globals[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		echo '</body>', "\n\n";

		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}

	/** Build all items frame
	 *
	 * @return str
	 */
	function &_allItems(&$rootDoc)
    {
		ob_start();

		echo '<body id="frame">', "\n\n";

		echo "<h1>All Items</h1>\n\n";

		$classes =& $rootDoc->classes();
		if ($classes) {
            ksort($classes);
			echo "<h2>Classes</h2>\n";
			echo "<ul>\n";
			foreach($classes as $name => $class) {
				$package =& $classes[$name]->containingPackage();
				if ($class->isInterface()) {
					echo '<li><em><a href="', $classes[$name]->asPath(), '" title="', $classes[$name]->packageName(),'" target="main">', $classes[$name]->name(), "</a></em></li>\n";
				} else {
					echo '<li><a href="', $classes[$name]->asPath(), '" title="', $classes[$name]->packageName(),'" target="main">', $classes[$name]->name(), "</a></li>\n";
				}
			}
			echo "</ul>\n\n";
		}

		$functions =& $rootDoc->functions();
		if ($functions) {
            ksort($functions);
			echo "<h2>Functions</h2>\n";
			echo "<ul>\n";
			foreach($functions as $name => $function) {
				$package =& $functions[$name]->containingPackage();
				echo '<li><a href="', $package->asPath(), '/package-functions.html#', $functions[$name]->name(), '()" title="', $functions[$name]->packageName(),'" target="main">', $functions[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}

		$globals =& $rootDoc->globals();
		if ($globals) {
            ksort($globals);
			echo "<h2>Globals</h2>\n";
			echo "<ul>\n";
			foreach($globals as $name => $global) {
				$package =& $globals[$name]->containingPackage();
				echo '<li><a href="', $package->asPath(), '/package-globals.html#', $globals[$name]->name(), '" title="', $globals[$name]->packageName(),'" target="main">', $globals[$name]->name(), "</a></li>\n";
			}
			echo "</ul>\n\n";
		}
		
		echo '</body>', "\n\n";

		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}

}

?>

