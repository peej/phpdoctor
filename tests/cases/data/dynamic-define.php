<?php
/**
 * @package PHPDoctor\Tests\Parser
 */
class DynamicDefine {
    protected $foo = 'bar';
    const DENG = 1;

    /**
     * C'tor
     */
    public function __construct() {
        $kv = array('abc' => 'def');
        foreach ($kv as $key => $value) {
            define($key, $value);
        }
        $zz = 'foo';
    }

    private function moreCode() {
        define($this->foo, 'moo');
        return true;
    }

    var $xx;
}
