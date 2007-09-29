<?php
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   17: class tx_dam_show_item
 *   29:     function isValid($type, &$pObj)
 *   46:     function render($type, &$pObj)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


class tx_dam_show_item {

	var $meta;


	/**
	 * Check if this object should render
	 *
	 * @param	string		Type: "file"
	 * @param	object		Parent object.
	 * @return	boolean
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

		if($type=='file' && is_array($this->meta = tx_dam::meta_getDataForFile($pObj->file, '*'))) {
			$isValid = true;
		}
		return $isValid;
	}


	/**
	 * Rendering
	 *
	 * @param	string		Type: "file"
	 * @param	object		Parent object.
	 * @return	string		Rendered content
	 */
	function render($type, &$pObj)	{
		global $LANG, $TCA, $BACK_PATH;

		$contentForm = '';

		$row = $this->meta;

			// convert row data for tceforms
		$trData = t3lib_div::makeInstance('t3lib_transferData');
		$trData->lockRecords = false;
		$trData->disableRTE = true;
		$trData->renderRecord('tx_dam', $row['uid'], $row['pid'], $row);
		reset($trData->regTableItems_data);
		$row = current($trData->regTableItems_data);
		$row['uid'] = $this->meta['uid'];
		$row['pid'] = $this->meta['pid'];


			// create form
		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->enableTabMenu = TRUE;
		$form->setNewBEDesignOrig();

		$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');

		$form->removeRequired($TCA['tx_dam_simpleforms']);
		$form->setNonEditable($TCA['tx_dam_simpleforms'], $TCA['tx_dam']['txdamInterface']['info_displayFields_isNonEditable']);
		$columnsExclude = t3lib_div::trimExplode(',', $TCA['tx_dam']['txdamInterface']['info_displayFields_exclude'], 1);
		foreach ($columnsExclude as $column) {
			unset($TCA['tx_dam_simpleforms']['columns'][$column]);
		}

		$contentForm.= $form->getMainFields('tx_dam_simpleforms', $row);

		$contentForm = $form->wrapTotal($contentForm, $this->meta /* raw */, 'tx_dam');

		$form->removeVirtualTable('tx_dam_simpleforms');



		$content = '';

			// Initialize document template object:
		$pObj->content = '';
		$pObj->doc = t3lib_div::makeInstance('mediumDoc');
		$pObj->doc->backPath = $BACK_PATH;
		$pObj->doc->docType = 'xhtml_trans';

		$pObj->doc->JScode = $pObj->doc->getDynTabMenuJScode();

		$pObj->doc->JScodeArray['changeWindowSize'] = 'self.resizeTo(520,490);';

			// Starting the page by creating page header stuff:
		$content.= $pObj->doc->startPage($LANG->sL('LLL:EXT:lang/locallang_core.xml:show_item.php.viewItem'));
		$content.= $pObj->doc->spacer(5);


		$content.= $pObj->doc->section('',$contentForm);

		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_show_item.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_show_item.php']);
}
?>