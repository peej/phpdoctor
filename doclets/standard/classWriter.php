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

// $Id: classWriter.php,v 1.11 2005/05/14 20:49:03 peejeh Exp $

/** This generates the HTML API documentation for each individual interface
 * and class.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $Revision: 1.11 $
 */
class ClassWriter extends HTMLWriter
{

	/** Build the class definitons.
	 *
	 * @param Doclet doclet
	 */
	function classWriter(&$doclet)
    {
	
		parent::HTMLWriter($doclet);
		
		$this->_id = 'definition';

		$rootDoc =& $this->_doclet->rootDoc();
		$phpdoctor =& $this->_doclet->phpdoctor();
		
		$packages =& $rootDoc->packages();
        asort($packages);

		foreach ($packages as $packageName => $package) {

			$this->_sections[0] = array('title' => 'Overview', 'url' => 'overview-summary.html');
			$this->_sections[1] = array('title' => 'Package', 'url' => $package->asPath().'/package-summary.html');
			$this->_sections[2] = array('title' => 'Class', 'selected' => TRUE);
			//$this->_sections[3] = array('title' => 'Use');
			if ($phpdoctor->getOption('tree')) $this->_sections[4] = array('title' => 'Tree', 'url' => $package->asPath().'/package-tree.html');
			//$this->_sections[5] = array('title' => 'Deprecated', 'url' => 'deprecated-list.html');
			$this->_sections[6] = array('title' => 'Index', 'url' => 'index-all.html');
		
			$this->_depth = $package->depth() + 1;

			$classes =& $package->ordinaryClasses();
			
			if ($classes) {
                asort($classes);
				foreach ($classes as $name => $class) {
				
					ob_start();
					
					echo "<hr>\n\n";
					
					echo '<h3>', $class->qualifiedName(), "</h3>\n\n";
					
					echo '<h1>Class ', $class->name(), "</h1>\n\n";

					echo '<pre>';
					$result =& $this->_buildTree($rootDoc, $classes[$name]);
					echo $result[0];
					echo "</pre>\n\n";
				
					$implements =& $class->interfaces();
					if (count($implements) > 0) {
						echo "<dl>\n";
						echo "<dt>All Implemented Interfaces:</dt>\n";
						echo '<dd>';
						foreach ($implements as $interface) {
							echo $interface->name(), ' ';
						}
						echo "</dt>\n";
						echo "</dl>\n\n";
					}
					
					echo "<hr>\n\n";

					echo '<p>', $class->modifiers(), ' class <strong>', $class->name(), '</strong>';
					if ($class->superclass()) {
						$superclass =& $rootDoc->classNamed($class->superclass());
						if ($superclass) {
							echo '<br>extends <a href="', str_repeat('../', $this->_depth), $superclass->asPath(), '">', $superclass->name(), "</a></p>\n\n";
						} else {
							echo '<br>extends ', $class->superclass(), "</p>\n\n";
						}
					}
					echo "</p>\n\n";
					
					$textTag =& $class->tags('@text');
					if ($textTag) {
						echo '<div class="comment" id="overview_description">', $this->_processInlineTags($textTag), "</div>\n\n";
					}

					$this->_processTags($class->tags());

					echo "<hr>\n\n";

					$fields =& $class->fields();
                    asort($fields);
					$constructors =& $class->constructor();
                    asort($constructors);
					$methods =& $class->methods();
                    asort($methods);

					if ($fields) {
						echo '<a name="summary_field"></a>', "\n";
						echo '<table class="title">', "\n";
						echo '<tr><th colspan="2" class="title">Field Summary</th></tr>', "\n";
						foreach ($fields as $field) {
							$textTag =& $field->tags('@text');
							$type =& $field->type();
							echo "<tr>\n";
							echo '<td class="type">', $field->modifiers(FALSE), ' ', $field->typeAsString(), "</td>\n";
							echo '<td class="description">';
							echo '<p class="name"><a href="#', $field->name(), '">';
							if (!$field->constantValue()) echo '$';
							echo $field->name(), '</a></p>';
							if ($textTag) {
								echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
							}
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n\n";
					}
					
					if ($class->superclass()) {
                        $superclass =& $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $this->inheritFields($superclass, $rootDoc, $package);
                        }
					}

					if ($constructors) {
						echo '<a name="summary_constr"></a>', "\n";
						echo '<table class="title">', "\n";
						echo '<tr><th colspan="2" class="title">Constructor Summary</th></tr>', "\n";
						foreach ($constructors as $constructor) {
							$textTag =& $constructor->tags('@text');
							echo "<tr>\n";
							echo '<td class="description">';
							echo '<p class="name"><a href="#', $constructor->name(), '">', $constructor->name(), '</a>', $constructor->flatSignature(), '</p>';
							if ($textTag) {
								echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
							}
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n\n";
					}

					if ($methods) {
						echo '<a name="summary_method"></a>', "\n";
						echo '<table class="title">', "\n";
						echo '<tr><th colspan="2" class="title">Method Summary</th></tr>', "\n";
						foreach($methods as $method) {
							$textTag =& $method->tags('@text');
							echo "<tr>\n";
							echo '<td class="type">', $method->modifiers(FALSE), ' ', $method->returnTypeAsString(), "</td>\n";
							echo '<td class="description">';
							echo '<p class="name"><a href="#', $method->name(), '">', $method->name(), '</a>', $method->flatSignature(), '</p>';
							if ($textTag) {
								echo '<p class="description">', strip_tags($this->_processInlineTags($textTag, TRUE), '<a><b><strong><u><em>'), '</p>';
							}
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n\n";
					}
					
					if ($class->superclass()) {
                        $superclass =& $rootDoc->classNamed($class->superclass());
                        if ($superclass) {
                            $this->inheritMethods($superclass, $rootDoc, $package);
                        }
					}

					if ($fields) {
						echo '<a name="detail_field"></a>', "\n";
						echo '<table class="detail">', "\n";
						echo '<tr><th colspan="2" class="title">Field Detail</th></tr>', "\n";
						echo "</table>\n";
						foreach($fields as $field) {
							$textTag =& $field->tags('@text');
							$type =& $field->type();
							echo '<a name="', $field->name(),'"></a>', "\n";
							echo '<h2>', $field->name(), "</h2>\n";
							echo '<code>', $field->modifiers(), ' ', $field->typeAsString(), ' <strong>';
							if (!$field->constantValue()) echo '$';
							echo $field->name(), '</strong>';
							if ($field->value()) echo ' = ', $field->value();
							echo "</code>\n";
							if ($textTag) {
								echo '<div class="details">', "\n";
								echo $this->_processInlineTags($textTag);
								echo "</div>\n\n";
							}
							$this->_processTags($field->tags());
							echo "<hr>\n\n";
						}
					}

					if ($constructors) {
						echo '<a name="detail_constr"></a>', "\n";
						echo '<table class="detail">', "\n";
						echo '<tr><th colspan="2" class="title">Constructor Detail</th></tr>', "\n";
						echo "</table>\n";
						foreach($constructors as $constructor) {
							$textTag =& $constructor->tags('@text');
							echo '<a name="', $constructor->name(),'"></a>', "\n";
							echo '<h2>', $constructor->name(), "</h2>\n";
							echo '<code>public <strong>';
							echo $constructor->name(), '</strong>', $constructor->flatSignature();
							echo "</code>\n";
							if ($textTag) {
								echo '<div class="details">', "\n";
								echo $this->_processInlineTags($textTag);
								echo "</div>\n\n";
							}
							$this->_processTags($constructor->tags());
							echo "<hr>\n\n";
						}
					}

					if ($methods) {
						echo '<a name="detail_method"></a>', "\n";
						echo '<table class="detail">', "\n";
						echo '<tr><th colspan="2" class="title">Method Detail</th></tr>', "\n";
						echo "</table>\n";
						foreach($methods as $method) {
							$textTag =& $method->tags('@text');
							echo '<a name="', $method->name(),'"></a>', "\n";
							echo '<h2>', $method->name(), "</h2>\n";
							echo '<code>', $method->modifiers(), ' ', $method->returnTypeAsString(), ' <strong>';
							echo $method->name(), '</strong>', $method->flatSignature();
							echo "</code>\n";
							if ($textTag) {
								echo '<div class="details">', "\n";
								echo $this->_processInlineTags($textTag);
								echo "</div>\n\n";
							}
							$this->_processTags($method->tags());
							echo "<hr>\n\n";
						}
					}

					$this->_output = ob_get_contents();
					ob_end_clean();
			
					$this->_write($package->asPath().'/'.strtolower($class->name()).'.html', $class->name(), TRUE);
				}
			}
		}
    }

	/** Build the class hierarchy tree which is placed at the top of the page.
	 *
	 * @param RootDoc rootDoc The root doc
	 * @param ClassDoc class Class to generate tree for
	 * @param int depth Depth of recursion
	 * @return mixed[]
	 */
	function _buildTree(&$rootDoc, &$class, $depth = NULL)
    {
		if ($depth === NULL) {
			$start = TRUE;
			$depth = 0;
		} else {
			$start = FALSE;
		}
		$output = '';
		if ($class->superclass()) {
			$superclass =& $rootDoc->classNamed($class->superclass());
			if ($superclass) {
				$result = $this->_buildTree($rootDoc, $superclass, $depth);
				$output .= $result[0];
				$depth = ++$result[1];
			} else {
				$output .= $class->superclass().'<br>';
				$output .= str_repeat('     ', $depth).'  |<br>';
				$output .= str_repeat('     ', $depth).'  +--';
			}
		}
		if ($depth > 0) {
			$output .= str_repeat('     ', $depth - 1).'  |<br>';
			$output .= str_repeat('     ', $depth - 1).'  +--';
		}
		if ($start) {
			$output .= '<strong>'.$class->name().'</strong><br />';			
		} else {
			$output .= '<a href="'.str_repeat('../', $this->_depth).$class->asPath().'">'.$class->name().'</a><br>';
		}
		return array($output, $depth);
	}
	
	/** Display the inherited fields of an element. This method calls itself
	 * recursively if the element has a parent class.
	 *
	 * @param ProgramElementDoc element
	 * @param RootDoc rootDoc
	 * @param PackageDoc package
	 */
	function inheritFields(&$element, &$rootDoc, &$package)
    {
		$fields =& $element->fields();
		if ($fields) {
            asort($fields);
			$num = count($fields); $foo = 0;
			echo '<table class="inherit">', "\n";
			echo '<tr><th colspan="2" class="inherit">Fields inherited from ', $element->qualifiedName(), "</th></tr>\n";
			echo '<tr><td>';
			foreach($fields as $field) {
				echo '<a href="', str_repeat('../', $this->_depth), $field->asPath(), '">', $field->name(), '</a>';
				if (++$foo < $num) {
					echo ', ';
				}
			}
			echo '</td></tr>';
			echo "</table>\n\n";
			if ($element->superclass()) {
                $superclass =& $rootDoc->classNamed($element->superclass());
                if ($superclass) {
                    $this->inheritFields($superclass, $rootDoc, $package);
                }
			}
		}
	}
	
	/** Display the inherited methods of an element. This method calls itself
	 * recursively if the element has a parent class.
	 *
	 * @param ProgramElementDoc element
	 * @param RootDoc rootDoc
	 * @param PackageDoc package
	 */
	function inheritMethods(&$element, &$rootDoc, &$package)
    {
		$methods =& $element->methods();
		if ($methods) {
            asort($methods);
			$num = count($methods); $foo = 0;
			echo '<table class="inherit">', "\n";
			echo '<tr><th colspan="2" class="inherit">Methods inherited from ', $element->qualifiedName(), "</th></tr>\n";
			echo '<tr><td>';
			foreach($methods as $method) {
				echo '<a href="', str_repeat('../', $this->_depth), $method->asPath(), '">', $method->name(), '</a>';
				if (++$foo < $num) {
					echo ', ';
				}
			}
			echo '</td></tr>';
			echo "</table>\n\n";
			if ($element->superclass()) {
                $superclass =& $rootDoc->classNamed($element->superclass());
                if ($superclass) {
                    $this->inheritMethods($superclass, $rootDoc, $package);
                }
			}
		}
	}

}

?>
