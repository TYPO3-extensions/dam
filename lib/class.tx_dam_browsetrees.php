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
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_dam_browseTrees
 *   69:     function init($thisScript, $mode='browse', $folderOnly=false)
 *   91:     function initSelectionClasses($selectionClassesArr, $thisScript, $mode='browse')
 *  160:     function getTrees()
 *  182:     function getMountsForTreeClass($classKey, $treeName='')
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





/**
 * Main script class for the tree navigation frame.
 * This is used in the nav frame or the element browser.
 *
 * @author	@author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_browseTrees {

	/**
	 * initialize the browsable trees
	 *
	 * @param	string		script name to link to
	 * @param	boolean		Element browser mode
	 * @param	boolean		Shows the folder tree only
	 * @return	void
	 */
	function init($thisScript, $mode='browse', $folderOnly=false)	{
		global $BE_USER,$LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		tx_dam::config_init();

		if ($folderOnly OR t3lib_div::_GP('folderOnly')) {
			$selClasses = $TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamFolder'];
			$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'] = array();
			$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamFolder'] = $selClasses;
		}

		$this->initSelectionClasses($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'], $thisScript, $mode);
	}

	/**
	 * initialize the browsable trees
	 *
	 * @param	array		$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']
	 * @param	string		script name to link to
	 * @param	boolean		Element browser mode
	 * @return	void
	 */
	function initSelectionClasses($selectionClassesArr, $thisScript, $mode='browse')	{
		global $BE_USER,$LANG,$BACK_PATH;

			// configuration - default
		$configDefault = tx_dam::config_getValue('setup.selections.default');
		$configDefault = $configDefault['properties'];

		if (is_array($selectionClassesArr))	{
			foreach($selectionClassesArr as $classKey => $classRef)	{

					// configuration - class
				$config = tx_dam::config_getValue('setup.selections.'.$classKey);
				$config = $config['properties'];
				if(intval($config['disable'])) {
					continue;
				}

				if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
					if (!$obj->isPureSelectionClass)	{
						if ($obj->isTreeViewClass)	{
								// object is a treeview class itself
							$this->treeObjArr[$classKey] = &$obj;
							$this->treeObjArr[$classKey]->init();

						} else {
								// object does not include treeview functionality. Therefore the standard browsetree is used with setup from the object
							$this->treeObjArr[$classKey] = &t3lib_div::makeInstance('tx_dam_browseTree');
							$this->treeObjArr[$classKey]->init();

							$this->treeObjArr[$classKey]->title = $obj->getTreeTitle();
							$this->treeObjArr[$classKey]->treeName = $obj->getTreeName();
							$this->treeObjArr[$classKey]->domIdPrefix = $obj->domIdPrefix ? $obj->domIdPrefix : $obj->getTreeName();
							$this->treeObjArr[$classKey]->rootIcon = PATH_txdam_rel.'i/cat2folder.gif';
							$this->treeObjArr[$classKey]->iconName = basename($obj->getDefaultIcon());
							$this->treeObjArr[$classKey]->iconPath = dirname($obj->getDefaultIcon()).'/';
								// workaround: Only variables can be passed by reference
							$this->treeObjArr[$classKey]->_data = $obj->getTreeArray();
							$this->treeObjArr[$classKey]->setDataFromArray($this->treeObjArr[$classKey]->_data);

						}

						$this->treeObjArr[$classKey]->thisScript = $thisScript;
						$this->treeObjArr[$classKey]->BE_USER = $BE_USER;
						$this->treeObjArr[$classKey]->mode = $mode;
						if(!($config['disableModeSelIcons']=='0') AND ($configDefault['disableModeSelIcons'] OR $config['disableModeSelIcons'])) {
							$this->treeObjArr[$classKey]->modeSelIcons = false;
						}
						$this->treeObjArr[$classKey]->ext_IconMode = '1'; // no context menu on icons
					}

					if ($this->treeObjArr[$classKey]->supportMounts) {
						$mounts = $this->getMountsForTreeClass($classKey, $this->treeObjArr[$classKey]->getTreeName());
						if (count($mounts)) {
							$this->treeObjArr[$classKey]->setMounts($mounts);
						} else {
							unset($this->treeObjArr[$classKey]);
						}
					}
				}
			}
		}

	}


	/**
	 * rendering the browsable trees
	 *
	 * @return	string		tree HTML content
	 */
	function getTrees()	{
		global $LANG,$BACK_PATH;

		$tree = '';

		if (is_array($this->treeObjArr)) {
			foreach($this->treeObjArr as $treeName => $treeObj)	{
				$tree .= $treeObj->getBrowsableTree();
			}
		}

		return $tree;
	}


	/**
	 * Returns the mounts for the selection classes
	 *
	 * @param	string		$classKey: ...
	 * @param	string		$treeName: ...
	 * @return	array
	 */
	function getMountsForTreeClass($classKey, $treeName='') {
		global $BE_USER, $TYPO3_CONF_VARS;

		if(!$treeName) {
			if (is_object($obj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'][$classKey])))	{
				$treeName = $obj->getTreeName();
			}
		}

		$mounts = array();

		if($GLOBALS['BE_USER']->user['admin']){
			$mounts = array(0 => 0);
			return $mounts;
		}

		if ($GLOBALS['BE_USER']->user['tx_dam_mountpoints']) {
			 $values = explode(',',$GLOBALS['BE_USER']->user['tx_dam_mountpoints']);
			 foreach($values as $mount) {
			 	list($k,$id) = explode(':', $mount);
			 	if ($k == $treeName) {
					$mounts[$id] = $id;
			 	}
			 }
		}

		if(is_array($GLOBALS['BE_USER']->userGroups)){
			foreach($GLOBALS['BE_USER']->userGroups as $group){
				if ($group['tx_dam_mountpoints']) {
					$values = explode(',',$group['tx_dam_mountpoints']);
					 foreach($values as $mount) {
					 	list($k,$id) = explode(':', $mount);
					 	if ($k == $treeName) {
							$mounts[$id] = $id;
					 	}
					 }
				}
			}
		}

			// if root is mount just set it and remove all other mounts
		if(isset($mounts[0])) {
			$mounts = array(0 => 0);
		}

		return $mounts;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_browsetrees.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_browsetrees.php']);
}


?>