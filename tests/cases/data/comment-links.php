<?php

/**
 * @package PHPDoctor\Tests\LinkTest
 */
class linkTest {

    var $var;
    
    function func() {}

}

/**
 * @package PHPDoctor\Tests\LinkTest2
 */
class linkTest {}


/**
 * This is some text.
 *
 * A link to a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest} somewhere else.
 *
 * A link to a non-qualified class {@link linkTest} somewhere else.
 *
 * A link to a non-existant class {@link lonkTest} somewhere else.
 *
 * A link to a class in a non-existant package {@link PHPDoctor\linkTest} somewhere else.
 *
 * A link to an element in a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest#var} somewhere else.
 *
 * A link to an element in a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest::var} (alternative syntax) somewhere else.
 *
 * A link to an element with $ in a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest#$var} somewhere else.
 *
 * A link to a method in a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest#func} somewhere else.
 *
 * A link to a method with parenthesis in a fully qualified class {@link PHPDoctor\Tests\LinkTest\linkTest#func()} somewhere else.
 *
 * A link to a rooted fully qualified class {@link \PHPDoctor\Tests\LinkTest\linkTest} somewhere else.
 *
 * A link to a website {@link http://www.google.com} somewhere else.
 *
 * A link to a website {@link http://www.google.com Google} with a name.
 *
 * Another line
 *
 * @package PHPDoctor\Tests\LinkTest
 * @see linkTest Something else
 * @todo More stuff
 */
class commentLinks {}

