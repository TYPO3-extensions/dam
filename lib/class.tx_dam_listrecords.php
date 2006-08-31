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
 * **************************************************************/
/**
 * DAM db listing class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   74: class tx_dam_listrecords extends tx_dam_listbase
 *
 *              SECTION: Setup
 *  124:     function tx_dam_listrecords()
 *  134:     function __construct()
 *  163:     function init($table, $dbObj)
 *  185:     function excludeControls($elements)
 *
 *              SECTION: Rendering
 *  208:     function renderList()
 *
 *              SECTION: Column rendering
 *  348:     function getItemAction ($item)
 *  382:     function getItemIcon (&$item)
 *  409:     function isEditableColumn($field)
 *  434:     function getHeaderColumnControl($field)
 *  460:     function getHeaderControl()
 *  491:     function getItemControl($item)
 *
 * TOTAL FUNCTIONS: 11
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam_listbase.php');
require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');

 /**
  * DAM db listing class
  *
  * @author	Rene Fritz <r.fritz@colorcube.de>
  * @package DAM-BeLib
  * @subpackage Lib
  */
class tx_dam_listrecords extends tx_dam_listbase {





	// Used in this class:
	var $no_noWrap = 0;
	var $oddColumnsTDAttr = ''; // If set this is <td>-params for odd columns in addElement. Used with db_layout / pages section

	// Not used in this class - but maybe extension classes...
	var $fixedL = 50; // Max length of strings
	var $headLineCol = '#dddddd'; // Head line color


	// internal
	var $table = ''; // set to the tablename if single-table mode
	var $returnUrl = '';

	var $res;
	var $allItemCount = 0; // Counting the elements no matter what...

	var $addElement_tdParams = array(); // Keys are fieldnames and values are td-parameters to add in addElement();


	var $calcPerms = 0;
	var $currentTable = array();


	var $noControlPanels = 0;





	/***************************************
	 *
	 *	 Setup
	 *
	 ***************************************/



	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_listrecords()	{
		$this->__construct();
	}

	/**
	 * Initialization of object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {
		global $BE_USER;

		parent::__construct();

		$this->showControls = array('viewPage', 'editPage', 'newPage', 'unHidePage', 'movePage', 'pasteIntoPage', 'clearPageCache', 'refresh', 'permsRec', 'revertRec', 'editRec', 'infoRec', 'newRec', 'sortRec', 'unHideRec', 'delRec');
		$this->showControls = array('cvsExp', 'refresh', 'editRec', 'sortRec', 'unHideRec', 'delRec', 'revertRec');

		$this->elementAttr['table'] = ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist"';

		$this->showMultiActions = true;
		$this->showAction = true;
		$this->showIcon = true;


		$this->calcPerms = $GLOBALS['SOBE']->calcPerms;

	}







	/**
	 * Initialize the object
	 *
	 * @param	string		$table: ...
	 * @param	object		$dbObj Data object (iterator)
	 * @return	void
	 */
	function init($table, $dbObj) {
		global $TCA, $BE_USER, $BACK_PATH;
// TODO

		$this->dataObjects['db'] = $dbObj;
		$this->table = $table;
		$this->returnUrl = t3lib_div::_GP('returnUrl');



		if ($this->paramName['recs'] AND !count($this->recs)) {
			$this->recs = t3lib_div::_GP($this->paramName['recs']);
		}

	}




// TODO	 misc todo


