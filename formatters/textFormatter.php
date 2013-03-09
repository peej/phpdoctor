<?php

/**
 * The formatter base class.
 *
 * @package PHPDoctor\Formatters
 */
class textFormatter
{

    /**
     * Returns the plain text value of the string, with all formatting information
     * removed.
     *
     * @param  str $text the raw input
     * @return str
     */
    public function toPlainText ($text)
    {
        return $this->_removeWhitespace($text);
    }

    /**
     * Returns the text with all recognized formatting directives applied. Meaningful
     * implementations are provided by subclasses.
     *
     * @param  str $text the raw input
     * @return str
     */
    public function toFormattedText ($text)
    {
        return $this->toPlainText($text);
    }

    /**
     * Removes whitespace around newlines.
     *
     * @param  str $text the raw input
     * @return str
     */
    public function _removeWhitespace($text)
    {
        $text = preg_replace("/[ \t]*\n[ \t]*/", "\n", $text);

        return $text;
    }

}
