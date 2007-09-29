<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Contains class for getting and transforming data for display in backend forms (TCEforms)
 *
 * $Id: class.ux_t3lib_transferdata.php,v 1.1 2005/04/19 08:51:25 cvsrene Exp $
 * Revised for TYPO3 3.6 September/2003 by Kasper Skaarhoj
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 */






/**
 * Class for getting and transforming data for display in backend forms (TCEforms)
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
class ux_t3lib_transferData extends t3lib_transferData {

	/**
	 * Processing of the data value in case the field type is "group"
	 *
	 * @param	string		The field value
	 * @param	array		TCA field config
	 * @param	array		TCEform TSconfig for the record
	 * @param	string		Table name
	 * @param	array		The row
	 * @param	string		Field name
	 * @return	string		The processed input field value ($data)
	 * @access private
	 * @see renderRecord()
	 */
	function renderRecord_groupProc($data,$fieldConfig,$TSconfig,$table,$row,$field)	{
		switch ($fieldConfig['config']['internal_type'])	{
			case 'file':
				$data = parent::renderRecord_groupProc($data,$fieldConfig,$TSconfig,$table,$row,$field);
			break;
			case 'db':
				$loadDB = t3lib_div::makeInstance('t3lib_loadDBGroup');

				$matchTablenames = '';
				if ($fieldConfig['config']['prepend_tname']) {
					if ($fieldConfig['config']['MM_foreign_select']) {
						$matchTablenames = $table;
					} else {
						$matchTablenames = $fieldConfig['config']['allowed'];
					}
				}
				$loadDB->start($data, $fieldConfig['config']['allowed'].','.$matchTablenames, $fieldConfig['config']['MM'], $row['uid'], $matchTablenames, $fieldConfig['config']['MM_ident'], $fieldConfig['config']['MM_foreign_select']);
				$loadDB->getFromDB();
				$data = $loadDB->readyForInterface();
			break;
		}

		return $data;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_transferdata.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_transferdata.php']);
}
?>
