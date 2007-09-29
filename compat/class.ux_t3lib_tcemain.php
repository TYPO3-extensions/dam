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
 * Contains the TYPO3 Core Engine
 *
 * $Id: class.ux_t3lib_tcemain.php,v 1.1 2005/04/19 08:51:25 cvsrene Exp $
 * Revised for TYPO3 3.6 August/2003 by Kasper Skaarhoj
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 */











/**
 * This is the TYPO3 Core Engine class for manipulation of the database
 * This class is used by eg. the tce_db.php script which provides an the interface for POST forms to this class.
 *
 * Dependencies:
 * - $GLOBALS['TCA'] must exist
 * - $GLOBALS['LANG'] (languageobject) may be preferred, but not fatal.
 *
 * Note: Seems like many instances of array_merge() in this class are candidates for t3lib_div::array_merge() if integer-keys will some day make trouble...
 *
 * tce_db.php for further comments and SYNTAX! Also see document 'Inside TYPO3' for details.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
class ux_t3lib_TCEmain extends t3lib_TCEmain	{

	/**
	 * Evaluates 'group' or 'select' type values.
	 *
	 * @param	array		The result array. The processed value (if any!) is set in the 'value' key.
	 * @param	string		The value to set.
	 * @param	array		Field configuration from TCA
	 * @param	array		Additional parameters in a numeric array: $table,$id,$curValue,$status,$realPid,$recFID
	 * @param	[type]		$uploadedFiles: ...
	 * @param	string		Field name
	 * @return	array		Modified $res array
	 */
	function checkValue_group_select($res,$value,$tcaFieldConf,$PP,$uploadedFiles,$field)	{

		list($table,$id,$curValue,$status,$realPid,$recFID) = $PP;

			// the table name is needed for foreign select
		$tcaFieldConf['MM_foreign_select'] = $tcaFieldConf['MM_foreign_select'] ? $table : false;

		return parent::checkValue_group_select($res,$value,$tcaFieldConf,$PP,$uploadedFiles,$field);


//				case 'db':
//						// the table name is needed for foreign select
//					$tcaFieldConf['MM_foreign_select'] = $tcaFieldConf['MM_foreign_select'] ? $table : false;
//					$valueArray = $this->checkValue_group_select_processDBdata($valueArray,$tcaFieldConf,$id,$status,'group');
//				break;

	}


	/**
	 * Returns data for group/db and select fields
	 *
	 * @param	array		Current value array
	 * @param	array		TCA field config
	 * @param	integer		Record id, used for look-up of MM relations (local_uid)
	 * @param	string		Status string ('update' or 'new')
	 * @param	string		The type, either 'select' or 'group'
	 * @return	array		Modified value array
	 */
	function checkValue_group_select_processDBdata($valueArray,$tcaFieldConf,$id,$status,$type)	{
		$tables = $type=='group'?$tcaFieldConf['allowed']:$tcaFieldConf['foreign_table'].','.$tcaFieldConf['neg_foreign_table'];
		$prep = $type=='group'?$tcaFieldConf['prepend_tname']:$tcaFieldConf['neg_foreign_table'];

		$dbAnalysis = t3lib_div::makeInstance('t3lib_loadDBGroup');
		$dbAnalysis->registerNonTableValues = $tcaFieldConf['allowNonIdValues'] ? 1 : 0;
		$dbAnalysis->start(implode(',',$valueArray),$tables);

		if ($tcaFieldConf['MM'])	{

			if ($tcaFieldConf['prepend_tname']) {
				if ($tcaFieldConf['MM_foreign_select']) {
					foreach ($dbAnalysis->itemArray as $key => $val) {
						$dbAnalysis->itemArray[$key]['table'] = $tcaFieldConf['MM_foreign_select']; // is the current table
					}
				}
			}

			if ($status=='update')	{
				$dbAnalysis->writeMM($tcaFieldConf['MM'], $id, $prep, $tcaFieldConf['MM_ident'], $tcaFieldConf['MM_foreign_select']);
			} else {
				$this->dbAnalysisStore[] = array($dbAnalysis,$tcaFieldConf['MM'],$id,$prep);	// This will be traversed later to execute the actions
			}
			$cc=count($dbAnalysis->itemArray);
			$valueArray = array($cc);

		} else {
			$valueArray = $dbAnalysis->getValueArray($prep);
			if ($type=='select' && $prep)	{
				$valueArray = $dbAnalysis->convertPosNeg($valueArray,$tcaFieldConf['foreign_table'],$tcaFieldConf['neg_foreign_table']);
			}
		}

			// Here we should se if 1) the records exist anymore, 2) which are new and check if the BE_USER has read-access to the new ones.
		return $valueArray;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_tcemain.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_tcemain.php']);
}
?>
