<?php

class ux_SC_show_item extends SC_show_item {
	/**
	 * Main function. Will generate the information to display for the item set internally.
	 *
	 * @param	string		<a> tag closing/returning.
	 * @return	void
	 */
	function renderFileInfo($returnLinkTag)	{
		global $LANG, $TCA, $BACK_PATH;



		if($meta=tx_dam::getMetaForFile($this->file)) {

			$row = $meta['fields'];

				// convert row data for tceforms
			$trData = t3lib_div::makeInstance('t3lib_transferData');
			$trData->lockRecords = false;
			$trData->disableRTE = true;
			$trData->renderRecord('tx_dam', $row['uid'], $row['pid'], $row);
			reset($trData->regTableItems_data);
			$row = current($trData->regTableItems_data);


				// create form
			require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
			$form = t3lib_div::makeInstance('tx_dam_simpleForms');
			$form->initDefaultBEmode();
			$form->enableTabMenu = TRUE;
			$form->setNewBEDesignOrig();

			$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');

			$form->removeRequired($TCA['tx_dam_simpleforms']);
			$form->setNonEditable($TCA['tx_dam_simpleforms'], $TCA['tx_dam']['txdamInterface']['info_fieldList_isNonEditable']);
			$columnsExclude = t3lib_div::trimExplode(',', $TCA['tx_dam']['txdamInterface']['info_fieldList_exclude'], 1);
			foreach ($columnsExclude as $column) {
				unset($TCA['tx_dam_simpleforms']['columns'][$column]);
			}

			$content.= $form->getMainFields('tx_dam_simpleforms', $row);

			$content = $form->wrapTotal($content, $meta['fields'] /* raw */, 'tx_dam');

			$form->removeVirtualTable('tx_dam_simpleforms');



				// reset content
			$this->content = '';

				// Initialize document template object:
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->docType = 'xhtml_trans';

			$this->doc->JScode = $this->doc->getDynTabMenuJScode();

			$this->doc->JScodeArray['changeWindowSize'] = 'self.resizeTo(520,450);';

				// Starting the page by creating page header stuff:
			$this->content.=$this->doc->startPage($LANG->sL('LLL:EXT:lang/locallang_core.php:show_item.php.viewItem'));
			$this->content.=$this->doc->spacer(5);


			$this->content.= $this->doc->section('',$content);



		} else {
			parent::renderFileInfo($returnLinkTag);
		}

	}

	/**
	 * Main function. Will generate the information to display for the item set internally.
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;

		if ($this->access)	{
			$returnLinkTag = t3lib_div::_GP('returnUrl') ? '<a href="'.t3lib_div::_GP('returnUrl').'" class="typo3-goBack">' : '<a href="#" onclick="window.close();">';



				// Branch out based on type:
			switch($this->type)	{
				case 'db':
					$this->renderDBInfo();
				break;
				case 'file':
					$this->renderFileInfo($returnLinkTag);
				break;
			}
				// If return Url is set, output link to go back:
			if (t3lib_div::_GP('returnUrl'))	{
				$this->content = $this->doc->section('',$returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.goBack',1).'</strong></a><br /><br />').$this->content;
			}

				// If return Url is set, output link to go back:
			if (t3lib_div::_GP('returnUrl'))	{
				$this->content.= $this->doc->section('','<br />'.$returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.goBack',1).'</strong></a>');
			}
		}
	}

	/**
	 * End page and print content
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

}

?>
