<?php
    
    /**
     * class ListsUl.
     *
     * Process a *doc comment* with unordered lists.
     *
     * o They could use 'o'.
     * o Or they could use '-'
     * o Or +, or #.
     * 
     * They can start directly after a paragraph, without a blank line in between:
     * # Like this. This is a list item.
     * # The second and last list item. Again, we don't need a blank line afterwards.
     * This line is not part of the list anymore.
     *
     * We also accept multi-line list items:
     *
     * + This is the first list item, and it goes on and on and
     *   on into a second line.
     * + This one doesn't.
     * + This is another long one. It, too, spills over into
     *   a second line because of its wordiness, and it continues
     *   even further into a third.
     * 
     * Lines containing just a bullet point character are not considered part of any list:
     * -
     * - But here comes the first real bullet point.
     * - Another bullet point.
     * - The list goes on even if there is a blank line in between. Here it is.
     *
     * - And we have passed it. It makes sense to capture this because people sometimes spread the
     *   list items a little. Especially if they have more text in a second line and even more in
     *   a third.
     *
     * - There might be list items which are rather large and in fact consist of several paragraphs.
     *   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam blandit, ipsum id porta
     *   sollicitudin.
     *
     *   Dolor dui mattis libero, eget elementum quam tortor a tortor. Fusce turpis lectus, varius
     *   ut dictum id, mollis eu elit. Morbi magna orci, tincidunt sit amet sagittis eget, rutrum
     *   eget orci. Vestibulum tempus feugiat dui nec.
     *
     *   In nibh velit, luctus id pulvinar eget, tincidunt et sapien. Curabitur sagittis mollis purus,
     *   nec commodo augue hendrerit quis. Cum sociis natoque penatibus et magnis dis parturient montes!
     *   
     * - And an item #6. Enough.
     * And more text, not part of the preceding list.
     * 
     * Lists don't have to be aligned stricty because any accidental hit on the space bar would throw
     * detection off.
     *
     * 		o This is the first list item.
     *  o This is the second.
     *   o And a third.
     *
     * The same is true for whitespace behind the bullet point.
     * 
     * 	+  This is the first list item.
     *  +    This is the second.
     *  +   And a third.
     *
     * Finally, a single line with a bullet point could be anything and won't be treated as
     * a list. Because it takes at least two ...
     * 
     * - ... to tango. This is not a list.
     * 
     * That's it for unordered lists.
     * 
     * @author I don't care who did it, but we want to have a tag here.
     * @package PHPDoctor\Tests
     */
    class ListsUl {}
    
    
    
?>