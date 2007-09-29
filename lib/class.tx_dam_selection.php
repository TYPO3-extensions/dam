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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   79: class tx_dam_selection
 *  113:     function init(&$pObj, &$SOBE, $selectionClasses, $paramPrefix='slqg', $store_MOD_SETTINGS='')
 *  127:     function hasSelection()
 *  137:     function serialize()
 *  150:     function setFromSerialized($sel, $storeAsCurrent=true)
 *
 *              SECTION: selection storage / undo
 *  177:     function initSelection_getStored_mergeSubmitted()
 *  194:     function setCurrentSelectionFromStored()
 *  207:     function storeSelection()
 *  217:     function storeCurrentSelectionAsUndo()
 *  243:     function undoSelection()
 *
 *              SECTION: selection to query definition conversion
 *  268:     function getSelectionWhereClausearray()
 *  332:     function getWhereClausePart($queryType, $operator, $cat, $id, $value)
 *  348:     function setFieldMapping($table, $fieldMapping)
 *  355:     function getFieldMapping($table, $field)
 *
 *              SECTION: selection array processing
 *  379:     function mergeSelection ($sel)
 *  505:     function cleanSelectionArray($sel, $removeEmptyValues=TRUE, $countDown=2)
 *
 * TOTAL FUNCTIONS: 15
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */







