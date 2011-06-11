<?php
/**
 * @package PHPDoctor\Tests\Parser
 */
class ThrowsTag {
	
    /**
     * @param int one
     * @param str two
     * @throws testException Some kind of exception occurred
     */
    function throwsTag($one, $two) {}

}

/**
 * @package PHPDoctor\Tests\Parser
 */
class testException extends Exception {}
