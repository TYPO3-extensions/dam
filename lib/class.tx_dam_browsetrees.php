<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2005 René Fritz (r.fritz@colorcube.de)
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
 *   59: class tx_dam_browseTrees 
 *   68:     function init($thisScript, $mode)	
 *  124:     function getTrees()	
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_stdselection.php');



/**
 * Main script class for the tree navigation frame.
 * This is used in the nav frame or the element browser.
 * 
 * @author	@author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_browseTrees {

	/**
	 * initialize the browsable trees
	 * 
	 * @param	string		script name to link to
	 * @param	boolean		Element browser mode
	 * @return	void		
	 */
	function init($thisScript, $mode='browse')	{
		global $BE_USER,$LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		if (t3lib_div::_GP('folderOnly')) {
			$selClasses = $TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamFolder'];
			$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'] = array();
			$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamFolder'] = $selClasses;
		}

			// move media types to the end
		if(isset($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamMedia'])) {
			$selClasses = $TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamMedia'];
			unset($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamMedia']);
			$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamMedia'] = $selClasses;
		}

# debug($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']);

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

		if (is_array($selectionClassesArr))	{
			foreach($selectionClassesArr as $classKey => $classRef)	{

				if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
					if (!$obj->isPureSelectionClass)	{
						if ($obj->isTreeViewClass)	{
								// object is a treeview class itself or just no tree class
							$this->arrayTree[$classKey] = &$obj;
							$this->arrayTree[$classKey]->init();

						} else {
								// object does not include treeview functionality. Therefore the standard browsetree is used with setup from the object
							$this->arrayTree[$classKey] = &t3lib_div::makeInstance('tx_dam_browseTree');
							$this->arrayTree[$classKey]->init();

							$this->arrayTree[$classKey]->title = $obj->dam_treeTitle();
							$this->arrayTree[$classKey]->treeName = $obj->dam_treeName();
							$this->arrayTree[$classKey]->iconName = basename($obj->dam_defaultIcon());
							$this->arrayTree[$classKey]->iconPath = dirname($obj->dam_defaultIcon()).'/';

							$this->arrayTree[$classKey]->setDataFromArray($obj->getTreeArray());

						}

						$this->arrayTree[$classKey]->thisScript = $thisScript;
						$this->arrayTree[$classKey]->BE_USER = $BE_USER;
						$this->arrayTree[$classKey]->mode = $mode;
						$this->arrayTree[$classKey]->ext_IconMode = '1'; // no context menu on icons
					}

					if ($this->arrayTree[$classKey]->supportMounts) {
						$mounts = $this->getMountsForTreeClass($classKey, $this->arrayTree[$classKey]->dam_treeName());
						if (count($mounts)) {
							$this->arrayTree[$classKey]->setMounts($mounts);
						} else {
							unset($this->arrayTree[$classKey]);
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

		if (is_array($this->arrayTree)) {
			foreach($this->arrayTree as $treeName => $treeObj)	{
				$tree .= $treeObj->getBrowsableTree();
			}
		}

		return $tree;
	}






	function getMountsForTreeClass($classKey, $treeName='') {
		global $BE_USER, $TYPO3_CONF_VARS;

		if(!$treeName) {
			if (is_object($obj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'][$classKey])))	{
				$treeName = $obj->dam_treeName();
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