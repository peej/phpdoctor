<?php

/**
 * @package PHPDoctor.Tests
 */
class TestPHP5 extends UnitTestCase
{
	
	function testPHP5() {
        $this->UnitTestCase('PHP5 tests');
    }
	
	function testPHP5Test()
	{
		ob_start();
		passthru('php phpdoc.php tests/php5-test.ini');
		$results = ob_get_contents();
		ob_end_clean();
		
		$this->assertTrue(strpos($results, 'public interface anInterface'));
		$this->assertTrue(strpos($results, 'public final str aConst = \'const\''));
		$this->assertTrue(strpos($results, 'public final int anIntConst'));
		$this->assertTrue(strpos($results, 'private final int aPrivateIntConst'));
		$this->assertTrue(strpos($results, 'public final int multipleConsts1 = 1'));
		$this->assertTrue(strpos($results, 'public final int multipleConsts2 = 2'));
		$this->assertTrue(strpos($results, 'public final int multipleConsts3 = 3'));
		
		$this->assertTrue(strpos($results, 'private interface aPrivateInterface'));
		$this->assertTrue(strpos($results, 'protected interface aProtectedInterface'));
		
		$this->assertTrue(strpos($results, 'public class aClass implements anInterface'));
		$this->assertTrue(strpos($results, 'public mixed $aVar'));
		$this->assertTrue(strpos($results, 'public int $anIntVar'));
		$this->assertTrue(strpos($results, 'private int $aPrivateIntVar'));
		$this->assertTrue(strpos($results, 'public mixed $multipleVars1 = 1'));
		$this->assertTrue(strpos($results, 'public mixed $multipleVars2'));
		$this->assertTrue(strpos($results, 'public mixed $multipleVars3 = 3'));
		$this->assertTrue(strpos($results, 'public mixed $aVarWithValue = 1'));
		$this->assertTrue(strpos($results, 'public mixed $anArrayVar = array(4, 5, 6)'));
		
		$this->assertTrue(strpos($results, 'public void aFunction()'));
		$this->assertTrue(strpos($results, 'public void aFunctionWithParams(mixed one, mixed two)'));
		$this->assertTrue(strpos($results, 'mixed $one'));
		$this->assertTrue(strpos($results, 'mixed $two'));
		$this->assertTrue(strpos($results, 'private bool aPrivateFunctionWithParams(str one, int two)'));
		$this->assertTrue(strpos($results, 'str $one'));
		$this->assertTrue(strpos($results, 'int $two'));
		
		$this->assertTrue(strpos($results, 'final int THREE = 3'));
		
		$this->assertTrue(strpos($results, 'public class childClass extends aClass'));
        
        $this->assertTrue(strpos($results, 'final int CONSTANT = 1'));
        $this->assertTrue(strpos($results, 'final int CONSTANT2 = 2'));
        $this->assertTrue(strpos($results, 'final str CONSTANT3 = \'three\''));
		
	}

}

?>
