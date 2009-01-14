<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * This script extends class template for operation with DAM and TYPO3 4.2.0-4.2.3.
 * This script becomes deprecated and is not used when operating with TYPO3 4.2.4 (see file ext_localconf.php)
 *
 * @author	Michael Stucki <michael@typo3.org>
 *
 * $Id: class.ux_template.php $  *
 */

require_once(PATH_typo3.'template.php');

class ux_template extends template {
	/**
	 * Function to load a HTML template file with markers.
	 *
	 * @param	string		tmpl name, usually in the typo3/template/ directory
	 * @return	string		HTML of template
	 */
	function getHtmlTemplate($filename) {
		if ($GLOBALS['TBE_STYLES']['htmlTemplates'][$filename]) {
			$filename = $GLOBALS['TBE_STYLES']['htmlTemplates'][$filename];
		}
		return ($filename ? t3lib_div::getURL(t3lib_div::resolveBackPath($this->backPath . $filename)) : '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_template.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_template.php']);
}

?>