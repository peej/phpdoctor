<?php
    
    require_once(dirname(__file__).DIRECTORY_SEPARATOR.'textFormatter.php');
    
    /**
     * The standard formatter. Basic implementation, just deals with unordered lists for now.
     *
     * @package PHPDoctor\Formatters
     */
    class htmlStandardFormatter extends TextFormatter
    {
        
        function toFormattedText($text)
        {
            $text = $this->_addListMarkupUL($text);
            $text = '<p>'.str_replace("\n\n", '</p><p>', $this->toPlainText($text)).'</p>';
            $text = str_replace('<ul>', "</p>\n<ul>", $text);
            $text = str_replace('</ul>', "</ul>\n<p>", $text);
            $text = $this->_removeWhitespace($text);
            return $text;
        }
        
        /**
         * Detects unordered lists and adds the necessary markup.
         * 
         * @param  string  $txt 			the text to parse and modify
         * @return string    
         */
        function _addListMarkupUL($txt)
        {
            // Create unordered lists. -, +, # and o are recogized as bullet points
            
            // $li_rx: regex capturing a list entry, including those extending over multiple lines and
            //         those padded with empty lines
            // $ul_rx: regex capturing an unordered list - at least two list entries required
            $li_rx = '^([ \t]+([\-+#o])[ \t]+)(\S.*(?:\n [ \t]+(?!\2)(?![ \t]).*|\n[ \t]*)*\n)';
            $ul_rx = "(?:$li_rx){2,}";
            
            $txt = preg_replace("/$ul_rx/m", "\n\n<ul>\n$0\n</ul>\n\n", $txt);
            
            if (preg_match_all("%<ul>.*?</ul>%s", $txt, $outerLists)) {
                $lists = preg_replace("/$li_rx/m", "<li>$3</li>", $outerLists[0]);
                $txt = str_replace($outerLists[0], $lists, $txt);
                
                // Cleanup: Making sure that the lists won't appear inside a <p> (by removing double newlines around ul tags)
                // and won't have empty paragraphs in between list items (by removing double newlines between different li tags).
                $txt = preg_replace('%\s*<ul>\s*(<li>.+?</li>)\s*</ul>\s*%s', "<ul>\n$1\n</ul>", $txt);
                $txt = preg_replace('%\s*</li>\s*%', "</li>\n", $txt);
                
                // Inside <li>s which contain multiple paragraphs, use <p>s with a css hook.
                preg_match_all("%<li>.*?</li>%s", $txt, $items);
                $adjustedItems = preg_replace("%[ \t]*\n[ \t]*\n[ \t]*%", '</p><p class="list">', $items[0]);
                $adjustedItems = preg_replace('%^<li>(.*?</p><p class="list">.*?)</li>$%s', "<li><p class=\"list\">$1</p></li>", $adjustedItems);
                $txt = str_replace($items[0], $adjustedItems, $txt);
                
            } 
            
            return $txt;
        }
        
    }
    
?>