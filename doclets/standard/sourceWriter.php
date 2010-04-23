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

// $Id: classWriter.php,v 1.21 2008/06/08 10:08:35 peejeh Exp $

/** This uses GeSHi to generate formatted source for each source file in the
 * parsed code.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $Revision: 1.21 $
 */
class SourceWriter extends HTMLWriter
{

	/** Parse the source files.
	 *
	 * @param Doclet doclet
	 */
	function sourceWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		$rootDoc =& $this->_doclet->rootDoc();
		$phpdoctor =& $this->_doclet->phpdoctor();
		
		$sources =& $rootDoc->sources();
        
		foreach ($sources as $filename => $source) {
		    
			$this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
			$this->_sections[1] = array('title' => 'Package');
			$this->_sections[2] = array('title' => 'Class');
			//$this->_sections[3] = array('title' => 'Use');
			if ($phpdoctor->getOption('tree')) $this->_sections[4] = array('title' => 'Tree');
			$this->_sections[5] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
			$this->_sections[6] = array('title' => 'Index', 'url' => 'index-all.html');
			
            $this->_depth = substr_count($filename, '/') + 1;
            
            $geshi = new GeSHi($source, 'php');
            $parsed = $geshi->parse_code();
            $this->_output = '';
            foreach (explode("\n", $parsed) as $index => $line) {
                $this->_output .= '<a name="line'.($index + 1).'"></a>'.$line."\n";
            }
            
            $this->_write('source/'.strtolower($filename).'.html', $filename, TRUE);
            
		}
		
    }
}
