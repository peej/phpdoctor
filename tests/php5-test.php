<?php

/**
 * This is some text and a link {@link aPrivateInterface} somewhere else.
 *
 * Another line
 *
 * @package PHPDoctor.Tests.Data
 * @see aPrivateInterface Something else
 * @todo More stuff
 */
interface anInterface {
	
	const aConst = 'const';
	
	/**
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
 * @package PHPDoctor.Tests.Data
 * @access private
 */
interface aPrivateInterface {}

/**
 * @access protected
 * @package PHPDoctor.Tests.Data
 */
interface aProtectedInterface {}

/**
 * @package PHPDoctor.Tests.Data
 */
class aClass implements anInterface {
	
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
	
	function aClass() {}
	
	function aFunction() {
		/**
		 * @var int
		 */
		define('THREE', 3);
	}
	
	function aFunctionWithParams($one, $two) {}
	
	/**
	 * @access private
	 * @param str one
	 * @param int two
	 * @return bool
	 */
	function aPrivateFunctionWithParams($one, $two) {}
	
}

/**
 * @package PHPDoctor.Tests.Data
 */
class childClass extends aClass {}

/**
 * @package PHPDoctor.Tests.Data
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

define('CONSTANT', 1);
define( 'CONSTANT2' , 2 );
define(  'CONSTANT3'  ,  'three'  );
define(  'CONSTANT4'  ,  array('woo', 'yay')  );

?>
