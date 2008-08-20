<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

require_once (PATH_t3lib.'interfaces/interface.t3lib_browselinkshook.php');
require_once(t3lib_extMgm::extPath('dam').'class.tx_dam_browse_media.php');

/**
 * Implementation of the t3lib_browselinkshook interface for DAM to hook on tx_rtehtmlarea_browse_links
 *
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @package 	DAM
 */
class tx_dam_rtehtmlarea_browse_links implements t3lib_browseLinksHook {
	
	protected $invokingObject;
	protected $mode;
	protected $act;
	protected $bparams;
	protected $isHtmlAreaRTE;
	protected $isEnabled;
	protected $browserRenderObj; // DAM browser object
	
	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	parent browse_links object
	 * @param	array		additional parameters
	 * @return	void
	 */
	public function init($parentObject, $additionalParameters) {
		$this->invokingObject =& $parentObject;
		$this->mode =& $this->invokingObject->mode;
		$this->act =& $this->invokingObject->act;
		$this->bparams =& $this->invokingObject->bparams;
		$invokingObjectClass = get_class($this->invokingObject);
		$this->isHtmlAreaRTE = ($invokingObjectClass == 'tx_rtehtmlarea_browse_links' || $invokingObjectClass == 'ux_tx_rtehtmlarea_browse_links');
		$this->isEnabled = ((string)$this->mode == 'rte') && $this->isHtmlAreaRTE;
		if ($this->isEnabled) {
			$this->invokingObject->anchorTypes[] = 'media';
		}
	}
	
	/**
	 * Adds new items to the currently allowed ones and returns them
	 * Replaces the 'file' item with the 'media' item
	 *
	 * @param	array	currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	public function addAllowedItems($currentlyAllowedItems) {
		$allowedItems =& $currentlyAllowedItems;
		if ($this->isEnabled) {
			foreach ($currentlyAllowedItems as $key => $item) {
				if ($item == 'file') {
					$allowedItems[$key] = 'media';
					break;
				}
			}
		}
		return $allowedItems;
	}
	
	/**
	 * Modifies the menu definition and returns it
	 * Adds definition of the 'media' menu item
	 *
	 * @param	array	menu definition
	 * @return	array	modified menu definition
	 */
	public function modifyMenuDefinition($menuDefinition) {
		global $LANG;
		$menuDef =& $menuDefinition;
		if ($this->isEnabled && in_array('media', $this->invokingObject->allowedItems)) {
			$menuDef['media']['isActive'] = $this->invokingObject->act == 'media';
			$menuDef['media']['label'] =  $LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_tabs_tab',1);
			$menuDef['media']['url'] = '#';
			$menuDef['media']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars('?act=media&editorNo='.$this->invokingObject->editorNo.'&contentTypo3Language='.$this->invokingObject->contentTypo3Language.'&contentTypo3Charset='.$this->invokingObject->contentTypo3Charset).'\');return false;"';
		}
		return $menuDef;
	}
	
	/**
	 * Returns a new tab for the browse links wizard
	 * Returns the 'media' tab to the RTE link browser
	 *
	 * @param	string		current link selector action
	 * @return	string		a tab for the selected link action
	 */
	public function getTab($linkSelectorAction) {
		$content = '';
		if ($this->isEnabled && $linkSelectorAction == 'media') {
			$content .= $this->invokingObject->addAttributesForm();
			$this->initMediaBrowser();
			$content .= $this->browserRenderObj->part_rte_linkfile();
			$this->addDAMStylesAndJSArrays();
		}
		return $content;
	}
	
	/**
	 * Checks the current URL and determines what to do
	 * If the link was determined to be a file link, then set the action to 'media'
	 *
	 * @param	unknown_type		$href
	 * @param	unknown_type		$siteUrl
	 * @param	unknown_type		$info
	 * @return	unknown_type
	 */
	public function parseCurrentUrl($href, $siteUrl, $info) {
		if ($this->isEnabled && $info['act'] == 'file') {
			$info['act'] = 'media';
		}
		return $info;
	}

	protected function initMediaBrowser() {
		$this->browserRenderObj = t3lib_div::makeInstance('tx_dam_browse_media');
		$this->browserRenderObj->pObj =& $this->invokingObject;
		$this->invokingObject->browser =& $this->browserRenderObj;
			// init class browse_links
		$this->browserRenderObj->init();
		$this->browserRenderObj->mode =& $this->mode;
		$this->browserRenderObj->act =& $this->act;
		$this->browserRenderObj->bparams =& $this->bparams;
			// init the DAM object
		$this->browserRenderObj->initDAM();
			// processes MOD_SETTINGS
		$this->browserRenderObj->getModSettings();
			// Processes bparams parameter
		$this->browserRenderObj->processParams();
			// init the DAM selection after we've got the params
		$this->browserRenderObj->initDAMSelection();
	}

	protected function addDAMStylesAndJSArrays() {
		$this->invokingObject->doc->inDocStylesArray = array_merge($this->invokingObject->doc->inDocStylesArray, $this->browserRenderObj->doc->inDocStylesArray);
		$this->invokingObject->doc->JScodeArray = array_merge($this->invokingObject->doc->JScodeArray, $this->browserRenderObj->doc->JScodeArray);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_links.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_links.php']);
}
?>