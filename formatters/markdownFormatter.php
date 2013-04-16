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
        $autoloader = realpath(dirname(__file__).'/../vendor/autoload.php');
        
        if (file_exists('Markdown.php')) {
            require_once 'Markdown.php';
        } else if (file_exists($autoloader)) {
            require_once $autoloader;
        }
        
        $md = new \Michelf\Markdown;
        $md->empty_element_suffix = '>';
        return $md->transform(trim($text));
    }
}