	/**
	 * Defines control elements not to be shown
	 *
	 * @param	mixed		$elements Comma list or array of control elements to be ecluded from display
	 * @return	void
	 */
	function excludeControls($elements) {
		$elements = is_array($elements) ? $elements : t3lib_div::trimExplode(',', $elements, 1);
		$this->showControls = array_diff($this->showControls, $elements);
	}







	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Creates the listing of records from a single table
	 *
	 * @return	string		HTML table with the listing for the record.
	 */
	function renderList() {
		global $TCA, $BACK_PATH, $LANG;



		if ($this->pointer->countTotal) {



			// Fixing a order table for sortby tables
			$this->currentTable = array();
			$doSort = ($TCA[$this->table]['ctrl']['sortby'] && !$this->sortField);

			$prevUid = 0;
			$prevPrevUid = 0;
			$accRows = array(); // Accumulate rows here



			foreach ($this->dataObjects as $list) {

				if ($list->count())	{

					while ($list->valid()) {

						$row = $list->current();


						$accRows[] = $row;
						$this->currentTable['idList'][] = $row['uid'];
						if ($doSort) {
							if ($prevUid) {
								$this->currentTable['prev'][$row['uid']] = $prevPrevUid;
								$this->currentTable['next'][$prevUid] = '-'.$row['uid'];
								$this->currentTable['prevUid'][$row['uid']] = $prevUid;
							}
							$prevPrevUid = isset ($this->currentTable['prev'][$row['uid']]) ? - $prevUid : $row['pid'];
							$prevUid = $row['uid'];
						}
						$list->next();
					}
				}
			}
			unset($this->dbObj);



				// render item rows
			$this->allItemCount = $this->pointer->firstItemNum;
			$itemCount = count($accRows);
			$itemCurrentCount = 0;

			$tdStyleAppend = '';

			foreach ($accRows as $item) {

				$itemCurrentCount ++;

				$item['__type'] = 'record';
				$item['__table'] = $this->table;

					// 	Columns rendering
				if ($this->showMultiActions)	$itemMultiAction = $this->getItemMultiAction ($item);
				if ($this->showAction)	$itemAction = $this->getItemAction ($item);
				if ($this->showIcon)	$itemIcon = $this->getItemIcon ($item);

				#$itemColumns = $this->getItemColumns ($item);

				$itemColumns = array();
				foreach($this->columnList as $fCol => $dummy) {
					if ($fCol == $this->titleColumnKey) {

						$recTitle = t3lib_BEfunc::getRecordTitle($this->table, $item, 1);
						$recTitle = $this->linkWrapFile($recTitle, $item);

						$thumbImg = '';
						if ($this->showThumbs) {
							$thumbImg = '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item).'</div>';
						}

						$itemColumns[$fCol] = $recTitle.$thumbImg;
					}
					elseif ($fCol == 'pid') {
						$itemColumns[$fCol] = $item[$fCol];
					}
					elseif ($fCol == '_CONTROL_') {
						$itemColumns[$fCol] = $this->getItemControl($item);
					} else {
						$itemColumns[$fCol] = t3lib_BEfunc::getProcessedValueExtra($this->table, $fCol, $item[$fCol], 100);
					}
				}




				# $trStyle = '';
				$trStyle = ' background-color:'.$this->colorTREven.';';
				if ($this->showAlternateBgColors) {
					if ($this->allItemCount % 2) {
						$trStyle = ' background-color:'.$this->colorTREven.';';
					}
					else {
						$trStyle = ' background-color:'.$this->colorTROdd.';';
					}
				}

					// this is the last line which should have a line afterwards
				if($itemCurrentCount==$itemCount) {
					$tdStyleAppend = ' border-bottom:1px solid #888;';
				}



				$this->addRow(	array(
						'multiAction' => $itemMultiAction,
						'action' => $itemAction,
						'icon' => $itemIcon,
						'data' => $itemColumns,
						'tdAttribute' => $this->elementAttr['itemTD'],
						'tdStyle' => $this->elementStyle['itemTD'].$tdStyleAppend,
						'trStyle' => $trStyle,
						'trHover' => true,
					));

				$this->allItemCount++;
			}


		}
	}




	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/



