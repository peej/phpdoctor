PHPDoctor: The PHP Documentation Creator
========================================

aka Peej's Quick & Dirty PHPDoc Clone
-------------------------------------

PHPDoctor is a Javadoc style comment parser for PHP, written with an emphasis on
speed and simplicity. It is designed to be as close a clone to Javadoc as possible.


Installation
------------

You can clone it from Github but you're better off using [Composer](http://getcomposer.org)
to install it within your project:

    {
        require: "peej/phpdoctor": "2.0.5"
    }

Then run Composer:

    $ curl -s https://getcomposer.org/installer | php
    $ composer.phar install

And then if everything went according to plan, run PHPDoctor with it's default
configuration:

    $ bin/phpdoc


Configuration
-------------

PHPDoctor will run with default options that will process all *.php files within
and below the current directory, unless you provide a configuration file.

Configuration is done via a PHP style ini file. If there's a file called
phpdoctor.ini in the current directory, PHPDoctor will use this, alternatively
you can pass in the name of a configuration as the first commandline option.

PHPDoctor supports a number of configuration directives:

 * files - Names of files to parse. This can be a single filename, or a
   comma separated list of filenames. Wildcards are allowed.
 * ignore - Names of files or directories to ignore. This can be a single
   filename, or a comma separated list of filenames. Wildcards are NOT
   allowed.
 * source_path - The directory to look for files in, if not used the
   PHPDoctor will look in the current directory (the directory it
   is run from).
 * subdirs - If you do not want PHPDoctor to look in each sub directory
   for files uncomment this line.
 * quiet - Quiet mode suppresses all output other than warnings and
   errors.
 * verbose - Verbose mode outputs additional messages during execution.
 * doclet - Select the doclet to use for generating output.
 * doclet_path - The directory to find the doclet in. Doclets are
   expected to be in a directory named after themselves at the
   location given.
 * taglet_path - The directory to find taglets in. Taglets allow you to
   make PHPDoctor handle new tags and to alter the behavour of
   existing tags and their output.
 * default_package - If the code you are parsing does not use package
   tags or not all elements have package tags, use this setting to
   place unbound elements into a particular package.
 * overview - Specifies the name of a HTML file containing text for the
   overview documentation to be placed on the overview page. The
   path is relative to "source_path" unless an absolute path is
   given.
 * package_comment_dir - Package comments will be looked for in a file
   named package.html in the same directory as the first source
   file parsed in that package or in the directory given below. If
   the directive below is used then package comments should be
   named "<packageName>.html".
 * globals - Parse out global variables.
 * constants - Parse out global constants.
 * private - Generate documentation for all class members.
 * protected - Generate documentation for public and protected class
   members.
 * public - Generate documentation for only public class members.
 
 The following directives are specific for the standard doclet:
 
 * d - The directory to place generated documentation in. If the given
   path is relative to it will be relative to "source_path".
 * windowtitle - Specifies the title to be placed in the HTML <title>
   tag.
 * doctitle - Specifies the title to be placed near the top of the
   overview summary file.
 * header - Specifies the header text to be placed at the top of each
   output file. The header will be placed to the right of the
   upper navigation bar.
 * footer - Specifies the footer text to be placed at the bottom of each
   output file. The footer will be placed to the right of the
   lower navigation bar.
 * bottom - Specifies the text to be placed at the bottom of each output
   file. The text will be placed at the bottom of the page, below
   the lower navigation bar.
 * tree - Create a class tree


Doc Comments
------------

A full description of the format of doc comments can be found on the
Sun Javadoc web site (http://java.sun.com/j2se/javadoc/). Doc comments
look like this:

    /**
     * This is the typical format of a simple documentation comment
     * that spans two lines.
     */


### Tags

PHPDoctor supports the following tags within a doc comment:

    @author name-text
    @deprecated deprecated-text
    {@link package.class#member label}
    {@linkplain package.class#member label}
    @param parameter-type parameter-name description
    @return return-type description
    @see packahge.class#member
    @since since-text
    @var var-type
    @version version-text

Some Javadoc tags are not relevant to PHP, others are added or slightly changed
due to PHPs loose typing.


Questions
---------

Q: Why do we need another PHPDdoc clone?

A: I wrote PHPDoctor because I back in 2004, I couldn't find a Javadoc clone for
PHP that was small and simple and worked out of the box or that worked at all.
The PHP tokenizer extension has made creating PHPDoc programs really easy since
PHP can now do the hard work for you.

Q: Why is PHPDoctor different from other PHPDoc programs?

A: PHPDoctor is very small and easy to use, sticking as closely as
possible to the way Javadoc works, including using the same program
structure and doclet approach to templating. PHPDoctor has a very small
learning curve, most people should be able to generate API
documentation in only a few minutes.

Q: Tell me more about how PHPDoctor works

A: PHPDoctor uses the PHP tokenizer extension, this means that it lets PHP do the
parsing of your source code. PHPDoctor just takes the tokens PHP parses out and
turns them into API documentation via doclet clases. This means it will work for
any valid PHP code, no exceptions, it also makes it very fast.
