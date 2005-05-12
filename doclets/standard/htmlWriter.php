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

// $Id: htmlWriter.php,v 1.9 2005/05/12 21:25:10 peejeh Exp $

/** This generates the index.html file used for presenting the frame-formated
 * "cover page" of the API documentation.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $Revision: 1.9 $
 */
class HTMLWriter
{

	/** The doclet that created this object.
	 *
	 * @var doclet
	 */
	var $_doclet;

	/** The section titles to place in the header and footer.
	 *
	 * @var str[][]
	 */
	var $_sections = NULL;

	/** The directory structure depth. Used to calculate relative paths.
	 *
	 * @var int
	 */
	var $_depth = 0;

	/** The <body> id attribute value, used for selecting style.
	 *
	 * @var str
	 */
	var $_id = 'overview';

	/** The output body.
	 *
	 * @var str
	 */
	var $_output = '';

	/** Writer constructor.
	 */
	function htmlWriter(&$doclet)
    {
		$this->_doclet =& $doclet;
	}

	/** Build the HTML header. Includes doctype definition, <html> and <head>
	 * sections, meta data and window title.
	 *
	 * @return str
	 */
	function _htmlHeader($title)
    {
	
		$output = $this->_doctype();
		$output .= '<html lang="en">'."\n";
		$output .= "<head>\n\n";

		$output .= '<meta name="generator" content="PHPDoctor '.$this->_doclet->version().' (http://phpdoctor.sourceforge.net/)">'."\n";
		$output .= '<meta name="when" content="'.date('r').'">'."\n\n";
		
		$output .= '<link rel="stylesheet" type="text/css" href="'.str_repeat('../', $this->_depth).'stylesheet.css">'."\n";
		$output .= '<link rel="start" href="'.str_repeat('../', $this->_depth).'overview-summary.html">'."\n\n";
		
		$output .= '<title>';
		if ($title) {
			$output .= $title.' ('.$this->_doclet->windowTitle().')';
		} else {
			$output .= $this->_doclet->windowTitle();
		}
		$output .= "</title>\n\n";
		$output .= "</head>\n";

		return $output;

	}
    
    /** Get the HTML DOCTYPE for this output
     *
     * @return str
     */
    function _doctype()
    {
        return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'."\n\n";
    }
	
	/** Build the HTML footer.
   *
   * @return str
   */
	function _htmlFooter()
    {
		return '</html>';
	}

	/** Build the HTML shell header. Includes beginning of the <body> section,
	 * and the page header.
	 *
	 * @return str
	 */
	function _shellHeader($path)
    {
		$output = '<body id="'.$this->_id.'" onload="parent.document.title=document.title;">'."\n\n";
		$output .= $this->_nav($path);
		return $output;
	}
	
	/** Build the HTML shell footer. Includes the end of the <body> section, and
	 * page footer.
	 *
	 * @return str
	 */
	function _shellFooter($path)
    {
		$output = $this->_nav($path);
		$output .= "<hr>\n\n";
		$output .= '<p id="footer">'.$this->_doclet->bottom().'</p>'."\n\n";
		$output .= "</body>\n\n";
		return $output;
	}
	
	/** Build the navigation bar
	 *
	 * @return str
	 */
	function _nav($path)
    {
		$output = '<table width="100%" cellpadding="0" class="header">'."\n";
		$output .= "<tr>\n";
		$output .= '<td class="header">';
		if ($this->_sections) {
			foreach ($this->_sections as $section) {
				if (isset($section['selected']) && $section['selected']) {
					$output .= '<span class="location">'.$section['title'].'</span> &nbsp; ';
				} else {
					if (isset($section['url'])) {
						$output .= '<a href="'.str_repeat('../', $this->_depth).$section['url'].'">'.$section['title'].'</a> &nbsp; ';
					} else {
						$output .= $section['title'].' &nbsp; ';
					}
				}
			}
		}
		$output .= '</td><td class="short_title">'.$this->_doclet->getHeader().'</td>';
		$output .= "</tr>\n";
		$output .= "</table>\n\n";

		$output .= '<table width="100%" cellpadding="0" class="small_links">'."\n";
		$output .= "<tr>\n";
		$output .= "<td>\n";
		$output .= '<a href="'.str_repeat('../', $this->_depth).'index.html" target="_top">FRAMES</a>'."\n";
		$output .= '<a href="'.str_repeat('../', $this->_depth).$path.'" target="_top">NO FRAMES</a>'."\n";
		$output .= "</td>\n";
		$output .= "</tr>\n";
		if (get_class($this) == 'classwriter') {
			$output .= "<tr>\n";
			$output .= '<td>SUMMARY: &nbsp;<a href="#summary_field">FIELD</a> | <a href="#summary_method">METHOD</a> | <a href="#summary_constr">CONSTR</a></td>'."\n";
			$output .= '<td>DETAIL: &nbsp;<a href="#detail_field">FIELD</a> | <a href="#detail_method">METHOD</a> | <a href="#summary_constr">CONSTR</a></td>'."\n";
			$output .= "</tr>\n";
		} elseif (get_class($this) == 'functionwriter') {
			$output .= "<tr>\n";
			$output .= '<td>SUMMARY: &nbsp;<a href="#summary_function">FUNCTION</a></td>'."\n";
			$output .= '<td>DETAIL: &nbsp;<a href="#detail_function">FUNCTION</a></td>'."\n";
			$output .= "</tr>\n";
		} elseif (get_class($this) == 'globalwriter') {
			$output .= "<tr>\n";
			$output .= '<td>SUMMARY: &nbsp;<a href="#summary_global">GLOBAL</a></td>'."\n";
			$output .= '<td>DETAIL: &nbsp;<a href="#detail_global">GLOBAL</a></td>'."\n";
			$output .= "</tr>\n";
		}
		$output .= "</table>\n\n";

		return $output;
	}

