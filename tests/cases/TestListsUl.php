<?php

/**
 * @package PHPDoctor\Tests
 */
class TestListsUl extends DoctorTestCase
{
    var $output;

    function TestListsUl() {
        $this->__construct();
    }

    function __construct() {
        parent::__construct('List (ul) tests');

        $this->clearOutputDir();

        $this->setIniFile('lists-ul.ini');
        $this->runPhpDoctor();

        $this->output = $this->readOutputFile('phpdoctor/tests/listsul.html');
    }

    function testListConversion() {
        // This is actually not the way it should be done, but I'm a bit short of time. So here's just a check that
        // the testcase doc as a whole comes out as expected, instead of testing one issue at a time.

        $expected = <<<EXPECTED
<p>class ListsUl.</p>|<p>Process a *doc comment* with unordered lists.</p>

<ul>
<li>They could use 'o'.</li>
<li>Or they could use '-'</li>
<li>Or +, or #.</li>
</ul>

<p>They can start directly after a paragraph, without a blank line in between:</p>

<ul>
<li>Like this. This is a list item.</li>
<li>The second and last list item. Again, we don't need a blank line afterwards.</li>
</ul>

<p>This line is not part of the list anymore.</p>|<p>We also accept multi-line list items:</p>

<ul>
<li>This is the first list item, and it goes on and on and
on into a second line.</li>
<li>This one doesn't.</li>
<li>This is another long one. It, too, spills over into
a second line because of its wordiness, and it continues
even further into a third.</li>
</ul>

<p>Lines containing just a bullet point character are not considered part of any list:
-</p>

<ul>
<li>But here comes the first real bullet point.</li>
<li>Another bullet point.</li>
<li>The list goes on even if there is a blank line in between. Here it is.</li>
<li>And we have passed it. It makes sense to capture this because people sometimes spread the
list items a little. Especially if they have more text in a second line and even more in
a third.</li>
<li><p class="list">There might be list items which are rather large and in fact consist of several paragraphs.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam blandit, ipsum id porta
sollicitudin.</p><p class="list">Dolor dui mattis libero, eget elementum quam tortor a tortor. Fusce turpis lectus, varius
ut dictum id, mollis eu elit. Morbi magna orci, tincidunt sit amet sagittis eget, rutrum
eget orci. Vestibulum tempus feugiat dui nec.</p><p class="list">In nibh velit, luctus id pulvinar eget, tincidunt et sapien. Curabitur sagittis mollis purus,
nec commodo augue hendrerit quis. Cum sociis natoque penatibus et magnis dis parturient montes!</p></li>
<li>And an item #6. Enough.</li>
</ul>

<p>And more text, not part of the preceding list.</p>|<p>Lists don't have to be aligned stricty because any accidental hit on the space bar would throw
detection off.</p>

<ul>
<li>This is the first list item.</li>
<li>This is the second.</li>
<li>And a third.</li>
</ul>

<p>The same is true for whitespace behind the bullet point.</p>

<ul>
<li>This is the first list item.</li>
<li>This is the second.</li>
<li>And a third.</li>
</ul>

<p>Finally, a single line with a bullet point could be anything and won't be treated as
a list. Because it takes at least two ...</p>|<p>- ... to tango. This is not a list.</p>|<p>That's it for unordered lists.</p>|</div>

<dl>
<dt>Author:</dt>
<dd>I don't care who did it, but we want to have a tag here.</dd>
</dl>
EXPECTED;

        $this->assertStringContains($expected, $this->output, true);
    }

}
