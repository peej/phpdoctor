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

/** This generates the index.html file used for presenting the frame-formated
 * "cover page" of the API documentation.
 *
 * @package PHPDoctor.Doclets.Standard
 * @version $id$
 */
class frameOutputWriter extends htmlWriter {

	/** Build the HTML frameset.
	 *
	 * @param doclet doclet
	 */
	function frameOutputWriter(&$doclet) {
	
		parent::htmlWriter($doclet);

		ob_start();
		echo <<<END
		
<frameset cols="20%,80%">

<frameset rows="30%,70%">

<frame src="overview-frame.html" name="packagelist" />
<frame src="allitems-frame.html" name="index" />

</frameset>

<frame src="overview-summary.html" name="main" />

<noframes>
<h2>Frame Alert</h2>
<p>This document is designed to be viewed using frames. If you see this message, you are using a non-frame-capable browser.<br />
Link to <a href="overview-summary.html">Non-frame version</a>.</p>
</noframes>

</frameset>
END;

		$this->_output = ob_get_contents();
		ob_end_clean();
		
		$this->_write('index.html', FALSE, FALSE);
	
	}

}

?>