<?php
    
    /**
     * class LastLine
     *
     * @package PHPDoctor\Tests
     */
    class LastLine {
        
        /**
         * Tag in last line, with description
         * 
         * @return int    a return tag on the last line
         */
        public function lastLineTagWithDescription () {
            
            $foo = 1;
            return $foo;
            
        }
        
        /**
         * Tag in last line, without description
         * 
         * @return int
         */
        public function lastLineTagWithoutDescription () {
            
            $foo = 1;
            return $foo;
            
        }
        
        /**
         * Last line empty, tag with description
         * 
         * @return int    a return tag followed by an empty line
         * 
         */
        public function lastLineEmptyWithDescription () {
            
            $foo = 1;
            return $foo;
            
        }
        
        /**
         * Last line empty, tag without description
         * 
         * @return bool
         * 
         */
        public function lastLineEmptyWithoutDescription () {
            
            $foo = true;
            return $foo;
            
        }
        
        /**
         * Last two lines empty, tag with description
         * 
         * @return int  a return tag followed by two empty lines
         * 
         * 
         */
        public function lastTwoLinesEmptyWithDescription () {
            
            $foo = 1;
            return $foo;
            
        }
        
        /**
         * Last two lines empty, tag without description
         * 
         * @return array
         * 
         * 
         */
        public function lastTwoLinesEmptyWithoutDescription () {
            
            $foo = array();
            return $foo;
            
        }
        
    }
    
?>