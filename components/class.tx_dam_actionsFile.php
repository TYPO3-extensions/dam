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
 * @package DAM-Component
 * @subpackage Action
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  119: class tx_dam_action_newTextfile extends tx_dam_actionbase
 *  137:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  152:     function getIcon ($addAttribute='')
 *  169:     function getLabel ()
 *  178:     function getDescription ()
 *  188:     function _getCommand()
 *
 *
 *  213: class tx_dam_action_editFileRecord extends tx_dam_actionbase
 *  238:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  254:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  282:     function getIcon ($addAttribute='')
 *  301:     function getDescription ()
 *  312:     function _getCommand()
 *
 *
 *  334: class tx_dam_action_viewFile extends tx_dam_actionbase
 *  356:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  373:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  395:     function getIcon ($addAttribute='')
 *  411:     function getDescription ()
 *  424:     function _getCommand()
 *
 *
 *  448: class tx_dam_action_infoFile extends tx_dam_action_viewFile
 *  466:     function getIcon ($addAttribute='')
 *  485:     function getDescription ()
 *  496:     function _getCommand()
 *
 *
 *  517: class tx_dam_action_renameFile extends tx_dam_actionbase
 *  539:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  555:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  571:     function getIcon ($addAttribute='')
 *  589:     function getDescription ()
 *  600:     function _getCommand()
 *
 *
 *  632: class tx_dam_action_editFile extends tx_dam_action_renameFile
 *  651:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  669:     function getIcon ($addAttribute='')
 *  687:     function getDescription ()
 *
 *
 *  707: class tx_dam_action_replaceFile extends tx_dam_action_renameFile
 *  725:     function getIcon ($addAttribute='')
 *  743:     function getDescription ()
 *
 *
 *  763: class tx_dam_action_deleteFile extends tx_dam_action_renameFile
 *  781:     function getIcon ($addAttribute='')
 *  799:     function getDescription ()
 *
 *
 *  817: class tx_dam_action_deleteFileQuick extends tx_dam_action_renameFile
 *  839:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  853:     function getIcon ($addAttribute='')
 *  871:     function getDescription ()
 *  882:     function _getCommand()
 *
 *
 *  897: class tx_dam_actionsFile
 *
 * TOTAL FUNCTIONS: 34
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');




/**
 * New file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_newTextfile extends tx_dam_actionbase {

	var $cmd = 'tx_dam_cmd_filenew';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'button');

	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid) {
			$valid = ($this->itemInfo['__type'] == 'dir');
		}
		return $valid;
	}

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = 'gfx/new_file_i.gif';
		} else {
			$iconFile = 'gfx/new_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}

	/**
	 * Returns the short label like: Delete
	 *
	 * @return	string
	 */
	function getLabel () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:file');
	}

	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:newTextFile');
	}

	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$path = $this->itemInfo['dir_path_relative'];

		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&folder='.rawurlencode($path);
		$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);

		$commands['href'] = $script;

		return $commands;
	}
}



/**
 * Edit record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_editFileRecord extends tx_dam_actionbase {

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');

	/**
	 * If set the action is for itmes with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = true;

	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] == 'file');
		}
		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		global $TCA;

		$valid = ($this->isTypeValid ($type, $itemInfo, $env) AND $this->itemInfo['uid']);
		if ($valid AND $this->editPermsNeeded) {

			if (!isset($this->env['permsEdit'])) {
				$this->env['permsEdit'] = ($GLOBALS['SOBE']->calcPerms & 16);
			}
			if (!isset($this->itemInfo['__table'])) {
				$this->itemInfo['__table'] = 'tx_dam';
			}
		 	$valid = ($this->env['permsEdit'] AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']);

		}
		return $valid;
	}




	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = 'gfx/edit2_i.gif';
		} else {
			$iconFile = 'gfx/edit2'. (!$TCA[$this->itemInfo['__table']]['ctrl']['readOnly'] ? '' : '_d').'.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="11" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:edit');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$params = '&edit['.$this->itemInfo['__table'].']['.$this->itemInfo['uid'].']=edit';
		$onClick = t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], -1);

		$commands['href'] = '#';
		$commands['onclick'] = $onClick;

		return $commands;
	}
}



/**
 * View file (popup)
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_viewFile extends tx_dam_actionbase {

// see tx_dam_action_viewFileRec

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] == 'file');
		}

		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = ($this->isTypeValid ($type, $itemInfo, $env) AND ($itemInfo['file_status'] != TXDAM_status_file_missing));
		if ($valid) {
// more simpler access check is needed
			if ($this->itemInfo['file_path_absolute'])	{
				$valid = (t3lib_div::isFirstPartOfStr($this->itemInfo['file_path_absolute'], PATH_site));
			} else 	{
				$this->itemInfo['file_path_absolute'] = tx_dam::path_makeAbsolute ($this->itemInfo['file_path']);
				$valid = (t3lib_div::isFirstPartOfStr($this->itemInfo['file_path_absolute'], PATH_site));
			}
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		if ($this->disabled) {
			$iconFile = 'gfx/zoom_i.gif';
		} else {
			$iconFile = 'gfx/zoom.gif';
		}
		$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';
		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:viewFile');
	}




	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$href = tx_dam::file_relativeSitePath ($this->itemInfo['file_path_absolute'].$this->itemInfo['file_name']);
		$aOnClick = "return top.openUrlInWindow('".t3lib_div::getIndpEnv('TYPO3_SITE_URL').$href."','WebFile');";

		$commands['href'] = '#';
		$commands['aTagAttribute'] = 'onclick="'.htmlspecialchars($aOnClick).'"';

		return $commands;
	}

}




/**
 * Info file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_infoFile extends tx_dam_action_viewFile {


	/**
	 * If set the action is for itmes with edit permissions only
	 * @access private
	 */
	var $editPermsNeeded = false;



	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->disabled) {
			$iconFile = 'gfx/zoom2_i.gif';
		} else {
			$iconFile = 'gfx/zoom2.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:showInfo');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$commands['href'] = '#';

		$filename = $this->itemInfo['file_path_absolute'].$this->itemInfo['file_name'];
		$commands['onclick'] = 'top.launchView(\''.$filename.'\', \'\'); return false;';

		return $commands;
	}
}



