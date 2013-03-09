<?php

/**
 * @package PHPDoctor\Tests
 */
class TestParser extends DoctorTestCase
{
    var $output;

    function TestParser() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('Parser tests');

        $this->setIniFile('parser.ini');
        $this->output = $this->runPhpDoctor();
    }

    function testInterface() {
        $this->assertTrue(strpos($this->output, 'public interface PHPDoctor\\Tests\\Data\\anInterface'));
        $this->assertTrue(strpos($this->output, 'public final static str PHPDoctor\\Tests\\Data\\aConst = \'const\''));
        $this->assertTrue(strpos($this->output, 'public final static int PHPDoctor\\Tests\\Data\\anIntConst'));
        $this->assertTrue(strpos($this->output, 'private final static int PHPDoctor\\Tests\\Data\\aPrivateIntConst'));
        $this->assertTrue(strpos($this->output, 'public final static int PHPDoctor\\Tests\\Data\\multipleConsts1 = 1'));
        $this->assertTrue(strpos($this->output, 'public final static int PHPDoctor\\Tests\\Data\\multipleConsts2 = 2'));
        $this->assertTrue(strpos($this->output, 'public final static int PHPDoctor\\Tests\\Data\\multipleConsts3 = 3'));
    }

    function testClass() {
        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\Data\\aClass implements PHPDoctor\\Tests\\Data\\anInterface'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$aVar'));
        $this->assertTrue(strpos($this->output, 'public int PHPDoctor\\Tests\\Data\\$anIntVar'));
        $this->assertTrue(strpos($this->output, 'private int PHPDoctor\\Tests\\Data\\$aPrivateIntVar'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars1 = 1'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars2'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$multipleVars3 = 3'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$aVarWithValue = 1'));
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$anArrayVar = array(4, 5, 6)'));

        $this->assertTrue(strpos($this->output, 'final static bool PHPDoctor\\Tests\\Data\\booleanClassConstant = TRUE'));
        $this->assertTrue(strpos($this->output, 'final static str PHPDoctor\\Tests\\Data\\stringClassConstant = \'str\''));
        $this->assertTrue(strpos($this->output, 'final static int PHPDoctor\\Tests\\Data\\integerClassConstant = 3'));
    }

    function testFunction() {
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\FileLevel\\aFunction()'));
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\FileLevel\\aFunctionWithParams(mixed one, mixed two)'));
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\FileLevel\\aFunctionWithTypeHints(int three, string four)'));
        $this->assertTrue(strpos($this->output, 'throws myException'));
        $this->assertTrue(strpos($this->output, 'throws myInterfaceException'));
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\FileLevel\\functionThatReturnsAReference()'));
    }

    function testMethod() {
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\Data\\aMethod()'));
        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\Data\\aMethodWithParams(mixed one, mixed two)'));
        $this->assertTrue(strpos($this->output, 'mixed PHPDoctor\\Tests\\Data\\$one'));
        $this->assertTrue(strpos($this->output, 'mixed PHPDoctor\\Tests\\Data\\$two'));
        $this->assertTrue(strpos($this->output, 'private bool PHPDoctor\\Tests\\Data\\aPrivateMethodWithParams(str one, int two)'));
        $this->assertTrue(strpos($this->output, 'str PHPDoctor\\Tests\\Data\\$one'));
        $this->assertTrue(strpos($this->output, 'int PHPDoctor\\Tests\\Data\\$two'));

        $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\Data\\aMethodWithGuessedParamTypes(str stringParam, int integerParam, bool booleanParam)'));
    }

    function testConstant() {
        $this->assertTrue(strpos($this->output, 'final int PHPDoctor\\Tests\\FileLevel\\CONSTANT = 1'));
        $this->assertTrue(strpos($this->output, 'final int PHPDoctor\\Tests\\FileLevel\\CONSTANT2 = 2'));
        $this->assertTrue(strpos($this->output, 'final str PHPDoctor\\Tests\\FileLevel\\CONSTANT3 = \'three\''));
        // global constant defined within another program element
        $this->assertTrue(strpos($this->output, 'final int PHPDoctor\\Tests\\FileLevel\\THREE = 3'));
    }

    function testInheritance() {
        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\Data\\childClass extends PHPDoctor\\Tests\\Data\\aClass'));

        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\FileLevel\\inheritTestChild extends PHPDoctor\\Tests\\FileLevel\\inheritTest'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of class doccomments'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of method doccomments'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of field doccomments'));

        $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\FileLevel\\inheritTestImplements implements PHPDoctor\\Tests\\FileLevel\\inheritInterfaceTest'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of interface doccomments'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of interface method doccomments'));
        $this->assertTrue(strpos($this->output, 'InheritDoc: Test inheriting of interface field doccomments'));
    }

    function testHTMLInVariable() {
        $this->assertTrue(strpos($this->output, 'public mixed PHPDoctor\\Tests\\Data\\$varContainingHTMLToEscape = \'<strong>Escape me</strong>\''));
    }

    function testExtendingClassWithSameNameInDifferentNamespace() {
        if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\FileLevel\\duplicateClass extends PHPDoctor\\Tests\\MyNamespace\\duplicateClass'));
            $this->assertTrue(strpos($this->output, 'public class PHPDoctor\\Tests\\MyNamespace\\duplicateClass'));
        }
    }

    function testNonExplicitParameterDoctags() {
        if (version_compare(EXEC_VERSION, '5.3.0', '>=')) {
            $this->assertTrue(strpos($this->output, 'public void PHPDoctor\\Tests\\MyNamespace\\NonExplicitParameterDoctags(string field, string value, bool default)'));
        }
    }

}
