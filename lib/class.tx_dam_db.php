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
 *   92: class tx_dam_db 
 *
 *              SECTION: tx_dam_db::....
 *  116:     function checkFileIsIndexed ($fileName,$path,$mtime) 
 *  151:     function updateRecordStatus($uid,$status,$mtime=0)	
 *  173:     function insertMetaRecord($meta, $id='NEW', $status=TXDAM_needs_review)	
 *
 *              SECTION: references
 *  252:     function get_mm_fileList($local_table, $local_uid, $select='', $whereClause='', $groupBy='', $orderBy='tx_dam_mm_ref.sorting', $limit=100) 
 *  297:     function get_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='tx_dam_mm_ref.tablenames', $limit=100) 
 *  329:     function exec_SELECT_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='tx_dam_mm_ref.tablenames', $limit=100) 
 *  363:     function SELECT_mm_query($select,$local_table,$mm_table,$foreign_table,$whereClause='',$groupBy='',$orderBy='',$limit='')	
 *
 *              SECTION: categories
 *  395:     function getCatByName ($title) 
 *  400:     function addMetaRecordToCat($uidMetaRecord,$uidCatRecord)	
 *  427:     function getSubRecords ($uidList,$level=1,$fields='*',$table='tx_dam_cat',$where='')	
 *  459:     function getSubRecordsIdList($uidList,$level=1,$table='tx_dam_cat',$where='')	
 *
 *              SECTION: media types
 *  478:     function updateBrowseTypes($meta)	
 *
 *              SECTION: DAM sysfolder
 *  550:     function createDAMFolder($pid=0) 
 *  571:     function getDAMFolders() 
 *  586:     function getDAMFolderPidList() 
 *  596:     function initDAMFolders()	
 *
 *              SECTION: Misc
 *  638:     function compileFieldList($table, $fields, $checkTCA=TRUE) 
 *  668:     function cleanupRecordArray($table, $row) 
 *  685:     function cleanupFieldList($table, $fields) 
 *  705:     function getTCAFieldListArray($table, $mainFieldsOnly=FALSE)	
 *
 * TOTAL FUNCTIONS: 20
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_t3lib.'class.t3lib_befunc.php');

// these constants will change!!
define ('TXDAM_file_unknown', -1);
# < 0 means: not in DAM
define ('TXDAM_file_ok', 1);
define ('TXDAM_file_changed', 2);
define ('TXDAM_needs_review', 3);


