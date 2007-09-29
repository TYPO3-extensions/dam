<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/




/**
 * Class for updating the db
 *
 * @author	 Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		if (!t3lib_div::GPvar('do_update'))	{
			$onClick = "document.location='".t3lib_div::linkThisScript(array('do_update'=>1))."'; return false;";

			return 'Do you want to perform the database update now?

				<form action=""><input type="submit" value="DO IT" onclick="'.htmlspecialchars($onClick).'"></form>
			';
		} else {

				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', 'module='.$GLOBALS['TYPO3_DB']->fullQuoteStr('dam', 'pages').'', array('doktype'=>'254'));


				$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tt_content');
				if (isset($res['tx_dam_flexform']) AND !isset($res['ce_flexform'])) {
					$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tt_content CHANGE tx_dam_flexform ce_flexform mediumtext NOT NULL');
				}
				
				return 'DB updated.';
		}
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 *
	 * @return	boolean
	 */
	function access()	{
		$doit = false;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)', 'pages', 'module='.$GLOBALS['TYPO3_DB']->fullQuoteStr('dam', 'pages').' AND doktype<254');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		$doit = $row[0] ? true : $doit;
		
		$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tt_content');
		if (isset($res['tx_dam_flexform']) AND !isset($res['ce_flexform'])) {
			$doit = true;
		}
		
		return $doit;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.ext_update.php']);
}


?>