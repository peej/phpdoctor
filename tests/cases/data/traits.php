<?php

/**
 * @package PHPDoctor\Tests\Parser
 */
trait myTrait {
    function traitMethod() {}
}

/**
 * @package PHPDoctor\Tests\Parser
 */
trait myOtherTrait {
    function anotherTraitMethod() {}
}

/**
 * @package PHPDoctor\Tests\Parser
 */
class traitTestClass {
    use myTrait, myOtherTrait;
}