/**
 * Selection compiler
 *
 * Generates SQL queries from selection commands by calling registered selection classes
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
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

	/**
	 * prefix used for special commands (undo)
	 */
	var $paramPrefix = 'slqg';

	var $pObj;
	var $SOBE;
	var $selectionClasses;


	/**
	 * Initializes the object
	 *
	 * @param	object		$pObj That is the object that includes this object
	 * @param	object		$SOBE That is the object that is the module and stores the session data
	 * @param 	array 		$selectionClasses Array of class resources
	 * @param	string		$paramPrefix Name of a prefix used for special commands (undo).
	 * @param	string		$store_MOD_SETTINGS Name of the MOD_SETTINGS key to store the selection.
	 * @return	void
	 */
	function init(&$pObj, &$SOBE, $selectionClasses, $paramPrefix='slqg', $store_MOD_SETTINGS='')	{
		$this->pObj = &$pObj;
		$this->SOBE = &$SOBE;
		$this->selectionClasses = $selectionClasses;
		$this->paramPrefix = $paramPrefix;
		$this->store_MOD_SETTINGS = $store_MOD_SETTINGS ? $store_MOD_SETTINGS : $this->paramPrefix.'_select';
	}


	/**
	 * Checks if there's a selection
	 *
	 * @return	boolean		returns true if there is a selection
	 */
	function hasSelection()	{
		return count($this->sel);
	}


	/**
	 * Serializes the current selection
	 *
	 * @return	string		returns a serialized selection definition
	 */
	function serialize()	{
		#return t3lib_div::array2xml($this->sel);
		return serialize($this->sel);
	}


	/**
	 * Unserializes a selection
	 *
	 * @param	string		$sel serialized selection definition
	 * @return	array		returns a selection definition
	 */
	function unserialize($sel)	{
		#return t3lib_div::array2xml($sel);
		return $this->cleanSelectionArray(unserialize($sel));
	}


	/**
	 * Set current selection from serialized data
	 *
	 * @param	string		$sel serialized selection definition
	 * @param	boolean		$storeAsCurrent If set the selection will be stored in session as the current selection.
	 * @return	void
	 */
	function setFromSerialized($sel, $storeAsCurrent=true)	{
		$sel = $this->unserialize($sel);
		$this->sel = array();
		if(is_array($sel)) {
			$this->sel = $sel;
			if($storeAsCurrent) {
				$this->storeCurrentSelectionAsUndo();
				$this->storeSelection();
			}
		}
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
			$this->mergeSelection(t3lib_div::GParrayMerged($this->paramStr));
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
		$this->sel = false;
		if(is_object($this->SOBE)) {
			$this->sel = $this->unserialize($this->SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS]);
		}
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
		if(is_object($this->SOBE)) {
			$this->SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->SOBE->MOD_MENU, array($this->store_MOD_SETTINGS => serialize($this->sel)), $this->SOBE->MCONF['name'], 'ses');
		}
	}


	/**
	 * Store the current setting in the undo storage.
	 *
	 * @return	void
	 */
	function storeCurrentSelectionAsUndo() {

		if(is_object($this->SOBE)) {
			$undo = $this->unserialize($this->SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS.'_undo']);
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
				$this->SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->SOBE->MOD_MENU, array($this->store_MOD_SETTINGS.'_undo' => serialize($undo)), $this->SOBE->MCONF['name'], 'ses');
			}
		}
	}


	/**
	 * Get the last selection from the undo storage and set it as current selection.
	 *
	 * @return	void
	 */
	function undoSelection() {

		if(is_object($this->SOBE)) {
			$undo = $this->unserialize($this->SOBE->MOD_SETTINGS[$this->store_MOD_SETTINGS.'_undo']);
			array_pop ($undo);
			$sel = end ($undo);
			$this->sel = $sel['undo'];

			$this->SOBE->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->SOBE->MOD_MENU, array($this->store_MOD_SETTINGS => serialize($this->sel),$this->store_MOD_SETTINGS.'_undo' => serialize($undo)), $this->SOBE->MCONF['name'], 'ses');
			$this->setCurrentSelectionFromStored();
		}
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
	function getSelectionWhereClauseArray() {
		$queryArr = array();
		$sel = $this->sel;

		foreach (array('SELECT','OR','AND','NOT','SEARCH') as $queryType) {
			if(is_array($sel[$queryType])) {
				foreach ($sel[$queryType] as $selectionRuleName => $items) {

						// if selection class can handle an array of items we call that
					if(is_array($items)) {
						$obj = &t3lib_div::getUserObj($this->selectionClasses[$selectionRuleName],'user_',TRUE);
						if (is_object($obj) AND method_exists($obj, 'selection_getQueryPartForItems'))      {
							list($queryTypeTarget, $query) = $obj->selection_getQueryPartForItems($queryType, $selectionRuleName, $items, $this->pObj);
							if ($queryTypeTarget AND $query) {
								$key = $selectionRuleName.'.'.md5(implode(array_keys($items)));
								$queryArr[$queryTypeTarget][$key] = $query;
							}

						} else {

							foreach($items as $id => $value) {

								$queryTypeTarget = $query = '';

								switch($queryType) {
									case 'SELECT':
									case 'OR':
										list($queryTypeTarget, $query) = $this->getWhereClausePart($queryType, '=', $selectionRuleName, $id, $value);
									break;
									case 'NOT':
										list($queryTypeTarget, $query) = $this->getWhereClausePart($queryType, '!=', $selectionRuleName, $id, $value);
									break;
									default:
										list($queryTypeTarget, $query) = $this->getWhereClausePart($queryType, '=', $selectionRuleName, $id, $value);
									break;
								}

								if ($queryTypeTarget AND $query) {
									$key = $selectionRuleName.'.'.$id;
									$queryTypeTarget = $queryTypeTarget=='SELECT' ? 'OR' : $queryTypeTarget;
									$queryArr[$queryTypeTarget][$key] = $query;
								}
							}
						}
					}
				}
			}
		}

		return $queryArr;
	}


	/**
	 * Get selection array entries for a given selection rule.
	 *
	 * @param	string		$selectionRuleName Category / selection rule - corresponds to the "treename" used for the category tree in the nav. frame
	 * @return	array		selection array entries
	 */
	function getSelectionArrayFor($selectionRuleName) {
		$selArr = array();
		$sel = $this->sel;

		foreach (array('SELECT','OR','AND','NOT','SEARCH') as $queryType) {
			if(is_array($sel[$queryType])) {
				foreach ($sel[$queryType] as $cat => $items) {
					if($cat==$selectionRuleName AND is_array($items)) {
						$selArr[$queryType][$selectionRuleName] = $items;
					}
				}
			}
		}

		return $selArr;
	}

	/**
	 * Transforms selection array entries into an array for the db select array.
	 *
	 * @param	string		$queryType Query type: AND, OR, ...
	 * @param	string		$operator Operator, eg. '!=' - see DAM Documentation
	 * @param	string		$selectionRuleName Category / selection rule - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		$id The select value/id
	 * @param	string		$value The select value (true/false,...)
	 * @return	string		where clause
	 */
	function getWhereClausePart($queryType, $operator, $selectionRuleName, $id, $value) {
		$query = '';
		$obj = &t3lib_div::getUserObj($this->selectionClasses[$selectionRuleName],'user_',TRUE);
		if (is_object($obj) AND !((string)$id==''))      {
			 list($queryType, $query) = $obj->selection_getQueryPart($queryType, $operator, $selectionRuleName, $id, $value, $this->pObj);
		} else {
			$queryType = false;
		}
		return array($queryType,$query);
	}


	/**
	 * TODO
	 */
	function setFieldMapping($table, $fieldMapping) {
		$this->fieldMapping[$table] = $fieldMapping;
	}

	/**
	 * TODO
	 */
	function getFieldMapping($table, $field) {
		$fieldMapped = '';
		if (isset($this->fieldMapping[$table][$field])) {
			$fieldMapped = $this->fieldMapping[$table][$field];
		} else {
			$fieldMapped = $table.'.'.$field;
		}
		return $fieldMapped;
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
	 * @param	array		$sel Passed selection array
	 * @return	void
	 */
	function mergeSelection ($sel) {

		$sel = $this->cleanSelectionArray($sel, FALSE);

			// only one main selection
			// SELECT is in fact the same as AND
		if (is_array($sel['SELECT'])) {
			reset($sel['SELECT']);
			$cat = key($sel['SELECT']);
			if (is_array($sel['SELECT'][$cat])) {
				$id = key($sel['SELECT'][$cat]);

				if($set=$sel['SELECT'][$cat][$id]) {
					$this->sel=array();
					$this->sel['SELECT'][$cat][$id]=$set;
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
							$this->sel['OR'][$cat][$id]=$set;
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
							$this->sel['AND'][$cat][$id]=$set;
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
						$this->sel['NOT'][$cat][$id]=$set;
							// remove from AND and OR
						unset($this->sel['AND'][$cat][$id]);
						unset($this->sel['OR'][$cat][$id]);
					} else {
						unset($this->sel['NOT'][$cat][$id]);
					}
				}
			}
		}

			// get some other value if SELECT is empty from AND or OR
		if (!is_array($this->sel['SELECT']) OR !is_array(current($this->sel['SELECT']))) {
			if (is_array($this->sel['AND']) AND count($this->sel['AND'])) {
				$cat = key($this->sel['AND']);
				$id=key($this->sel['AND'][$cat]);
				$set=$this->sel['AND'][$cat][$id];
				$this->sel['SELECT'][$cat][$id]=$set;
				unset($this->sel['AND'][$cat][$id]);
			}
		}
		if (!is_array($this->sel['SELECT']) OR !is_array(current($this->sel['SELECT']))) {
			if (is_array($this->sel['OR']) AND count($this->sel['OR'])) {
				$cat = key($this->sel['OR']);
				$id=key($this->sel['OR'][$cat]);
				$set=$this->sel['OR'][$cat][$id];
				$this->sel['SELECT'][$cat][$id]=$set;
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
	 * @param	array		$sel Selection array
	 * @param	boolean		$removeEmptyValues Removes empty values from selection
	 * @param	integer		$countDown Private parameter
	 * @return	array
	 */
	function cleanSelectionArray($sel, $removeEmptyValues=TRUE, $countDown=2) {

			// backward compatibility
		if ($sel['DESELECT_ID']['tx_dam']) {
			$sel['NOT']['txdamRecords'] = $sel['DESELECT_ID']['tx_dam'];
		}
		unset($sel['DESELECT_ID']);

		if(is_array($sel)) {
			foreach($sel as $type => $catArr) {
				if(is_array($catArr) AND count($catArr)) {
					foreach($catArr as $cat => $idArr) {

						$obj = &t3lib_div::getUserObj($this->selectionClasses[$cat],'user_',TRUE);

						if(is_object($obj) AND is_array($idArr) AND count($idArr)) {
							foreach($idArr as $id => $set) {
								if (is_null($set) OR ($removeEmptyValues AND empty($set)) OR ($removeEmptyValues AND $set==$obj->deselectValue)) {
									unset($sel[$type][$cat][$id]);
								}
							}
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