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

/** This generates the index.md file used for presenting the frame-formated
 * "cover page" of the API documentation.
 *
 * @package PHPDoctor\Doclets\Standard
 */
class HTMLWriter {

    /**
     *
     * @var string
     */
    var $_gitHubRepository;
    /**
     *
     * @var string 
     */
    var $_gitHubBranch = "master";
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
    function htmlWriter(&$doclet) {
        $this->_doclet = & $doclet;
        
        $this->_gitHubRepository = $this->_doclet->phpdoctor()->_options['github_repository'];
        
        if(isset($this->_doclet->phpdoctor()->_options['github_branch'])){
            $this->_gitHubBranch = $this->_doclet->phpdoctor()->_options['github_branch'];
        }
    }
    
    function getFileBaseURL() {
        return "$this->_gitHubRepository/blob/$this->_gitHubBranch/";
    }
    
    function getDirBaseURL() {
        return "$this->_gitHubRepository/tree/$this->_gitHubBranch/";
    }

    function _sourceLocation($doc) {
        if ($this->_doclet->includeSource()) {
            $url = str_replace(DIRECTORY_SEPARATOR, '/', $doc->sourceFilename());
            echo '<a href="', $this->getFileBaseURL(), 'source/', $url, '.md#line', $doc->sourceLine(), '" class="location">', $doc->location(), "</a>\n\n";
        } else {
            echo '<div class="location">', $doc->location(), "</div>\n";
        }
    }

    /** Write the HTML page to disk using the given path.
     *
     * @param str path The path to write the file to
     * @param str title The title for this page
     * @param bool shell Include the page shell in the output
     */
    function _write($path, $title, $shell) {
        $phpdoctor = & $this->_doclet->phpdoctor();

        // make directory separators suitable to this platform
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        // make directories if they don't exist
        $dirs = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($dirs);
        $testPath = $this->_doclet->destinationPath();
        foreach ($dirs as $dir) {
            $testPath .= $dir . DIRECTORY_SEPARATOR;
            if (!is_dir($testPath)) {
                if (!@mkdir($testPath)) {
                    $phpdoctor->error(sprintf('Could not create directory "%s"', $testPath));
                    exit;
                }
            }
        }

        // write file
        $fp = fopen($this->_doclet->destinationPath() . $path, 'w');
        if ($fp) {
            $phpdoctor->message('Writing "' . $path . '"');
//            fwrite($fp, $this->_htmlHeader($title));
//            if ($shell)
//                fwrite($fp, $this->_shellHeader($path));
            fwrite($fp, $this->_output);
//            if ($shell)
//                fwrite($fp, $this->_shellFooter($path));
//            fwrite($fp, $this->_htmlFooter());
            fclose($fp);
        } else {
            $phpdoctor->error('Could not write "' . $this->_doclet->destinationPath() . $path . '"');
            exit;
        }
    }

    /** Format tags for output.
     *
     * @param Tag[] tags
     * @return str The string representation of the elements doc tags
     */
    function _processTags(&$tags) {
        $tagString = '';
        foreach ($tags as $key => $tag) {
            if ($key != '@text') {
                if (is_array($tag)) {
                    $hasText = FALSE;
                    foreach ($tag as $key => $tagFromGroup) {
                        if ($tagFromGroup->text($this->_doclet) != '') {
                            $hasText = TRUE;
                        }
                    }
                    if ($hasText) {
                        $tagString .= '<dt>' . $tag[0]->displayName() . ":</dt>\n";
                        foreach ($tag as $tagFromGroup) {
                            $tagString .= '<dd>' . $tagFromGroup->text($this->_doclet) . "</dd>\n";
                        }
                    }
                } else {
                    $text = $tag->text($this->_doclet);
                    if ($text != '') {
                        $tagString .= '<dt>' . $tag->displayName() . ":</dt>\n";
                        $tagString .= '<dd>' . $text . "</dd>\n";
                    } elseif ($tag->displayEmpty()) {
                        $tagString .= '<dt>' . $tag->displayName() . ".</dt>\n";
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
     * @param bool first Process first line of tag only
     * @return str The string representation of the elements doc tags
     */
    function _processInlineTags(&$tag, $first = FALSE) {
        $description = '';
        if (is_array($tag))
            $tag = $tag[0];
        if (is_object($tag)) {
            if ($first) {
                $tags = & $tag->firstSentenceTags($this->_doclet);
            } else {
                $tags = & $tag->inlineTags($this->_doclet);
            }
            if ($tags) {
                foreach ($tags as $aTag) {
                    if ($aTag) {
                        $description .= $aTag->text($this->_doclet);
                    }
                }
            }
            return $this->_doclet->formatter->toFormattedText($description);
        }
        return NULL;
    }

}

?>
