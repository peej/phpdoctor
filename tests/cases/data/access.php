<?php
/**
 * @package PHPDoctor\Tests\Parser
 */
class AccessLevel {
	
    /**
     * @access public
     */
    var $publicVar;
	
    /**
     * @access protected
     */
    var $protectedVar;
	
    /**
     * @access private
     */
    var $privateVar;
	
    /**
     * @access protected
     */
    function accessLevel() {}
    
	/**
	 * @access public
	 */
	function publicMethod() {}
	
	/**
	 * @access protected
	 */
	function protectedMethod() {}
	
	/**
	 * @access private
	 */
	function privateMethod() {}
	
}

