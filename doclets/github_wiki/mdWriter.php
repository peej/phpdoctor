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
 * @package PHPDoctor\Doclets\GithubWiki
 * @todo Refactor this class to MDWriter
 */
class MDWriter {

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

    /**
     *
     * @var string
     */
    var $_gitHubSourceDirectory = '';

    /** The doclet that created this object.
     *
     * @var doclet
     */
    var $_doclet;

    /** The directory structure depth. Used to calculate relative paths.
     *
     * @var int
     */
    var $_depth = 0;

    /** The <body> id attribute value, used for selecting style.
     *
     * @var str
     */
    var $_id = "overview";

    /** The output body.
     *
     * @var str
     */
    var $_output = "";

    /** Writer constructor.
     */
    function mdWriter(&$doclet) {
        $this->_doclet = & $doclet;

        //$this->_doclet->phpdoctor()->_options["github_repository"]
        $this->_gitHubRepository = $this->_doclet->phpdoctor()->_options["github_repository"];

        if (isset($this->_doclet->phpdoctor()->_options["github_branch"])) {
            $this->_gitHubBranch = $this->_doclet->phpdoctor()->_options["github_branch"];
        }

		if(isset($this->_doclet->phpdoctor()->_options['github_source_directory'])) {
			$this->_gitHubSourceDirectory = $this->_doclet->phpdoctor()->_options['github_source_directory'];
		}
    }

    function getFileBaseURL() {
		return "/$this->_gitHubRepository/wiki/";
    }

    function getDirBaseURL() {
		return "/$this->_gitHubRepository/wiki";
    }

    function getSourcesBaseURL() {
        return "/$this->_gitHubRepository/blob/$this->_gitHubBranch/$this->_gitHubSourceDirectory/";
    }

    function _sourceLocation($doc) {
        if (isset($this->_gitHubRepository)) {
            $url = str_replace(DIRECTORY_SEPARATOR, '/', $doc->sourceFilename());
            echo "\n<a href=\"{$this->getSourcesBaseURL()}$url#L{$doc->sourceLine()}\" target='_blank'>{$doc->location()}</a>\n\n";
        } else {
            echo "\n<div class=\"location\">{$doc->location()}</div>\n";
        }
    }

    /** Write the HTML page to disk using the given path.
     *
     * @param str path The path to write the file to
     * @param str title The title for this page
     * @param bool shell Include the page shell in the output
     */
    function _write($path) {
        $phpdoctor = & $this->_doclet->phpdoctor();

        // make directory separators suitable to this platform
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);

        // make directories if they don"t exist
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
        $fp = fopen($this->_doclet->destinationPath() . $path, "w");
        if ($fp) {
            $phpdoctor->message('Writing "' . $path . '"');
            fwrite($fp, $this->_output);
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
        $tagString = "";
        foreach ($tags as $key => $tag) {
            if ($key != "@text") {
                if (is_array($tag)) {
                    $hasText = FALSE;
                    foreach ($tag as $key => $tagFromGroup) {
                        if ($tagFromGroup->text($this->_doclet) != "") {
                            $hasText = TRUE;
                        }
                    }
                    if ($hasText) {
                        $tagString .= "<dt>" . $tag[0]->displayName() . ":</dt>\n";
                        foreach ($tag as $tagFromGroup) {
                            $tagString .= "<dd>" . $tagFromGroup->text($this->_doclet) . "</dd>\n";
                        }
                    }
                } else {
                    $text = $tag->text($this->_doclet);
                    if ($text != "") {
                        $tagString .= "<dt>" . $tag->displayName() . ":</dt>\n";
                        $tagString .= "<dd>" . $text . "</dd>\n";
                    } elseif ($tag->displayEmpty()) {
                        $tagString .= "<dt>" . $tag->displayName() . ".</dt>\n";
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
        $description = "";
        if (is_array($tag)) {
			$tag = $tag[0];
		}

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

    function _fieldSignature($element) {
        $type = "";
        $classDoc = & $element->_type->asClassDoc();
        if ($classDoc) {
            $type = "<a href='{$this->_asURL($classDoc)}'>{$classDoc->name()}{$element->_type->dimension()}</a>";
        } else {
            $type = $element->_type->typeName() . $element->_type->dimension();
        }
        return "<span class='k'>{$element->modifiers(FALSE)}</span> <span class='nx'>{$type}</span>";
    }

    function _methodSignature($method) {
        $classDoc = & $method->_returnType->asClassDoc();
        $type = "";
        if ($classDoc) {
            $type = "<a href='{$this->_asURL($classDoc)}>{$classDoc->name()}{$method->_returnType->dimension()}</a>";
        } else {
            $type = $method->_returnType->typeName() . $method->_returnType->dimension();
        }
        return "<span class='k'>{$method->modifiers(FALSE)}</span> <span class='nx'>{$type}</span>";
    }

    function _flatSignature($method) {
        $signature = '';
        foreach ($method->_parameters as $param) {
            $type = & $param->type();
            $classDoc = & $type->asClassDoc();
            if ($classDoc) {
                $signature .= '<a href="' . "{$this->_asURL($classDoc)}" . '">' . $classDoc->name() . '</a> ' . $param->name() . ', ';
            } else {
                $signature .= $type->typeName() . ' ' . $param->name() . ', ';
            }
        }
        return '(' . substr($signature, 0, -2) . ')';
    }

    function _asURL($element) {
        return $this->getFileBaseURL() . $this->_asPath($element);
    }

    function _asPath($element) {
		if (isset($element->_package)){
			$result = $this->_normalize($element->_package);
		}else if(isset ($element->_name)){
			$result = $this->_normalize($element->_name);
		}

        $hashName = strtolower($element->_name);

        if ($element->isClass() || $element->isInterface() || $element->isException()) {
            $result .= "{$element->name()}";
        } else if ($element->isField()) {
            $class = & $element->containingClass();
            if ($class) {
                $result .= "{$class->name()}#{$hashName}";
            } else {
                $result .= "package-globals#{$hashName}";
            }
        } else if ($element->isConstructor() || $element->isMethod()) {
            $class = & $element->containingClass();
            if ($class) {
                $result .= "{$class->name()}#{$hashName}";
            } else {
                $result .="package-functions#{$hashName}";
            }
        } else if ($element->isGlobal()) {
            $result .="package-globals#{$hashName}";
        } else if ($element->isFunction()) {
            $result .="package-functions#{$hashName}";
        }

        return strtolower($result);
    }

	function _normalize($name) {
		return strtolower(str_replace('.', '_', str_replace('\\', '_', $name)) . '_');
	}
}

?>