/**
 * Misc DAM db functions
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_db {




	/***************************************
	 *
	 *	 tx_dam_db::....
	 *
	 ***************************************/



	/**
	 * do a check if a file is already indexed and have an entry in the DAM table
	 * 
	 * @param	[type]		$fileName: ...
	 * @param	[type]		$path: ...
	 * @param	[type]		$mtime: ...
	 * @return	integer		status value
	 * @params string 	file name
	 * @params string 	file path
	 * @params integer 	file mtime
	 */
	function checkFileIsIndexed ($fileName,$path,$mtime) {
#TODO: pid?
#TODO check for moved files etc - maybe use a different function for that
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,file_mtime,file_status', 'tx_dam', "file_name='".$GLOBALS['TYPO3_DB']->quoteStr($fileName,'tx_dam')."' AND file_path='".$GLOBALS['TYPO3_DB']->quoteStr($path,'tx_dam')."' AND deleted=0");

		### look if more than one record fit and do heavier check

		$uid=0;
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$uid=$row['uid'];

			if ($row['file_mtime']==$mtime) {
				return array(TXDAM_file_ok, $uid);
			} else {

				### check better for same file

				if ($row['file_status']!=TXDAM_file_changed) {
					$this->updateRecordStatus($row['uid'],TXDAM_file_changed,$mtime);
				}
				return array(TXDAM_file_changed, $uid);
			}
		}
		return array(TXDAM_file_unknown, $uid);
	}

	/**
	 * @param	[type]		$uid: ...
	 * @param	[type]		$status: ...
	 * @param	[type]		$mtime: ...
	 * @return	void		
	 * @params integer 	uid - record id
	 * @params integer 	status value
	 * @params integer 	file mtime
	 */
	function updateRecordStatus($uid,$status,$mtime=0)	{
#TODO define proper status values

		$values = array();
		$values['tstamp'] = time();
		$values['file_status'] = $status;
		if ($mtime) {
			$values['file_mtime'] = $mtime;
			$values['date_mod'] = $mtime;
		}
		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.intval($uid), $values);
	}


	/**
	 * @param	[type]		$meta: ...
	 * @param	[type]		$id: ...
	 * @param	[type]		$status: ...
	 * @return	integer		record id
	 * @params array 	meta record values
	 * @params integer 	status value
	 */
	function insertMetaRecord($meta, $id='NEW', $status=TXDAM_needs_review)	{

		$meta = $this->cleanupRecordArray ('tx_dam', $meta);

		#TODO set proper status
		$meta['file_status']=TXDAM_needs_review;

		if(1) {

		require_once (PATH_t3lib.'class.t3lib_tcemain.php');

		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->debug=0;
		$tce->disableRTE=1;
		$tce->stripslashes_values=0;

		#$TCAdefaultOverride = $BE_USER->getTSConfigProp('TCAdefaults');
		#if (is_array($TCAdefaultOverride))	{
		#	$tce->setDefaultsFromUserTS($TCAdefaultOverride);
		#}

		$tce->start(array('tx_dam'=>array($id=>$meta)),array());

		#$tce->process_datamap();
#debug($meta['category']);
		$res = $tce->checkValue('tx_dam','category',$meta['category'],$meta['uid'],($id=='NEW'?'new':'update'),$meta['pid'],$meta['pid']);
		if (isset($res['value']))	{
			$meta['category']=$res['value'];
		}
		if ($id=='NEW') {
			$tce->insertDB('tx_dam',$meta['uid'],$meta);
			$id = $tce->substNEWwithIDs[$meta['uid']];
		} else {
			$tce->updateDB('tx_dam',$meta['uid'],$meta);
		}
		$tce->dbAnalysisStoreExec();


#debug($id);


		} else {
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam',$meta);
		echo $GLOBALS['TYPO3_DB']->sql_error();
		$id = $GLOBALS['TYPO3_DB']->sql_insert_id($res);
		}

		return $id;
	}









	/***************************************
	 *
	 *	 references
	 *
	 ***************************************/




	/**
	 * Make a list of files by a mm-relation to the tx_dam table
	 * 
	 * @param	[type]		$local_table: ...
	 * @param	[type]		$local_uid: ...
	 * @param	[type]		$select: ...
	 * @param	[type]		$whereClause: ...
	 * @param	[type]		$groupBy: ...
	 * @param	[type]		$orderBy: ...
	 * @param	[type]		$limit: ...
	 * @return	[type]		...
	 */
	function get_mm_fileList($local_table, $local_uid, $MM_ident='', $MM_table='tx_dam_mm_ref', $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100) {

		$select = $select ? $select : 'tx_dam.uid, tx_dam.title, tx_dam.file_path, tx_dam.file_name, tx_dam.file_type' ;


		$whereClause = ' AND '.$MM_table.'.tablenames="'.$GLOBALS['TYPO3_DB']->quoteStr($local_table,$MM_table).'"';
		if ($MM_ident) {
			$whereClause.= ' AND '.$MM_table.'.ident="'.$GLOBALS['TYPO3_DB']->quoteStr($MM_ident,$MM_table).'"';
		}

		if(!$orderBy) {
			$orderBy = $MM_table.'.sorting';
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$select,
			'tx_dam',
			$MM_table,
			$local_table,
			'AND '.$local_table.'.uid IN ('.$local_uid.') '.$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);
		$files = array();
		$rows = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$files[$row['uid']] = $row['file_path'].$row['file_name'];
			$rows[$row['uid']] = $row;
		}

		return array('files'=>$files, 'rows'=>$rows);
	}

########## debug


	function SELECT_mm_query($select,$local_table,$mm_table,$foreign_table,$whereClause='',$groupBy='',$orderBy='',$limit='')	{
		$mmWhere = $local_table ? $local_table.'.uid='.$mm_table.'.uid_local' : '';
		$mmWhere.= ($local_table AND $foreign_table) ? ' AND ' : '';
		$mmWhere.= $foreign_table ? $foreign_table.'.uid='.$mm_table.'.uid_foreign' : '';
		return $GLOBALS['TYPO3_DB']->SELECTquery(
					$select,
					($local_table ? $local_table.',' : '').$mm_table.($foreign_table ? ','.$foreign_table : ''),
					$mmWhere.' '.$whereClause,		// whereClauseMightContainGroupOrderBy
					$groupBy,
					$orderBy,
					$limit
				);
	}

