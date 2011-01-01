<?php
/**
 * File level comment
 *
 * @package PHPDoctor\Tests\FileLevel
 */

/**
 * This is some text.
 *
 * A link to a fully qualified class {@link PHPDoctor\Tests\FileLevel\aPrivateInterface} somewhere else.
 *
 * A link to a non-qualified class {@link aPrivateInterface} somewhere else.
 *
 * A link to a non-existant class {@link aNonExistantClass} somewhere else.
 *
 * A link to a class in a non-existant package {@link PHPDoctor\aPrivateInterface} somewhere else.
 *
 * A link to an element in a fully qualified class {@link PHPDoctor\Tests\Data\aClass#aVar} somewhere else.
 *
 * A link to an element in a fully qualified class {@link PHPDoctor\Tests\Data\aClass::aVar} (alternative syntax) somewhere else.
 *
 * A link to an element with $ in a fully qualified class {@link PHPDoctor\Tests\Data\aClass#$aVar} somewhere else.
 *
 * A link to a method in a fully qualified class {@link PHPDoctor\Tests\Data\aClass#aFunction} somewhere else.
 *
 * A link to a rooted fully qualified class {@link \PHPDoctor\Tests\Data\aClass} somewhere else.
 *
 * A link to a method with parenthesis in a fully qualified class {@link PHPDoctor\Tests\Data\aClass#aFunction()} somewhere else.
 *
 * A link to a website {@link http://www.google.com} somewhere else.
 *
 * A link to a website {@link http://www.google.com Google} with a name.
 *
 * Another line
 *
 * @package PHPDoctor\Tests\Data
 * @see aPrivateInterface Something else
 * @todo More stuff
 */
interface anInterface {
	
	const aConst = 'const';
	
	/**
	 * Some words about this constant
	 *
	 * @var int
	 */
	const anIntConst = 0;
	
	/**
	 * @var int
	 * @access private
	 */
	const aPrivateIntConst = 0;
	
	const multipleConsts1 = 1, multipleConsts2 = 2, multipleConsts3 = 3;
	
}

/**
 * This is aClass that implements anInterface. This is a second part of the
 * docComment to be part of the long description after the first fullstop.
 *
 * This line however should always be only in the long description.
 *
 * @package PHPDoctor\Tests\Data
 */
class aClass implements anInterface {
	
    const booleanClassConstant = TRUE;
    const stringClassConstant = 'str';
    const integerClassConstant = 3;
    
	var $aVar;
	
	/**
	 * @var int
	 */
	var $anIntVar;
	
	/**
	 * @var int
	 * @access private
	 */
	var $aPrivateIntVar;
	
	var $multipleVars1 = 1, $multipleVars2, $multipleVars3 = 3;
	
	var $aVarWithValue = 1;
    
    var $aVarWithStringValue = "one";
	
	var $anArrayVar = array(4, 5, 6);
	
	var $varContainingHTMLToEscape = '<strong>Escape me</strong>';
	
	function aClass() {}
	
	/**
	 * This is a method of the class aClass
	 *
	 * @throws Exception
	 */
	function aMethod() {
		/**
		 * @var int
		 */
		define('THREE', 3);
	}
	
	function aMethodWithParams($one, $two) {}
	
	/**
	 * @access private
	 * @param str one
	 * @param int two
	 * @return bool
	 */
	function aPrivateMethodWithParams($one, $two) {}
	
	function aMethodWithGuessedParamTypes($stringParam = 'string', $integerParam = 2, $booleanParam = TRUE) {}
	
}

/**
 * @package PHPDoctor\Tests\Data
 */
class childClass extends aClass {
    
    function __construct($foo, $bar = NULL) {}
    function __destruct() {}
    
}

require_once 'php5-test2.php';
class duplicateClass extends PHPDoctor\Tests\MyNamespace\duplicateClass {}


function aFunction() { }
function aFunctionWithParams($one, $two) { }
function aFunctionWithTypeHints(int $three, string $four) {}
function &functionThatReturnsAReference() {}

/**
 * @namespace PHPDoctor\Tests\Data
 */
class anotherClassWithSameMemberAsAnotherClass {
    var $aVarWithValue = 2;
    
    /**
     * This is a
     * multi-line description
     * @param str foo This
                      is
     **               foo
     * @return str And a multi-line
     *             tag description
     */
    function aFunction($foo) {
	}

}


/**
 * Duplicate interface name in a different namespace
 *
 * @package PHPDoctor\Tests\MyNamespace
 */
interface anInterface { }

/**
 * This class implements the namespaced interface PHPDoctor\Tests\MyNamespace\anInterface
 */
class implementAnExternalInterface implements PHPDoctor\Tests\MyNamespace\anInterface { }

define('CONSTANT', 1);
define( 'CONSTANT2' , 2 );
define(  'CONSTANT3'  ,  'three'  );

class myException extends Exception { }

/**
 * This is a function that throws a custom exception
 *
 * @throws myException Some kind of exception occurred
 */
function customExceptionThrower() { }

/**
 * Test inheriting of class doccomments 
 */
class inheritTest {
    
    /**
     * Test inheriting of field doccomments 
     */
    var $aField;
    
    /**
     * Test inheriting of method doccomments 
     */
    function aMethod() { }
    
}

/**
 * A child class doccomment
 *
 * {@inheritDoc}
 */
class inheritTestChild extends inheritTest {
    
    /**
     * Parent field comment is: {@inheritDoc}
     */
    var $aField;
    
    /**
     * Parent method comment is: {@inheritDoc}
     */
    function aMethod() { }
    
}

/**
 * Test inheriting of interface doccomments 
 */
interface inheritInterfaceTest {
    
    /**
     * Test inheriting of interface field doccomments 
     */
    var $anInterfaceField;
    
    /**
     * Test inheriting of interface method doccomments
     *
     * @throws myInterfaceException
     */
    function anInterfaceMethod();
    
}

/**
 * A child class doccomment
 *
 * {@inheritDoc}
 */
class inheritTestImplements implements inheritInterfaceTest {
    
    /**
     * Parent field comment is: {@inheritDoc}
     */
    var $anInterfaceField;
    
    /**
     * Parent method comment is: {@inheritDoc}
     */
    function anInterfaceMethod() { }
    
}

?>
