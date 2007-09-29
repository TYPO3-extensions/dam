<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2004 René Fritz (r.fritz@colorcube.de)
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
	function init() {
		$this->query = array(
			'DISTINCT' => true,
			'FROM' => array (
				'tx_dam' => 'tx_dam.*',
			),
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
	}


	/**
	 * Init the db select array for BE usage.
	 * 
	 * @param	[type]		$pidList: ...
	 * @return	void		
	 */
	function initBESelect($pidList='') {
		$this->init();
		if ($pidList) {
			$this->query['WHERE']['WHERE']['tx_dam.pid'] = 'AND tx_dam.pid IN ('.$pidList.')';
		}
		$this->query['enableFields']['tx_dam'] = t3lib_BEfunc::deleteClause('tx_dam');
	}

	/**
	 * Init the db select array for FE usage.
	 * 
	 * @return	void		
	 */
	function initFESelect() {
		$this->init();
		if(is_object($this->cObj)) {
			$this->query['enableFields']['tx_dam'] = $this->cObj->enableFields('tx_dam');
		} else {
			$this->query['enableFields']['tx_dam'] = t3lib_BEfunc::BEenableFields('tx_dam').t3lib_BEfunc::deleteClause('tx_dam');
		}
	}



	/***************************************
	 *
	 *	 Modify query definition
	 *
	 ***************************************/
	 
	 
	 
	/**
	 * Add a mm table to the select array.
	 * The where clause have to be added separately.
	 * 
	 * @param	[type]		$mm_table: ...
	 * @param	[type]		$foreign_table: ...
	 * @param	[type]		$local_table: ...
	 * @return	void		
	 * @params string 	mm table. Default: 'tx_dam_mm_cat'
	 * @params string 	foreign table. Default: 'tx_dam_cat'
	 * @params string 	local table. Default: 'tx_dam'
	 */
	function queryAddMM($mm_table='tx_dam_mm_cat',$foreign_table='tx_dam_cat',$local_table='tx_dam')	{
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
	function queryAddCategoryJoin($mmtable='tx_dam_mm_cat')	{
		$this->query['LEFT_JOIN'][$mmtable] = 'tx_dam.uid='.$mmtable.'.uid_local';
	}
	
	
	/**
	 * Add a WHERE definition to the select array.
	 * 
	 * @param	array		$where: ...
	 * @param	string		$key: ...
	 * @return	void		
	 * @params mixed 	string or array. Where clause(s).
	 * @params string 	key to be used for the select array if $where is a string. If empty a md5 hash will be generated from the where clause.
	 */
	function queryAddWhere($where, $key='')	{
		if(is_array($where)) {
			$this->query['WHERE'] = t3lib_div::array_merge_recursive_overrule($this->query['WHERE'], $where);
		} else {
			$key = $key ? $key : md5($key);
			$this->query['WHERE'][$key] = $where;
		}
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
				
				$queryParts['SELECT'].= implode (',',$select['FROM']);
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
			$query[] = '1=1';

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

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_querygen.php']);
}


?>