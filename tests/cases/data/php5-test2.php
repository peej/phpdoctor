<?php
namespace PHPDoctor\Tests\MyNamespace;

/**
 * Duplicate class name in a different namespace
 */
class duplicateClass { }

/**
 * Non-explicit parameter doctags
 *
 * @param string
 * @param string
 * @param bool
 */
function NonExplicitParameterDoctags($field, $value, $default) { }