#############
	/**
	 * Make a list of references to foreign tables (eg. tt_content) by a mm-relation to the tx_dam table
	 * 
	 * @param	[type]		$tx_dam_uid: ...
	 * @param	[type]		$select: ...
	 * @param	[type]		$whereClause: ...
	 * @param	[type]		$groupBy: ...
	 * @param	[type]		$orderBy: ...
	 * @param	[type]		$limit: ...
	 * @return	[type]		...
	 */
	function get_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100, $MM_table='tx_dam_mm_ref') {

		if(!$orderBy) {
			$orderBy = $MM_table.'.tablenames';
		}

		$res = tx_dam_db::exec_SELECT_mm_refList(
			$tx_dam_uid,
			$select,
			$whereClause,
			$groupBy,
			$orderBy,
			$limit,
			$MM_table
		);

		$rows = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows[$row['uid']] = $row;
		}

		return $rows;
	}


	/**
	 * Make a list of references to foreign tables (eg. tt_content) by a mm-relation to the tx_dam table
	 * 
	 * @param	[type]		$tx_dam_uid: ...
	 * @param	[type]		$select: ...
	 * @param	[type]		$whereClause: ...
	 * @param	[type]		$groupBy: ...
	 * @param	[type]		$orderBy: ...
	 * @param	[type]		$limit: ...
	 * @return	[type]		...
	 */
	function exec_SELECT_mm_refList($tx_dam_uid, $select='', $whereClause='', $groupBy='', $orderBy='', $limit=100, $MM_table='tx_dam_mm_ref') {

		if(!$orderBy) {
			$orderBy = $MM_table.'.tablenames';
		}

		$select = $select ? $select : 'tx_dam.uid, tx_dam.title, tx_dam.file_path, tx_dam.file_name, tx_dam.file_type, '.$MM_table.'.tablenames, '.$MM_table.'.ident' ;
		$whereClause.= $tx_dam_uid ? ' AND tx_dam.uid IN ('.$tx_dam_uid.')' : '';



		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$select,
			'tx_dam',
			$MM_table,
			'',
			$whereClause,
			$groupBy,
			$orderBy,
			$limit
		);

		return $res;
	}




	/***************************************
	 *
	 *	 categories
	 *
	 ***************************************/



	/**
	 * @param	[type]		$title: ...
	 * @return	[type]		...
	 */
	function getCatByName ($title) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam_cat', 'title="'.$GLOBALS['TYPO3_DB']->quoteStr($title,'tx_dam_cat').'"');
		return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	}
