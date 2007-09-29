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
 * Contains class for loading database groups
 *
 * $Id: class.ux_t3lib_loaddbgroup.php,v 1.1 2005/04/19 08:51:25 cvsrene Exp $
 * Revised for TYPO3 3.6 September/2003 by Kasper Skaarhoj
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   72: class t3lib_loadDBGroup
 *   99:     function start($itemlist,$tablelist, $MMtable='',$MMuid=0)
 *  140:     function readList($itemlist)
 *  186:     function readMM($tableName,$uid)
 *  215:     function writeMM($tableName,$uid,$prependTableName=0)
 *  251:     function getValueArray($prependTableName='')
 *  279:     function convertPosNeg($valueArray,$fTable,$nfTable)
 *  301:     function getFromDB()
 *  333:     function readyForInterface()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */










/**
 * Load database groups (relations)
 * Used to process the relations created by the TCA element types "group" and "select" for database records. Manages MM-relations as well.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
class ux_t3lib_loadDBGroup extends t3lib_loadDBGroup	{


	/**
	 * Initialization of the class.
	 *
	 * @param	string		List of group/select items
	 * @param	string		Comma list of tables, first table takes priority if no table is set for an entry in the list.
	 * @param	string		Name of a MM table.
	 * @param	integer		Local UID for MM lookup
	 * @param	boolean		If set, then table names will be written in MM tables.
	 * @param	string		If set, the "ident" field will be used in MM tables
	 * @param	boolean		If set, uid_local and uid_foreign field names will be swapped in MM tables
	 * @return	void
	 */
	function start($itemlist, $tablelist, $MMtable='', $MMuid=0, $MMmatchTablenames='', $MMident='', $MMswapLocalForeign=0)	{

		$tablelist = implode(',', array_unique(t3lib_div::trimExplode(',',$tablelist,1)));

			// If the table list is "*" then all tables are used in the list:
		if (!strcmp(trim($tablelist),'*'))	{
			$tablelist = implode(',',array_keys($GLOBALS['TCA']));
			$MMmatchTablenames = $MMmatchTablenames ? $tablelist : '';
		}

			// The tables are traversed and internal arrays are initialized:
		$tempTableArray = t3lib_div::trimExplode(',',$tablelist,1);
		foreach($tempTableArray as $key => $val)	{
			$tName = trim($val);
			$this->tableArray[$tName] = Array();
			if ($this->checkIfDeleted && $GLOBALS['TCA'][$tName]['ctrl']['delete'])	{
				$fieldN = $tName.'.'.$GLOBALS['TCA'][$tName]['ctrl']['delete'];
				$this->additionalWhere[$tName].=' AND NOT '.$fieldN;
			}
		}

		if (is_array($this->tableArray))	{
			reset($this->tableArray);
		} else {return 'No tables!';}

			// Set first and second tables:
		$this->firstTable = key($this->tableArray);		// Is the first table
		next($this->tableArray);
		$this->secondTable = key($this->tableArray);	// If the second table is set and the ID number is less than zero (later) then the record is regarded to come from the second table...

			// Now, populate the internal itemArray and tableArray arrays:
		if ($MMtable)	{	// If MM, then call this function to do that:
			$this->readMM($MMtable, $MMuid, $MMmatchTablenames, $MMident, $MMswapLocalForeign);
		} else {
				// If not MM, then explode the itemlist by "," and traverse the list:
			$this->readList($itemlist);
		}
	}



	/**
	 * Reads the record tablename/id into the internal arrays itemArray and tableArray from MM records.
	 * You can call this function after start if you supply no list to start()
	 *
	 * @param	string		MM Tablename
	 * @param	integer		Local UID
	 * @param	string		The "tablenames" field will be queried with this table name list
	 * @param	string		The "ident" field will be queried with this value
	 * @param	boolean		If set, uid_local and uid_foreign field names will be swapped
	 * @return	void
	 */
	function readMM($tableName, $uid, $tableList='', $ident='', $swapLocalForeign=0)	{
		$key=0;

		if ($swapLocalForeign) {
			$uid_foreign = 'uid_local';
			$uid_local = 'uid_foreign';
		} else {
			$uid_local = 'uid_local';
			$uid_foreign = 'uid_foreign';
		}

		$where = $uid_local.'='.intval($uid);
		if ($ident) {
			$where.= ' AND ident="'.$GLOBALS['TYPO3_DB']->quoteStr($ident, $tableName).'"';
		}
		if ($tableList) {
			$tableArr = t3lib_div::trimExplode(',', $tableList, 1);
			$whereArr = array();
			foreach($tableArr as $foreignTable) {
				$whereArr[] = 'tablenames="'.$GLOBALS['TYPO3_DB']->quoteStr($foreignTable, $tableName).'"';
			}
			$where.= ' AND ( '.implode(' OR ', $whereArr).' ) ';
		}

			// Select all MM relations:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $tableName, $where, '', 'sorting');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				// in foreign_select mode firstTable is the right table - tablenames should then be the current table
			$theTable = ($row['tablenames'] AND !$swapLocalForeign) ? $row['tablenames'] : $this->firstTable;		// If tablesnames columns exists and contain a name, then this value is the table, else it's the the firstTable...
			if (($row[$uid_foreign] || $theTable=='pages') && $theTable && isset($this->tableArray[$theTable]))	{
				$this->itemArray[$key]['id'] = $row[$uid_foreign];
				$this->itemArray[$key]['table'] = $theTable;
				$this->tableArray[$theTable][]= $row[$uid_foreign];
			} elseif ($this->registerNonTableValues)	{
				$this->itemArray[$key]['id'] = $row[$uid_foreign];
				$this->itemArray[$key]['table'] = '_NO_TABLE';
				$this->nonTableArray[] = $row[$uid_foreign];
			}
			$key++;
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);
	}

	/**
	 * Writes the internal itemArray to MM table:
	 *
	 * @param	string		MM table name
	 * @param	integer		Local UID
	 * @param	boolean		If set, then table names will always be written.
	 * @param	string		If set, the "ident" field will be written
	 * @param	boolean		If set, uid_local and uid_foreign field names will be swapped
	 * @return	void
	 */
	function writeMM($tableName, $uid, $prependTableName=0, $ident='', $swapLocalForeign=0)	{

		if ($swapLocalForeign) {
			$uid_foreign = 'uid_local';
			$uid_local = 'uid_foreign';
		} else {
			$uid_local = 'uid_local';
			$uid_foreign = 'uid_foreign';
		}

		$where = $uid_local.'='.intval($uid);
		if ($ident) {
			$where.= ' AND ident="'.$GLOBALS['TYPO3_DB']->quoteStr($ident, $tableName).'"';
		}

			// Delete all relations:
		$GLOBALS['TYPO3_DB']->exec_DELETEquery($tableName, $where);

			// If there are tables...
		$tableC = count($this->tableArray);
		if ($tableC)	{
			$prep = ($tableC>1||$prependTableName) ? 1 : 0;
			$c=0;
			$tName=array();

				// For each item, insert it:
			foreach($this->itemArray as $val)	{
				$c++;

				$insertFields = array(
					$uid_local => $uid,
					$uid_foreign => $val['id'],
					'sorting' => $c
				);
				if ($prep || $val['table']=='_NO_TABLE')	{
					$insertFields['tablenames'] = $val['table'];
				}
				if ($ident)	{
					$insertFields['ident'] = $ident;
				}

				$GLOBALS['TYPO3_DB']->exec_INSERTquery($tableName, $insertFields);
			}
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_loaddbgroup.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_t3lib_loaddbgroup.php']);
}
?>
