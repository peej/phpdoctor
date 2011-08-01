<?php
/**
 * @package PHPDoctor\Tests\Parser
 */
class AccessLevelPHP5 {
	
    public $publicVar;
	
    protected $protectedVar;
	
    private $privateVar;
	
    protected function __construct() {}
    
	public function publicMethod() {}
	
	protected function protectedMethod() {}
	
	private function privateMethod() {}
	
}

