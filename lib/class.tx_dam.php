<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2005 René Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *
 */


require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_div.php');
require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_db.php');


/**
 * DAM API functions
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam {

	function getMetaForFile($filename) {
		$meta = false;
#TODO heavy improvement

#TODO should that be an option?		$pfile = realpath($absFile);

		if (is_file($filename)) {
			$meta = array();

			$meta['fields']['file_name'] = basename($filename);
			$meta['fields']['file_dl_name'] = $meta['fields']['file_name'];
			$meta['fields']['file_path'] = tx_dam_div::getRelPath (dirname($filename).'/');
			$meta['fields']['file_mtime'] = filemtime($filename);
			$meta['fields']['file_ctime'] = filectime($filename);
			$meta['fields']['file_inode'] = fileinode($filename);
			$meta['fields']['file_size'] = filesize($filename);


			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam', "file_name='".$GLOBALS['TYPO3_DB']->quoteStr($meta['fields']['file_name'],'tx_dam')."' AND file_path='".$GLOBALS['TYPO3_DB']->quoteStr($meta['fields']['file_path'],'tx_dam')."' AND deleted=0");

#TODO ## look if more than one record fit and do heavier check

			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$meta['fields'] = $row;
			}
		}

		return $meta;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php']);
}
?>