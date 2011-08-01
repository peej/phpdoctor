<?php
/*
PHPDoctor: The PHP Documentation Creator
Copyright (C) 2004 Paul James <paul@peej.co.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/** The debugging doclet. This doclet outputs all the parsed data in a format
 * suitable for debugging PHPDoctor.
 *
 * @package PHPDoctor\Doclets\Debug
 */
class Debug extends Doclet
{

	/** The depth of processing through the element hierarchy.
	 *
	 * @var int
	 */
	var $depth = 0;

	/** Doclet constructor.
	 *
	 * @param RootDoc rootDoc
	 * @param TextFormatter formatter
	 */
	function debug(&$rootDoc, $formatter)
    {
        
		$this->formatter = $formatter;
		
		foreach ($rootDoc->packages() as $package) {
			echo '- Namespace ', $package->name(), "\n";
			$this->fieldDoc($package->globals());
			$this->methodDoc($package->functions());
			$this->classDoc($package->allClasses());

		}
		
		echo "Done\n";
	
	}
    
    /** Return the depth indicator string
     *
     * @return str
     */
    function showDepth()
    {
        return str_repeat('|', $this->depth);
    }

	/** Output fieldDoc
	 *
	 * @param FieldDoc[] fields
	 */
	function fieldDoc(&$fields, $showAccess = FALSE)
    {
		$this->depth++;
		if ($fields) {
			foreach ($fields as $field) {
				$type = $field->type();
				echo $this->showDepth(), '- ', $field->modifiers($showAccess), $type->toString(), ' ';
				if ($field->isFinal()) {
					echo $field->packageName(), '\\', $field->name();
				} else {
					echo $field->packageName(), '\\$', $field->name();
				}
				if ($field->value()) {
					echo ' = ', $field->value();
				}
				echo ' [', $field->location(), ']';
				echo "\n";
				$this->docComment($field);
			}
		}
		$this->depth--;
	}
	
	/** Output methodDoc
	 *
	 * @param MethodDoc[] methods
	 */
	function methodDoc(&$methods)
    {
		$this->depth++;
		if ($methods) {
			foreach ($methods as $method) {
				$type = $method->returnType();
				echo $this->showDepth(), '- ', $method->modifiers();
				if ($type) {
					echo $type->toString(), ' ';
				} else {
					echo 'void ';
				}
				echo $method->packageName(), '\\', $method->name(), $method->flatSignature();
				echo ' [', $method->location(), ']';
				echo "\n";
				$this->fieldDoc($method->parameters());
				$this->methodDoc($method->functions());
				$exceptions =& $method->thrownExceptions();
				if ($exceptions) {
					foreach ($exceptions as $exception) {
						echo $this->showDepth(), '|- throws ';
						if (is_object($exception)) {
							echo $exception->packageName(), '\\', $exception->name(), "\n";
						} else {
							echo $exception, "\n";
						}
					}
				}
				$this->docComment($method);
			}
		}
		$this->depth--;
	}
	
	/** Output constructorDoc
	 *
	 * @param ConstructorDoc constructor
	 */
	function constructorDoc(&$constructor)
    {
		$this->depth++;
		if ($constructor) {
            echo $this->showDepth(), '- ', $constructor->modifiers();
            echo $constructor->packageName(), '\\', $constructor->name(), $constructor->flatSignature();
            echo ' [', $constructor->location(), ']';
            echo "\n";
            $this->fieldDoc($constructor->parameters());
		}
		$this->depth--;
	}

	/** Output classDoc
	 *
	 * @param ClassDoc[] classes
	 */
	function classDoc(&$classes)
    {
		$this->depth++;
		if ($classes) {
			foreach ($classes as $class) {
				echo $this->showDepth(), '- ', $class->modifiers();
				if ($class->isInterface()) {
					echo 'interface ';
				} else {
					echo 'class ';
				}
				echo $class->packageName(), '\\', $class->name();
				if ($class->superclass()) {
				    if (isset($classes[$class->superclass()])) {
				        $superclass = $classes[$class->superclass()];
				        echo ' extends ', $superclass->packageName(), '\\', $superclass->name();
				    } else {
				        echo ' extends ', $class->superclass();
				    }
				}
				$interfaces =& $class->interfaces();
				if ($interfaces) {
					echo ' implements ';
					foreach($interfaces as $interface) {
						echo $interface->packageName(), '\\', $interface->name(), ' ';
					}
				}
				echo ' [', $class->location(), ']';
				echo "\n";
				$this->docComment($class);
				$this->fieldDoc($class->constants(), TRUE);
				$this->fieldDoc($class->fields(), TRUE);
				$this->methodDoc($class->methods());
			}
		}
		$this->depth--;
	}
	
	function docComment(&$programElement) {
	    $textTag =& $programElement->tags('@text');
        if ($textTag && $textTag->text($this)) {
            echo $this->showDepth(), '|= ', $textTag->text($this), "\n";
            foreach($textTag->inlineTags($this) as $inlineTag) {
                if ($inlineTag->name() != '@text') {
                    echo $this->showDepth(), '|= ', $inlineTag->displayName(), ': ', $inlineTag->text($this), "\n";
                }
            }
        }
	}
	
}

?>