	/** Write the HTML page to disk using the given path.
	 *
	 * @param str path The path to write the file to
	 * @param str title The title for this page
	 * @param bool shell Include the page shell in the output
	 */
	function _write($path, $title, $shell)
    {
		$phpdoctor =& $this->_doclet->phpdoctor();
		
		// make directories if they don't exist
		$dirs = explode('/', $path);
		array_pop($dirs);
		$testPath = $this->_doclet->destinationPath();
		foreach ($dirs as $dir) {
			$testPath .= $dir.'/';
			if (!is_dir($testPath)) {
                if (!@mkdir($testPath)) {
                    $phpdoctor->error(sprintf('Could not create directory "%s"', $testPath));
                    exit;
                }
            }
		}
		
		// write file
		$fp = fopen($this->_doclet->destinationPath().$path, 'w');
		if ($fp) {
			$phpdoctor->message('Writing "'.$path.'"');
			fwrite($fp, $this->_htmlHeader($title));
			if ($shell) fwrite($fp, $this->_shellHeader($path));
			fwrite($fp, $this->_output);
			if ($shell) fwrite($fp, $this->_shellFooter($path));
			fwrite($fp, $this->_htmlFooter());
			fclose($fp);
		} else {
			$phpdoctor->error('Could not write "'.$this->_doclet->destinationPath().$path.'"');
            exit;
		}
	}
	
	/** Format tags for output.
	 *
	 * @param Tag[] tags
	 * @return str The string representation of the elements doc tags
	 */
	function _processTags(&$tags)
    {
		$tagString = '';
		foreach ($tags as $key => $tag) {
			if ($key != '@text') {
				if (is_array($tag)) {
                    $hasText = FALSE;
                    foreach ($tag as $key => $tagFromGroup) {
                        if ($tagFromGroup->text() != '') {
                            $hasText = TRUE;
                        }
                    }
                    if ($hasText) {
                        $tagString .= '<dt>'.$tag[0]->displayName().":</dt>\n";
                        foreach ($tag as $tagFromGroup) {
                            $tagString .= '<dd>'.$tagFromGroup->text()."</dd>\n";
                        }
                    }
				} else {
					$text = $tag->text();
					if ($text != '') {
						$tagString .= '<dt>'.$tag->displayName().":</dt>\n";
						$tagString .= '<dd>'.$text."</dd>\n";
					} elseif ($tag->displayEmpty()) {
						$tagString .= '<dt>'.$tag->displayName().".</dt>\n";
					}
				}
			}
		}
        if ($tagString) {
            echo "<dl>\n", $tagString, "</dl>\n";
        }
	}
	
	/** Convert inline tags into a string for outputting.
	 *
	 * @param Tag tag The text tag to process
	 * @param bool first process first line of tag only
	 * @return str The string representation of the elements doc tags
	 */
	function _processInlineTags(&$tag, $first = FALSE)
    {
		if ($tag) {
			$description = '';
			if ($first) {
				$tags =& $tag->firstSentenceTags();
			} else {
				$tags =& $tag->inlineTags();
			}
			if ($tags) {
				foreach ($tags as $aTag) {
					if ($aTag) {
						$description .= $aTag->text().' ';
					}
				}
			}
            if ($first) {
                $description = $this->_stripBlockTags($description);
            }
			return substr($description, 0, -1);
		}
		return NULL;
	}
    
    /** Strip block level HTML tags from a string.
     *
     * @param str string
     * @return str
     */
    function _stripBlockTags($string)
    {
        return strip_tags($string, '<a><b><strong><i><em><code><q><acronym><abbr><ins><del><kbd><samp><sub><sup><tt><var><big><small>');
    }

}

?>
