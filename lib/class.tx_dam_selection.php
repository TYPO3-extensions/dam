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
 *   78: class tx_dam_selection 
 *  101:     function init(&$pObj)	
 *
 *              SECTION: selection to query definition conversion
 *  122:     function getSelectionWhereClauseArray () 
 *  188:     function getWhereClausePart($queryType, $operator, $cat, $id, $value) 
 *
 *              SECTION: selection array processing
 *  216:     function mergeSelection ($sel) 
 *  341:     function cleanSelectionArray($sel, $removeEmptyValues=TRUE, $countDown=2) 
 *
 *              SECTION: selection storage / undo
 *  385:     function initSelection_getStored_mergeSubmitted() 
 *  401:     function setCurrentSelectionFromStored() 
 *  416:     function storeSelection() 
 *  428:     function storeCurrentSelectionAsUndo() 
 *  455:     function undoSelection() 
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */




require_once(PATH_t3lib.'class.t3lib_modsettings.php');

require_once(PATH_txdam.'lib/class.tx_dam_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_types.php');
require_once(PATH_txdam.'lib/class.tx_dam_div.php');



/**
 * Selection
 * Generates SQL queries from selection commands
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_selection {

	/**
	 * Current selection definition
	 * From this definition a SQL SELECT can be build.
	 */
	var $sel=array();


	/**
	 * Name of the command paramter
	 */
	var $paramStr = 'SLCMD';

	var $pObj;


	/**
	 * Initializes the object
	 * 
	 * @param	[type]		$$pObj: ...
	 * @return	void		
	 */
	function init(&$pObj)	{
		$this->pObj = &$pObj;
	}





	/********************************
	 *
	 * selection storage / undo
	 *
	 ********************************/
	 
	 
	/**
	 * Get the users last stored selection or processes.an undo command
	 * 
	 * @return	void		
	 */
	function initSelection_getStored_mergeSubmitted() {
		
		if (t3lib_div::_GP($this->paramPrefix.'_undo')) {
			$this->undoSelection ();
		} else {
			$this->setCurrentSelectionFromStored();
			$this->mergeSelection(t3lib_div::_GP($this->paramStr));
			$this->storeCurrentSelectionAsUndo();
			$this->storeSelection();
		}
	}
		
	/**
	 * Get the users last selection from MOD_SETTINGS and set it as current.
	 * 
	 * @return	void		
	 */
	function setCurrentSelectionFromStored() {
		global $SOBE;
		
		$this->sel = unserialize($SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS]);
		if (!is_array($this->sel)) {
			$this->sel = array();
		}
	}


	/**
	 * Store the current setting.
	 * 
	 * @return	void		
	 */
	function storeSelection() {
		global $SOBE;
		
		$SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($SOBE->MOD_MENU, array($this->store_MOD_SETTINGS => serialize($this->sel)), $SOBE->MCONF['name'], 'ses');
	}
	
	
	/**
	 * Store the current setting in the undo storage.
	 * 
	 * @return	void		
	 */
	function storeCurrentSelectionAsUndo() {
		global $SOBE;

		$undo = unserialize($SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS.'_undo']);
		if (!is_array($undo)) {
			$undo = array();
		} 
		
			// save only if different from previous
		$lastUndo = end($undo);
		$lastUndo = serialize($lastUndo['undo']);
		if($lastUndo!=serialize($this->sel)) {

			$undo[]['undo'] = $this->sel;
	
				//remove too many entries
			$undo = array_slice ($undo, min(0,count($undo)-10), 10);
			$SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($SOBE->MOD_MENU, array($this->store_MOD_SETTINGS.'_undo' => serialize($undo)), $SOBE->MCONF['name'], 'ses');
		}
	}


	/**
	 * Get the last selection from the undo storage and set it as current selection.
	 * 
	 * @return	void		
	 */
	function undoSelection() {
		global $SOBE;
		
		$undo = unserialize($SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS.'_undo']);
		array_pop ($undo);
		$sel = end ($undo);
		$this->sel = $sel['undo'];

		$SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($SOBE->MOD_MENU, array($this->store_MOD_SETTINGS => serialize($this->sel),$this->store_MOD_SETTINGS.'_undo' => serialize($undo)), $SOBE->MCONF['name'], 'ses');
		$this->setCurrentSelectionFromStored();
	}



	/********************************
	 *
	 * selection to query definition conversion
	 *
	 ********************************/


	/**
	 * Transforms selection array entries into an array for the db select array.
	 * 
	 * @return	array		db select array where clauses
	 */
	function getSelectionWhereClauseArray () {
		$queryArr = array();
		$sel = $this->sel;
		
		foreach (array('SELECT','OR','AND','NOT','SEARCH') as $queryType) {
			if(is_array($sel[$queryType])) {
				foreach ($sel[$queryType] as $cat => $items) {
					if(is_array($items)) {
						foreach($items as $id => $value) {
							if($value) {
								
								$key=$cat.'.'.$id;
								
								switch($queryType) {
									case 'SELECT':
									case 'OR':
										list($queryType, $query) = $this->getWhereClausePart($queryType, '=', $cat, $id, $value);
										if ($queryType) {
											$queryType = $queryType=='SELECT' ? 'OR' : $queryType;
											$queryArr[$queryType][$key] = $query;
										}
									break;
									case 'NOT':
										$query['NOT'][$key] = 
										list($queryType, $query) = $this->getWhereClausePart($queryType, '!=', $cat, $id, $value);
										if ($queryType) {
											$queryArr[$queryType][$key] = $query;
										}
									break;
									default:
										list($queryType, $query) = $this->getWhereClausePart($queryType, '=', $cat, $id, $value);
										if ($queryType) {
											$queryArr[$queryType][$key] = $query;
										}
									break;
								}
							}
						}
					}
				}
			}
		}
		if(is_array($sel['DESELECT_ID'])) {
			foreach ($sel['DESELECT_ID'] as $table => $items) {
				if(count($items)) {
					$ids = implode(',',array_keys($items));
					$queryArr['NOT'][$table.'deselect'] = $table.'.uid NOT IN ('.$ids.')';
				}
			}
		}

		return $queryArr;
	}


	/**
	 * Transforms selection array entries into an array for the db select array.
	 * 
	 * @param	[type]		$queryType: ...
	 * @param	[type]		$operator: ...
	 * @param	[type]		$cat: ...
	 * @param	[type]		$id: ...
	 * @param	[type]		$value: ...
	 * @return	string		where clauses
	 */
	function getWhereClausePart($queryType, $operator, $cat, $id, $value) {

		$query = '';
        $obj = &t3lib_div::getUserObj($this->selectionClasses[$cat],'user_',TRUE);
		if (is_object($obj) AND $id)      {
             list($queryType, $query) = $obj->dam_selectProc($queryType, $operator, $cat, $id, $value, $this->pObj);
        } else {
			$queryType = false;
		}
		return array($queryType,$query);
	}





	/********************************
	 *
	 * selection array processing
	 *
	 ********************************/


	/**
	 * Merge the passed selection array with the current selection.
	 * Usefull for GP vars.
	 * 
	 * @param	[type]		$sel: ...
	 * @return	void		
	 */
	function mergeSelection ($sel) {
		
		$sel = $this->cleanSelectionArray($sel, FALSE);
	
			// only one main selection
			// SELECT is in fact the same like AND
		if (is_array($sel['SELECT'])) {
			reset($sel['SELECT']);
			$cat = key($sel['SELECT']);
			if (is_array($sel['SELECT'][$cat])) {
				$id = key($sel['SELECT'][$cat]);
	
				if($sel['SELECT'][$cat][$id]) {
					$this->sel=array();
					$this->sel['SELECT'][$cat][$id]=1;
				} else {
					unset($this->sel['SELECT'][$cat]);
				}
			}
		}

			// OR
		if (is_array($sel['OR'])) {
			foreach($sel['OR'] as $cat => $idArr) {
				foreach($idArr as $id => $set) {
					if ($set) {
							// makes no sense to add it if its already in select
						if(!$this->sel['SELECT'][$cat][$id]) {
							$this->sel['OR'][$cat][$id]=1;
						}
							// remove from NOT
						unset($this->sel['NOT'][$cat][$id]);
					} else {
						unset($this->sel['OR'][$cat][$id]);
					}
				}
			}
		}

			// AND
		if (is_array($sel['AND'])) {
			foreach($sel['AND'] as $cat => $idArr) {
				foreach($idArr as $id => $set) {
					if ($set) {
							// makes no sense to add it if its already in select
						if(!$this->sel['SELECT'][$cat][$id]) {
							$this->sel['AND'][$cat][$id]=1;
						}
							// remove from NOT
						unset($this->sel['NOT'][$cat][$id]);
					} else {
						unset($this->sel['AND'][$cat][$id]);
					}
				}
			}
		}

			// NOT
		if (is_array($sel['NOT'])) {
			foreach($sel['NOT'] as $cat => $idArr) {
				foreach($idArr as $id => $set) {
					if ($set) {
						$this->sel['NOT'][$cat][$id]=1;
							// remove from AND and OR
						unset($this->sel['AND'][$cat][$id]);
						unset($this->sel['OR'][$cat][$id]);
					} else {
						unset($this->sel['NOT'][$cat][$id]);
					}
				}
			}
		}
		
			// DESELECT_ID
		if (is_array($sel['DESELECT_ID'])) {
			foreach($sel['DESELECT_ID'] as $table => $idArr) {
				foreach($idArr as $id => $set) {
					if ($set) {
						$this->sel['DESELECT_ID'][$table][$id]=1;
					} else {
						unset($this->sel['DESELECT_ID'][$table][$id]);
					}
				}
			}
		}

			// get some other value if SELECT is empty from AND or OR
		if (!is_array($this->sel['SELECT']) OR !is_array(current($this->sel['SELECT']))) {
			if (is_array($this->sel['AND']) AND count($this->sel['AND'])) {
				$cat = key($this->sel['AND']);
				$id=key($this->sel['AND'][$cat]);
				$this->sel['SELECT'][$cat][$id]=1;
				unset($this->sel['AND'][$cat][$id]);
			}
		}
		if (!is_array($this->sel['SELECT']) OR !is_array(current($this->sel['SELECT']))) {
			if (is_array($this->sel['OR']) AND count($this->sel['OR'])) {
				$cat = key($this->sel['OR']);
				$id=key($this->sel['OR'][$cat]);
				$this->sel['SELECT'][$cat][$id]=1;
				unset($this->sel['OR'][$cat][$id]);
			}
		}
		
			// search 
		if (is_array($sel['SEARCH'])) {
			foreach ($sel['SEARCH'] as $cat => $idArr) {
				$this->sel['SEARCH'][$cat] = $idArr;
			}
		}
		
	
		$this->sel = $this->cleanSelectionArray($this->sel);
	}


	/**
	 * remove unused selection array entries
	 * 
	 * @param	[type]		$sel: ...
	 * @param	[type]		$removeEmptyValues: ...
	 * @param	[type]		$countDown: ...
	 * @return	array		
	 */
	function cleanSelectionArray($sel, $removeEmptyValues=TRUE, $countDown=2) {
		if(is_array($sel)) {
			foreach($sel as $type => $catArr) {
				if(is_array($catArr) AND count($catArr)) {	
					foreach($catArr as $cat => $idArr) {					
						if(is_array($idArr) AND count($idArr)) {
							foreach($idArr as $id => $set) {
								if (is_null($set) OR ($removeEmptyValues AND empty($set))) {
									unset($sel[$type][$cat][$id]);
								}
							}
						} else {
							unset($sel[$type][$cat]);
						}
					}
				} else {
					unset($sel[$type]);
				}
			}
				// second time because some main rules may be empty now
			if($countDown) {
				$sel = $this->cleanSelectionArray($sel, $removeEmptyValues, $countDown-1);
			}
		} else {
			$sel=array();
		}
		return $sel;		
	}



}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selection.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selection.php']);
}


?>