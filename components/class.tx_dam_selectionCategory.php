<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Contains standard selection trees/rules.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   69: class tx_dam_selectionCategory extends tx_dam_selBrowseTree
 *   87:     function tx_dam_selectionCategory()
 *
 *              SECTION: DAM specific functions
 *  137:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *  166:     function getControl($title,$row)
 *
 *              SECTION: categories
 *  213:     function uniqueList()
 *  245:     function getSubRecords ($uidList, $level=1, $fields='*', $table='tx_dam_cat', $where='')
 *  277:     function getSubRecordsIdList($uidList, $level=1, $table='tx_dam_cat', $where='')
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');





/**
 * category tree class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionCategory extends tx_dam_selBrowseTree {

	/**
	 * Defines if a browsetree for TCEForms can be rendered
	 */
	var $isTCEFormsSelectClass = true;

	/**
	 * If mounts are supported (be_users)
	 */
	var $supportMounts = true;


	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionCategory()	{
		global $LANG, $BACK_PATH;

		$this->title = $LANG->sL('LLL:EXT:dam/lib/locallang.xml:categories');
		$this->treeName = 'txdamCat';
		$this->domIdPrefix = $this->treeName;

		$this->table = 'tx_dam_cat';
		$this->parentField = $GLOBALS['TCA'][$this->table]['ctrl']['treeParentField'];
		$this->typeField = $GLOBALS['TCA'][$this->table]['ctrl']['type'];

		$this->iconName = 'cat.gif';
		$this->iconPath = PATH_txdam_rel.'i/';
		$this->rootIcon = PATH_txdam_rel.'i/catfolder.gif';

		$this->fieldArray = array('uid','title');
		if($this->parentField) $this->fieldArray[] = $this->parentField;
		if($this->typeField) $this->fieldArray[] = $this->typeField;
		$this->defaultList = 'uid,pid,tstamp,sorting';

		if(TYPO3_MODE=='FE') {
			$this->clause = $GLOBALS['TSFE']->sys_page->enableFields($this->table);
		} else {
			$this->clause = ' AND deleted=0';
		}
		$this->orderByFields = 'sorting,title';

		$conf = tx_dam::config_getValue('setup.selections.'.$this->treeName);
		$this->TSconfig = $conf['properties'];
	}




	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/

	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Query type: AND, OR, ...
	 * @param	string		Operator, eg. '!=' - see DAM Documentation
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {
		static $alias='a';

		$depth = isset($this->TSconfig['sublevelDepth']) ? intval($this->TSconfig['sublevelDepth']) : 99;

		$catUidList = $this->uniqueList(intval($id), $this->getSubRecordsIdList(intval($id), $depth, 'tx_dam_cat'));

		if ($queryType=='NOT')	{
			$query= 'tx_dam_mm_cat_'.$alias.'.uid_foreign NOT IN ('.$catUidList.')';
		} else {
			$query= 'tx_dam_mm_cat_'.$alias.'.uid_foreign IN ('.$catUidList.')';
		}

		$damObj->qg->addMMJoin('tx_dam_mm_cat', 'tx_dam', 'tx_dam_mm_cat_'.$alias);

		$alias = chr(ord($alias)+1);

		return array($queryType,$query);
	}



	/**
	 * Return a control (eg. selection icons) for the element
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function getControl($title,$row)	{
		global $BACK_PATH;

		$control = '';

		if ($this->modeSelIcons
			AND !($this->mode=='tceformsSelect')
			AND ($row['uid'] OR ($row['uid'] == '0' AND $this->linkRootCat))) {

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/plus.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
			$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals.gif" width="8" height="11" border="0" alt="" />';
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/equals.gif', 'width="8" height="11"').' alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

// TODO minus do not work - maybe with subqueries
//			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
//			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/minus.gif', 'width="8" height="11"').' alt="" />';
//			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
//			$control .= '<img src="'.$BACK_PATH.'clear.gif" width="12" height="11" border="0" alt="" />';
		}
		return $control;
	}






	/***************************************
	 *
	 *	 categories
	 *
	 ***************************************/



	/**
	 * Takes comma-separated lists and arrays and removes all duplicates.
	 *
	 * @param	string		Accept multiple parameters wich can be comma-separated lists of values and arrays.
	 * @return	string		Returns the list without any duplicates of values, space around values are trimmed
	 */
	function uniqueList()	{
		$listArray = array();

		$arg_list = func_get_args();
		foreach ($arg_list as $in_list)	{

			if (!is_array($in_list) AND empty($in_list))	{
				continue;
			}

			if (!is_array($in_list))	{
				$in_list = t3lib_div::trimExplode(',',$in_list,true);
			}
			if(count($in_list)) {
				$listArray = array_merge($listArray,$in_list);
			}
		}

		return implode(',',t3lib_div::uniqueArray($listArray));
	}


	/**
	 * Returns an array with rows for subrecords with parent_id IN ($uidList).
	 *
	 * @param	integer		$uidList UID list of records
	 * @param	integer		$level Level depth. How deep walk into the tree. Default is 1.
	 * @param	string		$fields List of fields to select (default is '*').
	 * @param	string		$table Table name. Default 'tx_dam_cat'
	 * @param	string		$where Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function getSubRecords ($uidList, $level=1, $fields='*', $table='tx_dam_cat', $where='')	{
		$rows = array();

		while ($level && $uidList)	{
			$level--;

			$newIdList = array();
			t3lib_div::loadTCA($table);
			$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $table, $ctrl['treeParentField'].' IN ('.$uidList.') '.$where.' AND NOT '.$table.'.'.$ctrl['delete']);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$rows[$row['uid']] = $row;
				$newIdList[] = $row['uid'];
			}
			$uidList = implode(',', $newIdList);

		}


		return $rows;
	}


	/**
	 * Returns a commalist of sub record uid's with parent_id IN ($uidList).
	 *
	 * @param	integer		$uidList UID list of records
	 * @param	integer		$level Level depth. How deep walk into the tree. Default is 1.
	 * @param	string		$table Table name. Default 'tx_dam_cat'
	 * @param	string		$where Additional WHERE clause, eg. " AND blablabla=0"
	 * @return	string		Comma-list of record ids
	 */
	function getSubRecordsIdList($uidList, $level=1, $table='tx_dam_cat', $where='')	{
		$uidList = $GLOBALS['TYPO3_DB']->cleanIntList($uidList);
		$rows = $this->getSubRecords ($uidList, $level, 'uid', $table, $where);
		return implode(',',array_keys($rows));
	}

}





if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionCategory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionCategory.php']);
}
?>