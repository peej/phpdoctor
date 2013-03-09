<?php

require_once(dirname(__file__).DIRECTORY_SEPARATOR.'textFormatter.php');

/**
 * Use the PHP Markdown parser to format text
 * http://michelf.com/projects/php-markdown/
 *
 * @package PHPDoctor\Formatters
 */
class markdownFormatter extends TextFormatter
{

    public function toFormattedText($text)
    {
        require_once 'markdown.php';
        @define('MARKDOWN_EMPTY_ELEMENT_SUFFIX', '>');

        return Markdown($text);
    }
}
