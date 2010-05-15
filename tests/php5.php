<?php

/**
 * @package PHPDoctor\Tests
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
		
		#echo $results; die;
		
		$this->assertTrue(strpos($results, 'public interface PHPDoctor\\Tests\\Data\\anInterface'));
		$this->assertTrue(strpos($results, 'public final str PHPDoctor\\Tests\\Data\\aConst = \'const\''));
		$this->assertTrue(strpos($results, 'public final int PHPDoctor\\Tests\\Data\\anIntConst'));
		$this->assertTrue(strpos($results, 'private final int PHPDoctor\\Tests\\Data\\aPrivateIntConst'));
		$this->assertTrue(strpos($results, 'public final int PHPDoctor\\Tests\\Data\\multipleConsts1 = 1'));
		$this->assertTrue(strpos($results, 'public final int PHPDoctor\\Tests\\Data\\multipleConsts2 = 2'));
		$this->assertTrue(strpos($results, 'public final int PHPDoctor\\Tests\\Data\\multipleConsts3 = 3'));
		
		$this->assertTrue(strpos($results, 'private interface PHPDoctor\\Tests\\FileLevel\\aPrivateInterface'));
		$this->assertTrue(strpos($results, 'protected interface PHPDoctor\\Tests\\Data\\aProtectedInterface'));
		
		$this->assertTrue(strpos($results, 'public class PHPDoctor\\Tests\\Data\\aClass implements PHPDoctor\\Tests\\Data\\anInterface'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$aVar'));
		$this->assertTrue(strpos($results, 'public int PHPDoctor\\Tests\\Data\\$anIntVar'));
		$this->assertTrue(strpos($results, 'private int PHPDoctor\\Tests\\Data\\$aPrivateIntVar'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars1 = 1'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars2'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars3 = 3'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$aVarWithValue = 1'));
		$this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$anArrayVar = array(4, 5, 6)'));
		
		$this->assertTrue(strpos($results, 'public void PHPDoctor\\Tests\\Data\\aFunction()'));
		$this->assertTrue(strpos($results, 'public void PHPDoctor\\Tests\\Data\\aFunctionWithParams(mixed one, mixed two)'));
		$this->assertTrue(strpos($results, 'mixed PHPDoctor\\Tests\\Data\\$one'));
		$this->assertTrue(strpos($results, 'mixed PHPDoctor\\Tests\\Data\\$two'));
		$this->assertTrue(strpos($results, 'private bool PHPDoctor\\Tests\\Data\\aPrivateFunctionWithParams(str one, int two)'));
		$this->assertTrue(strpos($results, 'str PHPDoctor\\Tests\\Data\\$one'));
		$this->assertTrue(strpos($results, 'int PHPDoctor\\Tests\\Data\\$two'));
		
		$this->assertTrue(strpos($results, 'final int PHPDoctor\\Tests\\FileLevel\\THREE = 3'));
		
		$this->assertTrue(strpos($results, 'public class PHPDoctor\\Tests\\Data\\childClass extends PHPDoctor\\Tests\\Data\\aClass'));
        
        $this->assertTrue(strpos($results, 'final int PHPDoctor\\Tests\\FileLevel\\CONSTANT = 1'));
        $this->assertTrue(strpos($results, 'final int PHPDoctor\\Tests\\FileLevel\\CONSTANT2 = 2'));
        $this->assertTrue(strpos($results, 'final str PHPDoctor\\Tests\\FileLevel\\CONSTANT3 = \'three\''));
        
        //testEscapeHTMLInVariable
        $this->assertTrue(strpos($results, 'public mixed PHPDoctor\\Tests\\Data\\$varContainingHTMLToEscape = \'<strong>Escape me</strong>\''));
	}

}

?>
