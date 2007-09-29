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
 * Query generator
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
 *   68: class tx_dam_querygen 
 *   83:     function tx_dam_querygen() 
 *
 *              SECTION: Initialize
 *  101:     function init() 
 *  129:     function initBESelect($pidList='') 
 *  142:     function initFESelect() 
 *
 *              SECTION: Modify query definition
 *  173:     function queryAddMM($mm_table='tx_dam_mm_cat',$foreign_table='tx_dam_cat',$local_table='tx_dam')	
 *  192:     function queryAddCategoryJoin($mmtable='tx_dam_mm_cat')	
 *  206:     function queryAddWhere($where,$key='')	
 *
 *              SECTION: Create query from definition
 *  229:     function getQuery() 
 *  250:     function getQueryParts() 
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



/**
 * Generates SQL queries
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_querygen {

	/**
	 * Current query definition
	 * From this definition a SQL SELECT can be build.
	 */
	var $query = array();


	var $table = 'tx_dam';

	/**
	 * Initialize the object
	 * 
	 * @return	void		
	 */
	function tx_dam_querygen() {
		$this->init();
	}



	/***************************************
	 *
	 *	 Initialize
	 *
	 ***************************************/


	/**
	 * Initialize the db select definition array.
	 * 
	 * @return	void		
	 */
	function init($table='') {

		if($table) {
			$this->table = $table;
		}

		$this->query = array(
			'DISTINCT' => true,
			'SELECT' => array (),
			'FROM' => array (),
			'LEFT_JOIN' => array (),
			'MM' => array (),
			'WHERE' => array(
				'WHERE' => array(), // self defined
				'AND' => array(), // ...
				'OR' => array(), // ...
				'NOT' => array(), // ...
			),
			'enableFields' => array (),
			'GROUPBY' => array (),
			'ORDERBY' => array (),
			'LIMIT' => array (),
		);
		$this->addSelectFields();
	}


	/**
	 * Init the db select array for BE usage.
	 * 
	 * @param	[type]		$pidList: ...
	 * @return	void		
	 */
	function initBESelect($table='', $pidList='') {
		$this->init($table);
		if ($pidList) {
			$this->query['WHERE']['WHERE'][$this->table.'.pid'] = 'AND '.$this->table.'.pid IN ('.$pidList.')';
		}
		$this->query['enableFields'][$this->table] = $this->enableFields();
		$this->addSelectFields();
	}

	/**
	 * Init the db select array for FE usage.
	 * 
	 * @return	void		
	 */
	function initFESelect($table='', $pidList='') {
		$this->initBESelect($table='', $pidList='');
	}



	/***************************************
	 *
	 *	 Modify query definition
	 *
	 ***************************************/

	/**
	 * 
	 * 
	 * @param	[type]		$count: ...
	 * @return	string		query
	 */
	function setCount($count=false) {
		if($count) {
			$this->query['FROM']['COUNT'] = $this->table.'.uid';
		} else {
			unset($this->query['FROM']['COUNT']);
		}
	}

	/**
	 * defines the main table
	 * 
	 * @return	void		
	 */
	function addSelectFields($fields='*', $table='') {
		$table = $table ? $table :$this->table;
		$this->query['FROM'][$table] = $this->compileFieldList($table, $fields);
	}

	/**
	 * 
	 * 
	 * @param	[type]		$pidList: ...
	 * @return	void		
	 */
	function addPidList($pidList='', $table='') {
		if ($pidList) {
			$table = $table ? $table :$this->table;
			$this->query['WHERE']['WHERE'][$table.'.pidList'] = 'AND '.$table.'.pid IN ('.$pidList.')';
		}
	}

	/**
	 * Init the db select array for FE usage.
	 * 
	 * @return	void		
	 */
	function addEnableFields($table='') {
		$table = $table ? $table :$this->table;
		$this->query['enableFields'][$table] = $this->enableFields($table);
	}

	/**
	 * Adds a LIMIT to the db select array.
	 * 
	 * @param	[type]		$limit: ...
	 * @param	[type]		$begin: ...
	 * @return	void		
	 */
	function addLimit ($limit, $begin='') {

		if($limit) {
			$this->query['LIMIT'] = array((intval($begin)?$begin.',':'').$limit);
#TODO ??			$this->query['LIMIT'][] = (intval($begin)?$begin.',':'').$limit;
		}
	}


	/**
	 * Add a WHERE definition to the select array.
	 * 
	 * @params mixed 	string. Where clause.
	 * @params string 	type: ADD, OR, NOT. Default: WHERE
	 * @params string 	key to be used for the select array. If empty a md5 hash will be generated from the where clause.
	 * @return	void		
	 */
	function addWhere($where, $type='WHERE', $key='')	{
		if(is_array($where)) {
			$this->query['WHERE'] = t3lib_div::array_merge_recursive_overrule($this->query['WHERE'], $where);
		} else {
			$key = $key ? $key : md5($key);
			$this->query['WHERE'][$type][$key] = $where;
		}
	}

	/**
	 * Add a mm table to the select array.
	 * The where clause have to be added separately.
	 * 
	 * @params string 	mm table. Default: 'tx_dam_mm_cat'
	 * @params string 	foreign table. Default: 'tx_dam_cat'
	 * @params string 	local table. Default: 'tx_dam'
	 * @return	void		
	 */
	function queryAddMM($mm_table='tx_dam_mm_cat',$foreign_table='tx_dam_cat',$local_table='tx_dam')	{
		$local_table = $local_table ? $local_table : $this->table;
		$key = $local_table.'.'.$mm_table.'.'.$foreign_table;
		#$this->query['MM'][$key] = $local_table.','.$mm_table.($foreign_table?','.$foreign_table:'');

		$this->query['MM'][$local_table] = '1';
		$this->query['MM'][$mm_table] = '1';
		if($foreign_table) {
			$this->query['MM'][$foreign_table] = '1';
		}

		$this->query['WHERE']['AND'][$key] = $local_table.'.uid='.$mm_table.'.uid_local'.($foreign_table?' AND '.$foreign_table.'.uid='.$mm_table.'.uid_foreign ':'');
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$mmtable: ...
	 * @return	[type]		...
	 */
	function addMMJoin($mmtable, $local_table='')	{
		$local_table = $local_table ? $local_table : $this->table;

		$this->query['LEFT_JOIN'][$mmtable] = $local_table.'.uid='.$mmtable.'.uid_local';
	}

	/**
	 * Merge a WHERE definition array to the select WHERE array part.
	 * 
	 * @params mixed 	array. Where clause(s).
	 * @return	void		
	 */
	function mergeWhere($where)	{
		if(is_array($where)) {
			$this->query['WHERE'] = t3lib_div::array_merge_recursive_overrule($this->query['WHERE'], $where);
		}
	}


	/***************************************
	 *
	 *	 compatibility
	 *
	 ***************************************/

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$mmtable: ...
	 * @return	[type]		...
	 */
	function queryAddCategoryJoin($mmtable='tx_dam_mm_cat', $local_table='')	{
		$this->addMMJoin($mmtable, $local_table);
	}


	/***************************************
	 *
	 *	 Create query from definition
	 *
	 ***************************************/


	/**
	 * Generates the query from the select array.
	 * 
	 * @return	string		the query
	 */
	function getQuery() {

		$queryParts = $this->getQueryParts();
		$query = $GLOBALS['TYPO3_DB']->SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE'],
					$queryParts['GROUPBY'],
					$queryParts['ORDERBY'],
					$queryParts['LIMIT']
				);

		return $query;
	}


	/**
	 * Generates the query from the select array.
	 * 
	 * @return	array		array of query parts
	 */
	function getQueryParts() {
		$queryParts=array(
				'SELECT' => '',
				'FROM' => '',
				'WHERE' => '',
				'GROUPBY' => '',
				'ORDERBY' => '',
				'LIMIT' => ''
				);

		$select = $this->query;


		//
		// SELECT (COUNT, DISTINCT)
		//

		$count = $select['FROM']['COUNT'];
		$distinct = $select['DISTINCT'] ? ' DISTINCT ' :'';

		if(!$count) {
			$queryParts['SELECT'].= $distinct;
		}


		if (count($select['FROM'])) {

				// count
			if($select['FROM']['COUNT']) {
				$queryParts['SELECT'].= ' COUNT('.trim($distinct.$select['FROM']['COUNT']).') as count';
				unset($select['FROM']['COUNT']);
			} else {


				//
				// FROM
				//

				$queryParts['SELECT'].= implode (',',$select['FROM']+$select['SELECT']);
			}

				// tables
			$queryParts['FROM'].= ' '.implode (',',array_keys($select['FROM'])+array_keys($select['MM']));


			//
			// LEFT_JOIN
			//

			$query = array();
			foreach($select['LEFT_JOIN'] as $table => $on) {
				$query[] = 'LEFT JOIN '.$table.' ON ('.$on.')';
			}
			$queryParts['FROM'].= "\n".implode ("\n",$query);


			//
			// WHERE
			//

			$query = array();
			$query[] = '1';

			$query[] = implode (' ',$select['WHERE']['WHERE']);
			unset($select['WHERE']['WHERE']);

			foreach($select['WHERE'] as $operator => $items){
				if(is_array($items) AND count($items)) {
					switch($operator) {
						case 'NOT':
							#$query[] = 'AND NOT ('.implode (' OR ',$items).')';
							#$query[] = 'AND NOT '.implode(' AND NOT ',$items);
							$query[] = 'AND '.implode(' AND ',$items);
						break;
						case 'AND':
							$query[] = 'AND '.implode (' AND ',$items);
						break;
						default:
							$query[] = 'AND ('.implode (' '.$operator.' ',$items).')';
						break;
					}
				}
			}
			$query[] = implode (' ',$select['enableFields']);

			$queryParts['WHERE'] = "\n".implode ("\n",$query);


			//
			// GROUPBY, ORDERBY, LIMIT
			//

			if(count($select['GROUPBY'])) {
				$queryParts['GROUPBY'] = implode (',',$select['GROUPBY']);
			}
			if(count($select['ORDERBY']) AND !$count) {
				$queryParts['ORDERBY'] = implode (',',$select['ORDERBY']);
			}
			if(count($select['LIMIT']) AND !$count) {
				$queryParts['LIMIT'] = implode (' ',$select['LIMIT']); #TODO ???
			}
		}

		#debug($queryParts,'$queryParts', __LINE__, __FILE__);
		return $queryParts;
	}


	/***************************************
	 *
	 *	 helper functions
	 *
	 ***************************************/



	/**
	 * Creates part of query for searching after a word ($this->searchString) fields in input table
	 * 
	 * @param	string		Table, in which the fields are being searched.
	 * @param	string		search string
	 * @return	string		Returns part of WHERE-clause for searching, if applicable.
	 */
	function makeSearchQueryPart($table, $searchString)	{
		global $TCA;

			// Make query, only if table is valid and a search string is actually defined:
		if ($TCA[$table] && $searchString)	{

				// Loading full table description - we need to traverse fields:
			t3lib_div::loadTCA($table);

				// Initialize field array:
			$sfields=array();
			$sfields[]='uid';	// Adding "uid" by default.

				// Traverse the configured columns and add all columns that can be searched:
			foreach($TCA[$table]['columns'] as $fieldName => $info)	{
				if ($info['config']['type']=='text' || ($info['config']['type']=='input' && !ereg('date|time|int',$info['config']['eval'])))	{
					$sfields[]=$table.'.'.$fieldName;
				}
			}

				// If search-fields were defined (and there always are) we create the query:
			if (count($sfields))	{
				$like=' LIKE "%'.$GLOBALS['TYPO3_DB']->quoteStr($searchString, $table).'%"';		// Free-text searching...
				$queryPart = '('.implode($like.' OR ',$sfields).$like.')';

					// Return query:
				return $queryPart;
			}
		}
	}

	/**
	 * Returns field list with table name prepended
	 * 
	 * @param	mixed		Field list as array or comma list as string
	 * @param	string		Table name
	 * @return	string		Comma list of fields with table name prepended
	 */
	function compileFieldList($table, $fields) {
		$fieldList = array();

		if ($fields=='*') {
			$fieldList[$table] = $table.'.*';
		} else {
			$fields = is_array($fields) ? $fields : t3lib_div::trimExplode(',', $fields, 1);
			foreach ($fields as $field) {
				$fieldList[$table.'.'.$field] = $table.'.'.$field;
			}
		}

		return implode(',',$fieldList);
	}


#TODO
	/**
	 * Returns a part of a WHERE clause which will filter out records with start/end times or hidden/fe_groups fields set to values that should de-select them according to the current time, preview settings or user login. Definitely a frontend function.
	 * THIS IS A VERY IMPORTANT FUNCTION: Basically you must add the output from this function for EVERY select query you create for selecting records of tables in your own applications - thus they will always be filtered according to the "enablefields" configured in TCA
	 * Simply calls t3lib_pageSelect::enableFields() BUT will send the show_hidden flag along! This means this function will work in conjunction with the preview facilities of the frontend engine/Admin Panel.
	 *
	 * @param	string		The table for which to get the where clause
	 * @return	string		The part of the where clause on the form " AND NOT [fieldname] AND ...". Eg. " AND NOT hidden AND starttime < 123345567"
	 * @see t3lib_pageSelect::enableFields()
	 */
	function enableFields($table='')	{
		$table = $table ? $table : $this->table;

		if (is_object($GLOBALS['TSFE'])) {
			return $GLOBALS['TSFE']->sys_page->enableFields($table);
		} else {
			# return t3lib_BEfunc::BEenableFields($table).t3lib_BEfunc::deleteClause($table);
			return t3lib_BEfunc::deleteClause($table);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php']);
}


?>