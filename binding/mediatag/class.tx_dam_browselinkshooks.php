<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  (c) 2011 Lorenz Ulrich <lorenz.ulrich@visol.ch>
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

require_once (PATH_t3lib . 'interfaces/interface.t3lib_browselinkshook.php');
require_once(t3lib_extMgm::extPath('dam') . 'class.tx_dam_browse_media.php');

/**
 * Implementation of the t3lib_browselinkshook interface for DAM to hook on link wizard
 *
 * @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
 * @author	Lorenz Ulrich <lorenz.ulrich@visol.ch>
 * @package 	DAM
 */
class tx_dam_browselinkshooks implements t3lib_browseLinksHook {

	protected $invokingObject;
	protected $mode;
	protected $act;
	protected $bparams;
	/** @var tx_dam_browse_media DAM browser object */
	protected $browserRenderObj;
	// Link title derived from the DAM database
	protected $damTitle = '';

	/**
	 * initializes the hook object
	 *
	 * @param	browse_links	$parentObject parent browse_links object
	 * @param	array		$additionalParameters additional parameters
	 * @return	void
	 */
	public function init($parentObject, $additionalParameters) {
		$invokingObjectClass = get_class($parentObject);
		$this->invokingObject =& $parentObject;
		$this->mode =& $this->invokingObject->mode;
		$this->act =& $this->invokingObject->act;
		$this->bparams =& $this->invokingObject->bparams;
		$this->invokingObject->anchorTypes[] = 'media';
		$GLOBALS['LANG']->includeLLFile('EXT:dam/compat/locallang.xml');
		//t3lib_utility_Debug::debug($this->invokingObject->act);
	}

	/**
	 * Adds new items to the currently allowed ones and returns them
	 * Replaces the 'file' item with the 'media' item
	 * Adds DAM upload tab
	 *
	 * @param	array	$currentlyAllowedItems currently allowed items
	 * @return	array	currently allowed items plus added items
	 */
	public function addAllowedItems($currentlyAllowedItems) {
		$allowedItems =& $currentlyAllowedItems;
		foreach ($currentlyAllowedItems as $key => $item) {
			if ($item == 'file') {
				$allowedItems[$key] = 'media';
				break;
			}
		}
		$this->initMediaBrowser();
		return $allowedItems;
	}

	/**
	 * Modifies the menu definition and returns it
	 * Adds definition of the 'media' menu item
	 *
	 * @param	array	$menuDefinition menu definition
	 * @return	array	modified menu definition
	 */
	public function modifyMenuDefinition($menuDefinition) {
		$menuDef =& $menuDefinition;
		$menuDef['media']['isActive'] = $this->invokingObject->act == 'media';
		$menuDef['media']['label'] =  $GLOBALS['LANG']->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_tabs_tab',1);
		$menuDef['media']['url'] = '#';
		$menuDef['media']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars('?act=media&editorNo='.$this->invokingObject->editorNo.'&contentTypo3Language='.$this->invokingObject->contentTypo3Language.'&contentTypo3Charset='.$this->invokingObject->contentTypo3Charset).'\');return false;"';
		return $menuDef;
	}

	/**
	 * Returns a new tab for the browse links wizard
	 * Returns the 'media' tab to the RTE link browser
	 *
	 * @param	string		$linkSelectorAction current link selector action
	 * @return	string		a tab for the selected link action
	 */
	public function getTab($linkSelectorAction) {

			// Only return content if the media tab was called.
		if ($linkSelectorAction !== 'media') {
			return FALSE;
		}

		$content = '';
		$this->initMediaBrowser();
		$content .= $this->browserRenderObj->part_rte_linkfile();
		$this->addDAMStylesAndJSArrays();
		return $content;
	}

	/**
	 * Checks the current URL and determines what to do
	 * If the link was determined to be a file link, then set the action to 'media'
	 *
	 * Unfortunately it is not possible display the current URL better than
	 * "Current Link: http://media:175"
	 * This is because class.browse_links.php calls $this->parseCurrentUrl again internally and offers no
	 * possibilities to define own parsing methods. Therefore the link to the DAM record is treated as external
	 * link. So we're only setting the right tab here.
	 *
	 * @param	string		$href
	 * @param	string		$siteUrl
	 * @param	array		$info
	 * @return	array
	 */
	public function parseCurrentUrl($href, $siteUrl, $info) {
		$info['act'] = 'media';
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
		$this->browserRenderObj->thisConfig =& $this->invokingObject->thisConfig;
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
		$this->invokingObject->doc->JScodeArray['mediatag-dam'] = $this->getAdditionalJSCode();
	}

	/**
	 * Get additional DAM-specific JS functions
	 *
	 * @return	string
	 */
	protected function getAdditionalJSCode() {
		$JScode = "
				function link_damRecord(id)	{	//
					updateValueInMainForm('media:' + id);
					close();
					return false;
				}
		";
		return $JScode;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_browselinkshooks.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_browselinkshooks.php']);
}
?>