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
 * Module extension (addition to function menu) 'edit selection' for the 'Media>List' module.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_dam_list_editsel extends t3lib_extobjbase
 *   72:     function modMenu()
 *   91:     function head()
 *  116:     function main()
 *  278:     function jumpExt(URL,anchor)
 *
 *              SECTION: internal
 *  316:     function fieldSelectBox($table, $allFields, $selectedFields, $formFields = 1)
 *  374:     function makeAllFieldList($table, $dontCheckUser = false, $useExludeFieldList = true)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_listrecords.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');

/**
 * Module extension  'Media>List>Selection'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_list_editsel extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

// TODO move tx_dam_list_list_ ... to main module

		return array(
			'tx_dam_list_editsel_onlyDeselected' => '1',
			'tx_dam_list_list_sortField' => '',
			'tx_dam_list_list_sortRev' => '',
			'tx_dam_list_displayFields' => '',
		);
	}


	/**
	 * Do some init things and aet some styles in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoHeader', 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems->registerFunc('getSearchBox', 'footer');
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
		$this->pObj->guiItems->registerFunc('getStoreControl', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_editsel_onlyDeselected', $LANG->getLL('tx_dam_list_editsel.onlyDeselected'));
	}


	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER, $LANG, $TCA;

		$content = '';


		//
		// get records by query depending on option 'Show deselected only'
		//

		$origSel = $this->pObj->selection->sl->sel;
		if($this->pObj->MOD_SETTINGS['tx_dam_list_editsel_onlyDeselected']) {
			if(is_array($this->pObj->selection->sl->sel['DESELECT_ID']['tx_dam'])) {
				$ids = array_keys($this->pObj->selection->sl->sel['DESELECT_ID']['tx_dam']);
			} else {
				$ids = array(0); //dummy
			}

			unset($this->pObj->selection->sl->sel['DESELECT_ID']);
			$this->pObj->selection->addSelectionToQuery();
			if(is_array($ids)) {
				$this->pObj->selection->qg->query['WHERE']['WHERE']['DESELECT_ID'] = 'AND tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList(implode(',',$ids)).')';
			}

		} else {
			unset($this->pObj->selection->sl->sel['DESELECT_ID']);
			$this->pObj->selection->addSelectionToQuery();
		}
		$this->pObj->selection->sl->sel = $origSel;



		//
		// Use the current selection to create a query and count selected records
		//

		$this->pObj->selection->execSelectionQuery(TRUE);



		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);

		//
		// current selection box
		//

		$content.= $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		$content.= $this->pObj->doc->spacer(25);


			// any records found?
		if($this->pObj->selection->pointer->countTotal) {


			$table = 'tx_dam';
			t3lib_div::loadTCA($table);


			//
			// set fields to display
			//

			$titleColumn = $TCA[$table]['ctrl']['label'];

			$allFields = $this->makeAllFieldList($table);

			$selectedFields = t3lib_div::_GP('tx_dam_list_displayFields');
			$selectedFields = is_array($selectedFields) ? $selectedFields : explode(',', $this->pObj->MOD_SETTINGS['tx_dam_list_displayFields']);


				// remove fields that can not be selected
			if (is_array($selectedFields)) {
				$selectedFields = array_intersect($allFields, $selectedFields);
				if(!count($selectedFields)) {
					$selectedFields[] = $titleColumn;
				}
			} else {
				$selectedFields = array();
				$selectedFields[] = $titleColumn;
			}


				// store field list
			$this->pObj->MOD_SETTINGS['tx_dam_list_displayFields'] = implode(',', $selectedFields);
			$GLOBALS['BE_USER']->pushModuleData($this->pObj->MCONF['name'], $this->pObj->MOD_SETTINGS);



			//
			// set query and sorting
			//


			$orderBy = ($TCA['tx_dam']['ctrl']['sortby']) ? 'tx_dam.'.$TCA['tx_dam']['ctrl']['sortby'] : 'tx_dam.sorting';

			if ($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'])	{
				if (in_array($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $allFields))	{
					$orderBy = 'tx_dam.'.$this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'];
					if ($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev'])	$orderBy.=' DESC';
				}
			}

			$queryFieldList = tx_dam_db::getMetaInfoFieldList(false, $selectedFields);
			$this->pObj->selection->qg->addSelectFields($queryFieldList);
			$this->pObj->selection->qg->addOrderBy($orderBy);

			$this->pObj->selection->addLimitToQuery();
			$res = $this->pObj->selection->execSelectionQuery();




			$dbObj = new tx_dam_iterator_db($res, $this->pObj->selection->pointer->countTotal);

			$dblist = t3lib_div::makeInstance('tx_dam_listrecords');
			$dblist->init('tx_dam', $dbObj);

				// add columns to list
			$dblist->clearColumns();
			$cc = 0;
			foreach ($selectedFields as $field) {
				$fieldLabel = is_array($TCA[$table]['columns'][$field]) ? preg_replace('#:$#', '', $LANG->sL($TCA[$table]['columns'][$field]['label'])) : '['.$field.']';
				$dblist->addColumn($field, $fieldLabel);
				$cc++;
				if($cc == 1) {
						// add control at second column
					$dblist->addColumn('_CONTROL_', '');
					$cc++;
				}
			}

			$dblist->showActions = true;

				// Enable/disable display of thumbnails
			$dblist->showThumbs = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showThumb'];
				// Enable/disable display of AlternateBgColors
			$dblist->showAlternateBgColors = $this->pObj->MOD_SETTINGS['tx_dam_list_list_showAlternateBgColors'];

			#$dblist->showAlternateBgColors = $this->pObj->modTSconfig['properties']['alternateBgColors']?1:0;


			$dblist->setPointer($this->pObj->selection->pointer);
			$dblist->setCurrentSorting($this->pObj->MOD_SETTINGS['tx_dam_list_list_sortField'], $this->pObj->MOD_SETTINGS['tx_dam_list_list_sortRev']);
			$dblist->setParameterNames('SET[tx_dam_list_list_sortField]', 'SET[tx_dam_list_list_sortRev]');




// TODO ???				// It is set, if the clickmenu-layer is active AND the extended view is not enabled.
#			$dblist->dontShowClipControlPanels = $CLIENT['FORMSTYLE'] && !$BE_USER->uc['disableCMlayers'];




				// JavaScript
			$this->pObj->doc->JScodeArray['redirectUrls'] = $this->pObj->doc->redirectUrls(t3lib_div::getIndpEnv('REQUEST_URI'));
			$this->pObj->doc->JScodeArray['jumpExt'] = '
				function jumpExt(URL,anchor)	{
					var anc = anchor?anchor:"";
					document.location = URL+(T3_THIS_LOCATION?"&returnUrl="+T3_THIS_LOCATION:"")+anc;
				}
				';


			$content.= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post" name="dblistForm">';
			$content.= $dblist->getListTable();
			$content.= '<input type="hidden" name="cmd_table"><input type="hidden" name="cmd"></form>';


			$fieldSelectBoxContent = $this->fieldSelectBox($table, $this->makeAllFieldList($table), $selectedFields);
			$content.= $this->pObj->buttonToggleDisplay('fieldselector', $LANG->getLL('field_selector'), $fieldSelectBoxContent);


		}

		return $content;
	}




	/********************************
	 *
	 * internal
	 *
	 ********************************/


	/**
	 * Create the selector box for selecting fields to display from a table:
	 *
	 * @param	string		Table name
	 * @param	boolean		If true, form-fields will be wrapped around the table.
	 * @return	string		HTML table with the selector box (name: displayFields['.$table.'][])
	 */
	function fieldSelectBox($table, $allFields, $selectedFields, $formFields = 1) {
		global $TCA, $LANG;

		t3lib_div::loadTCA($table);

		$formElements = array('', '');
		if ($formFields) {
			$formElements = array('<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post">', '</form>');
		}


		// TODO ??
		// Add pseudo "control" fields
		#		$fields['_PATH_'] = '_PATH_';
		#		$fields['_LOCALIZATION_'] = '_LOCALIZATION_';
		#		$fields['_CONTROL_'] = '_CONTROL_';
		#		$fields['_CLIPBOARD_'] = '_CLIPBOARD_';

			// Create an option for each field:
		$opt = array();
		$opt[] = '<option value=""></option>';
		foreach ($allFields as $fN) {
				// Field label
			$fL = is_array($TCA[$table]['columns'][$fN]) ? ereg_replace(':$', '', $LANG->sL($TCA[$table]['columns'][$fN]['label'])) : '['.$fN.']';
			$opt[] = '
														<option value="'.$fN.'"'. (in_array($fN, $selectedFields) ? ' selected="selected"' : '').'>'.htmlspecialchars($fL).'</option>';
		}

			// Compile the options into a multiple selector box:
		$lMenu = '
												<select size="'.t3lib_div::intInRange(count($allFields) + 1, 3, 8).'" multiple="multiple" name="tx_dam_list_displayFields[]">'.implode('', $opt).'
												</select>
						';

			// Table with the select box:
		$content .= '
				'.$formElements[0].'
						<!--
							Field selector for extended table view:
						-->
						<table border="0" cellpadding="0" cellspacing="0" class="bgColor4" id="typo3-dblist-fieldSelect">
							<tr>
								<td>'.$lMenu.'</td>
								<td><input type="Submit" name="search" value="&gt;&gt;"></td>
							</tr>
							</table>
					'.$formElements[1].'
				';
		return $content;
	}
	/**
	 * Makes the list of fields the user can select/view for a table
	 *
	 * @param	string		Table name
	 * @param	boolean		If set, users access to the field (non-exclude-fields) is NOT checked.
	 * @param	boolean		$useExludeFieldList: ...
	 * @return	array		Array, where values are fieldnames to include in query
	 */
	function makeAllFieldList($table, $dontCheckUser = false, $useExludeFieldList = true) {
		global $TCA, $BE_USER;

			// Init fieldlist array:
		$fieldListArr = array();

			// Check table:
		if (is_array($TCA[$table])) {
			t3lib_div::loadTCA($table);

			$exludeFieldList = t3lib_div::trimExplode(',', $TCA[$table]['interface']['excludeFieldList'],1);

				// Traverse configured columns and add them to field array, if available for user.
			foreach ($TCA[$table]['columns'] as $fN => $fieldValue) {
				if (($dontCheckUser || ((!$fieldValue['exclude'] || $BE_USER->check('non_exclude_fields', $table.':'.$fN)) && $fieldValue['config']['type'] != 'passthrough')) AND (!$useExludeFieldList || !in_array($fN, $exludeFieldList))) {
					$fieldListArr[$fN] = $fN;
				}
			}

				// Add special fields:
			if ($dontCheckUser || $BE_USER->isAdmin()) {
				$fieldListArr['uid'] = 'uid';
				$fieldListArr['pid'] = 'pid';
				if ($TCA[$table]['ctrl']['tstamp'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['crdate'])
					$fieldListArr[$TCA[$table]['ctrl']['tstamp']] = $TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['cruser_id'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['cruser_id'];
				if ($TCA[$table]['ctrl']['sortby'])
					$fieldListArr[$TCA[$table]['ctrl']['cruser_id']] = $TCA[$table]['ctrl']['sortby'];
				if ($TCA[$table]['ctrl']['versioning'])
					$fieldListArr['t3ver_id'] = 't3ver_id';
			}
		}
			// doesn't make sense, does it?
		unset ($fieldListArr['l18n_parent']);
		unset ($fieldListArr['l18n_diffsource']);

		return $fieldListArr;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php']);
}

?>