<?php

/**
 * @package PHPDoctor\Tests
 */
class TestMarkdown extends DoctorTestCase
{
	
    var $output;
    
	function testMarkdown() {
        $this->DoctorTestCase('Markdown formatter tests');
		
		$this->clearOutputDir();
		
		$this->setIniFile('markdown.ini');
		$this->runPhpDoctor();
		
		$this->output = $this->readOutputFile('phpdoctor/tests/markdownformattest.html');
		#var_dump($this->output);
	}
	
	function testListConversion() {
		
		// This is actually not the way it should be done, but I'm a bit short of time. So here's just a check that
		// the testcase doc as a whole comes out as expected, instead of testing one issue at a time.
		
		$expected = <<<EXPECTED
<p>This docblock uses Markdown to provide formatting</p>

<p>This is a new paragraph</p>

<p><em>normal emphasis with asterisks</em></p>

<p><em>normal emphasis with underscore</em></p>

<p><strong>strong emphasis with asterisks</strong></p>

<p><strong>strong emphasis with underscore</strong></p>

<p>This is some text <em>emphased</em> with asterisks.</p>

<p><a href="http://peej.github.com/phpdoctor/">PHP Doctor</a></p>

<p>Header 1
 ========</p>

<p>Header 2</p>

<hr />

<p>A quote about linear algebra:</p>

<blockquote>
  <p>Consistent linear systems in real life are solved in 
  one of two ways: by direct calculation (using a matrix 
  factorization, for example) or by an iterative procedure 
  that generates a sequence of vectors that approach the 
  exact solution.</p>
</blockquote>

<ol>
<li>Snowy</li>
<li>Elf</li>
<li>Boreal</li>
</ol>

<ul>
<li>Sun</li>
<li>Moon</li>
<li><p>Earth</p></li>
<li><p>Ingredients</p>

<ul>
<li>Milk</li>
<li>Eggs</li>
</ul></li>
<li>Recipies

<ol>
<li>Pancake</li>
<li>Waffle</li>
</ol></li>
</ul>
EXPECTED;

		$this->assertStringContains($expected, $this->output, true); 
	}
	
}

?>