	/**
	 * Renders the multi-action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemMultiAction ($item) {

		$multiActionID = $this->table.':'.$item['uid'];
		$multiActionSelected = $this->recs[$this->table][$item['uid']];

		$multiAction = '<input type="checkbox" name="'.$this->paramName['recs'].'[]" value="'.$multiActionID.'"'.($multiActionSelected?' checked="checked"':'').' />';

		return $multiAction;
	}


	/**
	 * Renders the action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemAction ($item) {
		global $LANG;

		$itemAction = '';

		if ($this->table == 'tx_dam') {

// TODO abstraction
			if($GLOBALS['SOBE']->selection->sl->sel['NOT']['txdamRecords'][$item['uid']]) {
				$params = 'SLCMD[NOT][txdamRecords]['.$item['uid'].']=0';
				$actionIcon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_reselect.gif', 'width="11" height="10"').' title="'.$LANG->getLL('reselect').'" alt="" />';
				$itemAction = '<a href="index.php?'.$params.'">'.$actionIcon.'</a>';
			} else {
				$params='SLCMD[NOT][txdamRecords]['.$item['uid'].']=1';
				$actionIcon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_deselect.gif', 'width="11" height="10"').' title="'.$LANG->getLL('deselect').'" alt="" />';
				$itemAction = '<a href="index.php?'.$params.'">'.$actionIcon.'</a>';
			}
		}


		return $itemAction;
	}



	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon (&$item) {
		static $titleNotIndexed;
		static $iconNotIndexed;

		if(!$iconNotIndexed) {
			$titleNotIndexed = 'title="'.$GLOBALS['LANG']->getLL('fileNotExists').'"';
			$iconNotIndexed = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/error_h.gif', 'width="10" height="10"').' '.$titleNotIndexed.' alt="" />';
		}

		$titletext = t3lib_BEfunc::getRecordIconAltText($item, $this->table);
		$itemIcon = t3lib_iconWorks::getIconImage($this->table, $item, $GLOBALS['BACK_PATH'] ,'title="'.$titletext.'"');
		if (!is_file(tx_dam::file_absolutePath($item))) {
			$item['file_status'] = TXDAM_status_file_missing;
			$itemIcon.= $iconNotIndexed;
		}
#		$itemIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($itemIcon, $this->table, $item['uid']);
		return $itemIcon;
	}


	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/


	function isEditableColumn($field) {
		global $TCA;

		$editable = false;
		$permsEdit = $this->calcPerms & ($this->table == 'pages' ? 2 : 16);
		if (
			$permsEdit AND
			!$TCA[$this->table]['ctrl']['readOnly'] AND
			$TCA[$this->table]['columns'][$field] AND
			!($TCA[$this->table]['columns'][$field]['config']['type']=='none') AND
			!($TCA[$this->table]['columns'][$field]['config']['form_type']=='none') AND
			!($TCA[$this->table]['columns'][$field]['config']['readOnly'])
		) {
				$editable = true;
		}
		return $editable;
	}


	/**
	 * Creates the column control panel for the header.
	 *
	 * @param 	string 	$field Column key
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderColumnControl($field) {

		$content = '';

		if ($this->isEditableColumn($field) AND
			is_array($this->currentTable['idList']) AND
			in_array('editRec', $this->showControls)
			) {

			$editIdList = implode(',', $this->currentTable['idList']);
			$params = '&edit['.$this->table.']['.$editIdList.']=edit&columnsOnly='.$field.'&disHelp=1';
			$iTitle = sprintf($GLOBALS['LANG']->getLL('editThisColumn'), preg_replace('#:$#', '', trim($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($this->table, $field)))));
			$content = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif', 'width="11" height="12"').' title="'.htmlspecialchars($iTitle).'" alt="" />';
			$onClick = t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'],-1);
			$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$content.'</a>';
		}

		return $content;
	}


	/**
	 * Creates the control panel for the header.
	 *
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderControl() {
		global $TCA;

		$permsEdit = $this->calcPerms & ($this->table == 'pages' ? 2 : 16);

		if (
				$permsEdit AND
				!$TCA[$this->table]['ctrl']['readOnly'] AND
				is_array($this->currentTable['idList']) AND
				in_array('editRec', $this->showControls)
			) {

			$editIdList = implode(',', $this->currentTable['idList']);
			$columnsOnly = implode(',', array_keys($this->columnList));
			$params = '&edit['.$this->table.']['.$editIdList.']=edit&columnsOnly='.$columnsOnly.'&disHelp=1';
			$content = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif', 'width="11" height="12"').' title="'.$GLOBALS['LANG']->getLL('editShownColumns').'" />';
			$onClick = t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], -1);
			$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$content.'</a>';
		}

		return $content;
	}



	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item)	{
		static $actionCall, $localCalcPerms, $permsEdit;

		$content = '';

		if($this->showControls) {
			if(!is_object($actionCall)) {
				$table = 'tx_dam';

				t3lib_div::loadTCA($table);

				if ($table == 'pages') {
						// If the listed table is 'pages' we have to request the permission settings for each page:
					$localCalcPerms = $GLOBALS['BE_USER']->calcPerms($item);
					$permsEdit = ($localCalcPerms & 2);
					$permsDelete = ($localCalcPerms & 4);
				} else {
						// This expresses the edit permissions for this particular element:
					$permsEdit = ($this->calcPerms & 16);
					$permsDelete = ($this->calcPerms & 16);
				}


				$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
				$actionCall->setRequest('control', array('__type' => 'record', '__table' => $table), '', $GLOBALS['MCONF']['name']);
				$actionCall->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
				$actionCall->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
				$actionCall->setEnv('calcPerms', $this->calcPerms);
				$actionCall->setEnv('permsEdit', $permsEdit);
				$actionCall->setEnv('permsDelete', $permsDelete);
				$actionCall->initActions(true);
			}
// TODO set allow deny: $this->showControls

			$actionCall->setRequest('control', $item, '', $GLOBALS['MCONF']['name']);
			$actions = $actionCall->renderActionsHorizontal(true);

				// Compile items into a DIV-element:
			$content = '
											<!-- CONTROL PANEL: '.htmlspecialchars($item['file_name']).' -->
											<div class="typo3-DBctrl">'.implode('', $actions).'</div>';
		}

		return $content;

// TODO how to add spacer with actions?
		$actions[] = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clear.gif', 'width="12" height="12"').' alt="" />';

	}




########// TODO######################




	/***************************************
	 *
	 *	 Clipboard
	 *
	 ***************************************/





}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listrecords.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listrecords.php']);
}
?>