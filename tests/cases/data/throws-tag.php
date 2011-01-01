<?php
/**
 * @package PHPDoctor\Tests\Parser
 */
class ThrowsTag {
	
    /**
     * @throws testException Some kind of exception occurred
     */
    function throwsTag() {}

}

class testException extends Exception {}