/*
	function addMetaRecordToCat($uidMetaRecord,$uidCatRecord)	{

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_dam_mm_cat', 'uid_local='.intval($uidMetaRecord).' AND uid_foreign='.intval($uidCatRecord));

		if (0==$GLOBALS['TYPO3_DB']->sql_affected_rows()) {
			$fields_values = array();
			$fields_values['uid_local'] = intval($uidMetaRecord);
			$fields_values['uid_foreign'] = intval($uidCatRecord);
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_mm_cat',$fields_values);
			echo $GLOBALS['TYPO3_DB']->sql_error();
			return $GLOBALS['TYPO3_DB']->sql_insert_id();
		}
	}
*/



	/**
	 * Returns an array with rows for subrecords with parent_id=$uid
	 * 
	 * @param	integer		UID of record
	 * @param	string		List of fields to select (default is '*')
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param	[type]		$table: ...
	 * @param	[type]		$where: ...
	 * @return	array		Returns the rows if found, otherwise empty array
	 */
	function getSubRecords ($uidList,$level=1,$fields='*',$table='tx_dam_cat',$where='')	{
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
	 * Returns a commalist of sub record ids
	 * 
	 * @param	integer		UIDs of record
	 * @param	string		Additional WHERE clause, eg. " AND blablabla=0"
	 * @param	[type]		$table: ...
	 * @param	[type]		$where: ...
	 * @return	string		Comma-list of record ids
	 */
	function getSubRecordsIdList($uidList,$level=1,$table='tx_dam_cat',$where='')	{
		$rows = tx_dam_db::getSubRecords ($uidList,$level,'uid',$table,$where);
		return implode(',',array_keys($rows));
	}



	/***************************************
	 *
	 *	 media types
	 *
	 ***************************************/


	/**
	 * @param	[type]		$meta: ...
	 * @return	void		
	 * @params array 	meta data. $meta['media_type'] and $meta['file_type'] have to be set
	 */
	function updateBrowseTypes($meta)	{

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		// MEDIA tx_dam_metypes_avail
		$mediaType = intval($meta['media_type']);
#debug($mediaType,'$mediaType');
			// check if media type exists
		if ($TX_DAM['code2media'][$mediaType]) {


				// get the id of the media type record
			$media_id = false;
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_metypes_avail', 'type='.$mediaType);

			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$media_id = $row['uid'];
			}
				// no record - then create one
			if (!$media_id) {
				$sorting = $TX_DAM['code2sorting'][$mediaType];
				$sorting = $sorting ? $sorting : 10000;
##TODO language
				$fields_values = array();
				$fields_values['pid'] = 0;
				$fields_values['parent_id'] = 0;
				$fields_values['tstamp'] = time();
				$fields_values['title'] = $GLOBALS['TYPO3_DB']->quoteStr($TX_DAM['code2media'][$mediaType], 'tx_dam_metypes_avail');
				$fields_values['type'] = $mediaType;
				$fields_values['sorting'] = $sorting;
				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_metypes_avail', $fields_values);
				echo $GLOBALS['TYPO3_DB']->sql_error();
				$media_id = $GLOBALS['TYPO3_DB']->sql_insert_id();
			}

				// get file type record
			$type_id = false;
			if ($media_id) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_dam_metypes_avail', 'title="'.$GLOBALS['TYPO3_DB']->quoteStr($meta['file_type'],'tx_dam_metypes_avail').'" AND parent_id='.$media_id);
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
					$type_id = $row['uid'];
				}
			}
				// no record - then create one
			if (!$type_id) {
				$fields_values = array();
				$fields_values['pid'] = 0;
				$fields_values['parent_id'] = $media_id;
				$fields_values['tstamp'] = time();
				$fields_values['title'] = $GLOBALS['TYPO3_DB']->quoteStr($meta['file_type'], 'tx_dam_metypes_avail');
				$fields_values['type'] = $mediaType;
				$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_metypes_avail', $fields_values);
				echo $GLOBALS['TYPO3_DB']->sql_error();
			}
		}
	}



	/***************************************
	 *
	 *	 DAM sysfolder
	 *
	 ***************************************/


	/**
	 * Create a DAM folders
	 * 
	 * @param	[type]		$pid: ...
	 * @return	void		
	 */
	function createDAMFolder($pid=0) {
		$fields_values = array();
		$fields_values['pid'] = $pid;
		$fields_values['sorting'] = 10111; #TODO
		$fields_values['perms_user'] = 31;
		$fields_values['perms_group'] = 31;
		$fields_values['perms_everybody'] = 31;
		$fields_values['title'] = 'Media';
		$fields_values['doktype'] = 2;
		$fields_values['module'] = 'dam';
		$fields_values['crdate'] = time();
		$fields_values['tstamp'] = time();
		return $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $fields_values);
	}


	/**
	 * Find the DAM folders
	 * 
	 * @return	array		rows of found DAM folders
	 */
	function getDAMFolders() {
		$rows=array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,pid,title', 'pages', 'doktype=2 and module="dam" '.t3lib_BEfunc::deleteClause('pages'));
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$rows[$row['uid']]=$row;
		}
		return $rows;
	}


	/**
	 * Returns pidList of DAM Folders
	 * 
	 * @return	string		commalist of PIDs
	 */
	function getDAMFolderPidList() {
		return implode(',',array_keys(tx_dam_db::getDAMFolders()));
	}


	/**
	 * Find the DAM folders or create one.
	 * 
	 * @return	array		
	 */
	function initDAMFolders()	{
		// creates a DAM folder on the fly
		// not really a clean way ...
		$damFolders = tx_dam_db::getDAMFolders();
		if (!count($damFolders)) {
			tx_dam_db::createDAMFolder();
			$damFolders = tx_dam_db::getDAMFolders();
			#$df = current($damFolders);
			#$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', '', array('pid' => $df['uid']));
			#$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam_cat', '', array('pid' => $df['uid']));
		}
		$df = current($damFolders);
		#$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'pid=0', array('pid' => $df['uid']));
		#$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam_cat', 'pid=0', array('pid' => $df['uid']));

		return array ($df['uid'],$df['uid'],implode(',',array_keys($damFolders)));

#		return array (
#			'defaultPid' => $df['uid'],
#			'defaultFolder' => $df['uid'],
#			'folderList' => implode(',',array_keys($damFolders))
#		);
	}



	/*******************************************
	 *
	 * Misc
	 *
	 *******************************************/	

	/**
	 * Generates a list if tx_dam db fields which are needed to get a proper info about the record
	 * 
	 * @param	boolean		If set the fields are prepended with table.
	 * @param	array		Field list array which should be appended to the list
	 * @return	string		Comma list of fields with table name prepended
	 * @see tx_dam_div::getItemFromRecord()
	 */
	function getInfoFieldListDAM($prependTableName=TRUE, $addFields=array()) {

		$infoFields = tx_dam_db::getTCAFieldListArray('tx_dam', TRUE, $addFields);
		$infoFields['file_name'] = 'file_name';
		$infoFields['file_dl_name'] = 'file_dl_name';
		$infoFields['file_path'] = 'file_path';
		$infoFields['file_size'] = 'file_size';
		$infoFields['file_type'] = 'file_type';
		$infoFields['file_ctime'] = 'file_ctime';
		$infoFields = tx_dam_db::compileFieldList('tx_dam', $infoFields, FALSE, $prependTableName);

		return $infoFields;
	}

	/**
	 * Returns field list with table name prepended
	 * 
	 * @param	string		Table name
	 * @param	mixed		Field list as array or comma list as string
	 * @param	boolean		If set the fields are checked if set in TCA
	 * @param	boolean		If set the fields are prepended with table.
	 * @return	string		Comma list of fields with table name prepended
	 */
	function compileFieldList($table, $fields, $checkTCA=TRUE, $prependTableName=TRUE) {
		global $TCA;

		$fieldList = array();

		$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);

		if ($checkTCA) {
			if (is_array($TCA[$table])) {
				$fields = $this->cleanupFieldList($table, $fields);
			} else {
				$table = NULL;
			}
		}
		if ($table) {
			foreach ($fields as $field) {
				if ($prependTableName) {
					$fieldList[$table.'.'.$field] = $table.'.'.$field;
				} else {
					$fieldList[$field] = $field;
				}
			}
		}
		return implode(',',$fieldList);
	}


	/**
	 * Removes fields from a record row array that are not configured in TCA
	 * 
	 * @param	string		Table name
	 * @param	array		Record row
	 * @return	array		Cleaned row
	 */
	function cleanupRecordArray($table, $row) {
		$allowedFields = $this->getTCAFieldListArray($table);
		foreach ($row as $field => $val) {
			if (!in_array($field, $allowedFields)) {
				unset($row[$field]);
			}
		}
		return $row;
	}

	/**
	 * Removes fields from a field list that are not configured in TCA
	 * 
	 * @param	string		Table name
	 * @param	mixed		Field list as array or comma list as string
	 * @return	array		Cleaned field list as array
	 */
	function cleanupFieldList($table, $fields) {
		$allowedFields = $this->getTCAFieldListArray($table);
		$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);

		foreach ($fields as $key => $field) {
			if (!in_array($field, $allowedFields)) {
				unset($fields[$key]);
			}
		}
		return $fields;
	}

	/**
	 * Returns an array of fields which are configured in TCA for a table.
	 * This includes uid, pid, and ctrl fields.
	 * 
	 * @param	string		Table name
	 * @param	boolean		If true not all fields from the TCA columns-array will be used but the ones from the ctrl-array
	 * @param	array		Field list array which should be appended to the list
	 * @return	array		Field list array
	 */
	function getTCAFieldListArray($table, $mainFieldsOnly=FALSE, $addFields=array())	{
		global $TCA;

		$fieldListArr=array();

		if (!is_array($addFields)) {
			$addFields = t3lib_div::trimExplode(';', $addFields, 1);
		}
		foreach ($addFields as $field)	{
			#if ($TCA[$table]['columns'][$field]) {
				$fieldListArr[$field] = $field;
			#}
		}

		if (is_array($TCA[$table]))	{
			t3lib_div::loadTCA($table);
			if (!$mainFieldsOnly) {
				foreach($TCA[$table]['columns'] as $fieldName => $dummy)	{
					$fieldListArr[$fieldName] = $fieldName;
				}
			}
			$fieldListArr['uid'] = 'uid';
			$fieldListArr['pid'] = 'pid';

			$ctrlFields = array ('label','label_alt','type','typeicon_column','tstamp','crdate','cruser_id','sortby','delete','fe_cruser_id','fe_crgroup_id');
			foreach ($ctrlFields as $field)	{
				if ($TCA[$table]['ctrl'][$field]) {
					$subFields = t3lib_div::trimExplode(',',$TCA[$table]['ctrl'][$field],1);
					foreach ($subFields as $subField)	{
						$fieldListArr[$subField] = $subField;
					}
				}
			}

			if (is_array($TCA[$table]['ctrl']['enablecolumns'])) {
				foreach ($TCA[$table]['ctrl']['enablecolumns'] as $field)	{
					if ($field) {
						$fieldListArr[$field] = $field;
					}
				}
			}
		}
		return $fieldListArr;
	}


	/**
	 * Takes comma-separated lists and arrays and removes all duplicates
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

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_db.php']);
}

?>