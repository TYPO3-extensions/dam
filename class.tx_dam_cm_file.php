<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2006 Rene Fritz (r.fritz@colorcube.de)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_dam_cm_file
 *   48:     function main(&$backRef, $menuItems, $file, $uid)
 *  120:     function createOnClick($url, $dontHide=false)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');

/**
 * Creates the whole (!) context menu for files (tx_dam records)
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_cm_file {

	function main(&$backRef, $menuItems, $file, $uid)	{
		global $BE_USER, $TCA, $LANG;


			// Returns directly, because the clicked item was not a file
		if ($backRef->cmLevel==0 AND $uid!='')	return $menuItems;
			// Returns directly, because the clicked item was not the second level menu from DAM records
		if ($backRef->cmLevel==1 AND t3lib_div::_GP('subname')!='tx_dam_cm_file')	return $menuItems;

		$this->backRef = &$backRef;

			// this is second level menu from DAM records
		$fileDAM = t3lib_div::_GP('txdamFile');
		$file = $fileDAM ? $fileDAM : $file;

		if (@is_file($file)) {
			$item = tx_dam::file_compileInfo($file);
		} elseif (@is_dir($file)) {
			$item = tx_dam::path_compileInfo($file);
		} else {
			return $menuItems;
		}

			// just clear the whole menu
		$menuItems = array();

// TODO perms
		$permsEdit = 1;
		$permsDelete = 1;

		$actionCall = t3lib_div::makeInstance('tx_dam_actionCall');
		$actionCall->setRequest('context', $item);
		$actionCall->setEnv('returnUrl', t3lib_div::_GP('returnUrl'));
		$actionCall->setEnv('backPath', $backRef->PH_backPath);
		$actionCall->setEnv('defaultCmdScript', PATH_txdam_rel.'mod_cmd/index.php');
#		$actionCall->setEnv('calcPerms', $calcPerms);
		$actionCall->setEnv('permsEdit', $permsEdit);
		$actionCall->setEnv('permsDelete', $permsDelete);
		$actionCall->setEnv('cmLevel', $backRef->cmLevel);
		$actionCall->setEnv('cmParent', t3lib_div::_GP('parentname'));
		$actionCall->initActions(true);

	// TODO set allow deny: $backRef->disabledItems

		$actions = $actionCall->renderActionsContextMenu(true);
		foreach ($actions as $id => $action) {
				if ($action['isDivider']) {
				$menuItems[$id] = 'spacer';
			} else {
				$onclick = $action['onclick'] ? $action['onclick'] : $this->createOnClick($action['url'], $action['dontHide']);

                $menuItems[$id] = $backRef->linkItem(
	                    $GLOBALS['LANG']->makeEntities($action['label']),
                    	$backRef->excludeIcon($action['icon']),
	                    $onclick,
	                    $action['onlyCM'],
	                    $action['dontHide']
                );
			}
		}

		return $menuItems;
	}


	/**
	 * create onclick stuff for an url
	 *
	 * @param	string		Script (eg. file_edit.php) relative to typo3/
	 * @param	boolean		If set, the clickmenu layer will not hide itself onclick - used for secondary menus to appear...
	 * @return	string		onclick stuff
	 */
	function createOnClick($url, $dontHide=false)	{

		if (!strpos($url, '?')) {
			$url .= '?';
		}

		$loc='top.content'.($this->backRef->listFrame && !$this->backRef->alwaysContentFrame ?'.list_frame':'');
		$editOnClick='if('.$loc.'){'.$loc.".location.href=top.TS.PATH_typo3+'".$url."&returnUrl='+top.rawurlencode(".$this->backRef->frameLocation($loc.'.document').');'.($dontHide?'':' hideCM();').'};';

		return $editOnClick.'return false;';
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_cm_file.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_cm_file.php']);
}

?>