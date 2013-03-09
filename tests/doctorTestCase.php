<?php

/**
 * Base test class to run PHPDoctor and capture output
 *
 * @package PHPDoctor\Tests
 */
class DoctorTestCase extends UnitTestCase {
    var $iniPath, $appDir, $testDir, $caseDir, $iniDir, $outputDir, $tempDir;

    /**
     * Create new instance.
     */
    function DoctorTestCase ($label = false) {
        $this->__construct($label);
    }

    /**
     * Create new instance.
     */
    function __construct($label = false) {
        parent::__construct($label);

        $this->appDir    = dirname(dirname(__file__)).DIRECTORY_SEPARATOR;
        $this->testDir   = $this->appDir.'tests'.DIRECTORY_SEPARATOR;
        $this->caseDir   = $this->testDir.'cases'.DIRECTORY_SEPARATOR;
        $this->iniDir 	 = $this->caseDir.'ini'.DIRECTORY_SEPARATOR;
        //$tmpBase = tempnam(sys_get_temp_dir(), 'pdtmp_');
        $tmpBase = $this->caseDir;
        $this->outputDir = $tmpBase.'output'.DIRECTORY_SEPARATOR;
        $this->tempDir   = $tmpBase.'tmp'.DIRECTORY_SEPARATOR;

        // ensure all ready for tests to run
        $this->clearOutputDir();
        $this->clearTempDir();
    }

    function setIniFile( $iniFileName ) {
        $this->iniPath = $this->iniDir.$iniFileName;
    }

    /**
     * Command line invocation to run PHPDoctor, independent of OS and include dir settings.
     * Call setIniFile first.
     *
     * @return string 	PHPDoctor messages
     */
    function runPhpDoctor () {
        if (!file_exists($this->iniPath)) exit("\n\nini file for test not found or undefined\n\n");

        $errorReporting = (string)(error_reporting() & ~2048); // Make sure E_STRICT is disabled

        ob_start();
        passthru(PHP . " -d error_reporting=$errorReporting \"{$this->appDir}phpdoc.php\" \"{$this->iniPath}\"");
        return ob_get_clean();
    }

    function readOutputFile($filename) {
        if (!file_exists('cases/output/'.$filename)) { return null; }
        return file_get_contents('cases/output/'.$filename);
    }

    /**
     * Reports an error if the $string does not contain $expected.
     *
     * Can be set to ignore insignificant whitespace in HTML output. Any whitespace in $expected is then
     * allowed to be expanded in $string.
     *
     * If you want to capture whitespace in $string which may also be completely absent, use a pipe in
     * $expected (e.g. '<td>|<tr>A cell</tr>|</td>'). If $expected happens to contain a literal pipe,
     * escape it with another pipe ('||').
     *
     * @param string $expected                     the needle
     * @param string $string                       the haystack
     * @param bool   $ignoreInsigificantWhitespace allows for additional space, tab and newline chars
     *
     * @return bool
     */
    function assertStringContains( $expected, $string, $ignoreInsigificantWhitespace = false ) {
        $contained = $this->inStr($string, $expected, $ignoreInsigificantWhitespace);
        return $this->assertTrue($contained);
    }

    /**
     * Reports an error if the $string contains $expected. Inverse of assertStringContains().
     *
     * @param string $expected                     the needle
     * @param string $string                       the haystack
     * @param bool   $ignoreInsigificantWhitespace allows for additional space, tab and newline chars
     *
     * @return bool
     */
    function assertStringDoesNotContain( $expected, $string, $ignoreInsigificantWhitespace = false ) {
        $contained = $this->inStr($string, $expected, $ignoreInsigificantWhitespace);
        return $this->assertFalse($contained);
    }

    /**
     * Returns if $haystack contains $needle.
     *
     * Can be set to ignore insignificant whitespace in HTML output. Any whitespace in $expected is then
     * allowed to be expanded in $string.
     *
     * If you want to capture whitespace in $string which may also be completely absent, use a pipe in
     * $expected (e.g. '<td>|<tr>A cell</tr>|</td>'). If $expected happens to contain a literal pipe,
     * escape it with another pipe ('||').
     *
     * (Quick and dirty solution for pipe escaping. Will get off track if the needle contains a chr(1) -
     * which is rather unlikely.)
     *
     * @param string $haystack                     the haystack
     * @param string $needle                       the needle (oh, really?!)
     * @param bool $ignoreInsigificantWhitespace   allows for additional space, tab and newline chars
     *
     * @return bool
     */
    function inStr( $haystack, $needle, $ignoreInsigificantWhitespace = false ) {
        if ($ignoreInsigificantWhitespace) {
            $needle = preg_quote($needle, '/');

            $needle = str_replace('\|\|', chr(1), $needle);
            $needle = preg_replace('%\\\\\|\s+%', ' ', $needle);
            $needle = preg_replace('%\s+\\\\\|%', ' ', $needle);
            $needle = str_replace('\|', '\s*', $needle);
            $needle = str_replace(chr(1), '\|', $needle);

            $needle = preg_replace('/\s+/', '\\s+', $needle);

            $contained = (bool) preg_match("/$needle/", $haystack);
        } else {
            $contained = (strpos($haystack, $needle)!==false);
        }

        return $contained;
    }

    /**
     * Reports an error if the $string does not contain $expected. $expected is a regular expression including
     * delimiters and any modifiers.
     *
     * @param string $expected                     the needle, as a regular expression
     * @param string $string                       the haystack
     *
     * @return bool
     */
    function assertStringContainsRx( $expectedRx, $string ) {
        $contained = $this->inStrRx($string, $expectedRx);
        return $this->assertTrue($contained);
    }

    /**
     * Reports an error if the $string contains $expected. Inverse of assertStringContainsRx().
     *
     * @param string $expected                     the needle, as a regular expression
     * @param string $string                       the haystack
     *
     * @return bool
     */
    function assertStringDoesNotContainRx( $expectedRx, $string ) {
        $contained = $this->inStrRx($string, $expectedRx);
        return $this->assertFalse($contained);
    }

    /**
     * Returns if $haystack contains $needle. $needle is a regular expression including delimiters and modifiers.
     *
     * @param string $haystack                     the haystack
     * @param string $needle                       the needle, as a regular expression.
     *
     * @return bool
     */
    function inStrRx( $haystack, $needle ) {
        $contained = (bool) preg_match($needle, $haystack);
        return $contained;
    }

    function clearTempDir() {
        $this->removeDir($this->tempDir);
        @mkdir($this->tempDir, 0777, true);
    }

    function clearOutputDir() {
        $this->removeDir($this->outputDir);
    }

    function removeDir($dir) {
        if(file_exists($dir)){
            foreach ( new DirectoryIterator($dir) as $file ) {
                if ( $file->isDir() ) {
                    if ( !$file->isDot() ) {
                        $this->removeDir($file->getPathname());
                    }
                } else {
                    @unlink($file->getPathname());
                }
            }

            @rmdir($dir);
        }
    }

}