/**
 * Rename file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_renameFile extends tx_dam_actionbase {

	var $cmd = 'tx_dam_cmd_filerename';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] == 'file');
		}
		return $valid;
	}


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] == 'file') AND ($itemInfo['file_status'] != TXDAM_status_file_missing));
		}
		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/rename_file_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/rename_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.rename');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$filepath = $this->itemInfo['file_path_absolute'].$this->itemInfo['file_name'];

		$script = $this->env['defaultCmdScript'];
		$script .= '?CMD='.$this->cmd;
		$script .= '&file='.rawurlencode($filepath);
		$script .= '&returnUrl='.rawurlencode($this->env['returnUrl']);

		$commands['href'] = $script;

		return $commands;
	}

}








/**
 * Edit file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_editFile extends tx_dam_action_renameFile {

	var $cmd = 'tx_dam_cmd_fileedit';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns true if the action is of the wanted type
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isValid ($type, $itemInfo=NULL, $env=NULL) {
		$valid = $this->isTypeValid ($type, $itemInfo, $env);

		if ($valid) {
			$valid = ($this->itemInfo['__type'] == 'file' AND t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'], $this->itemInfo['file_type']));
		}

		return $valid;
	}


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = 'gfx/edit_file_i.gif';
		} else {
			$iconFile = 'gfx/edit_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:editFile');
	}

}







/**
 * Replace file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_replaceFile extends tx_dam_action_renameFile {

	var $cmd = 'tx_dam_cmd_filereplace';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = PATH_txdam_rel.'i/replace_file_i.gif';
		} else {
			$iconFile = PATH_txdam_rel.'i/replace_file.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="15" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:dam/lib/locallang.xml:replaceFile');
	}

}







/**
 * Delete file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteFile extends tx_dam_action_renameFile {

	var $cmd = 'tx_dam_cmd_filedelete';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = 'gfx/delete_record_i.gif';
		} else {
			$iconFile = 'gfx/delete_record.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.delete');
	}
}






/**
 * Quick-Delete file action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteFileQuick extends tx_dam_action_renameFile {

	var $cmd = 'tx_dam_cmd_filedelete';

	/**
	 * Defines the types that the object can render
	 * @var array
	 */
	var $typesAvailable = array('icon', 'control');


	/**
	 * Returns true if the action is of the wanted type
	 * This method should return true if the action is possibly true.
	 * This could be the case when a control is wanted for a list of files and in beforhand a check should be done which controls might be work.
	 * In a second step each file is checked with isValid().
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	array		$env Environment array. Can be set with setEnv() too.
	 * @return	boolean
	 */
	function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL) {
		if ($valid = $this->isTypeValid ($type, $itemInfo, $env)) {
			$valid = ($this->itemInfo['__type'] == 'file');
		}
		return $valid;
	}

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {

		if ($this->disabled) {
			$iconFile = 'gfx/delete_record_i.gif';
		} else {
			$iconFile = 'gfx/delete_record.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="12" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
// TODO what about unindexed files without uid
		$msg = $GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'), htmlspecialchars($this->itemInfo['file_name'])));
		$params = '&cmd[tx_dam]['.$this->itemInfo['uid'].'][delete]=1';
		$aOnClick = 'if (confirm('.$msg.')) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');} return false;';

		$commands['href'] = '#';
		$commands['aTagAttribute'] = 'onclick="'.htmlspecialchars($aOnClick).'"';


		return $commands;
	}
}


class tx_dam_actionsFile {
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsFile.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsFile.php']);
}

?>