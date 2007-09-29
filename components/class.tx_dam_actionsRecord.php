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
 *  115: class tx_dam_action_recordBase extends tx_dam_actionbase
 *  140:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  156:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *
 *
 *  180: class tx_dam_action_editRec extends tx_dam_action_recordBase
 *  200:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  216:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  237:     function getIcon ($addAttribute='')
 *  256:     function getDescription ()
 *  267:     function _getCommand()
 *
 *
 *  288: class tx_dam_action_viewFileRec extends tx_dam_actionbase
 *  310:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  327:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  349:     function getIcon ($addAttribute='')
 *  365:     function getDescription ()
 *  378:     function _getCommand()
 *
 *
 *  399: class tx_dam_action_infoRec extends tx_dam_action_recordBase
 *  417:     function getIcon ($addAttribute='')
 *  436:     function getDescription ()
 *  447:     function _getCommand()
 *
 *
 *  472: class tx_dam_action_revertRec extends tx_dam_action_recordBase
 *  481:     function getIcon ($addAttribute='')
 *  500:     function getDescription ()
 *  511:     function _getCommand()
 *
 *
 *  530: class tx_dam_action_hideRec extends tx_dam_action_recordBase
 *  543:     function isPossiblyValid ($type, $itemInfo=NULL, $env=NULL)
 *  567:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  588:     function getIcon ($addAttribute='')
 *  612:     function getDescription ()
 *  631:     function _getCommand()
 *
 *
 *  657: class tx_dam_action_deleteRec extends tx_dam_action_recordBase
 *  667:     function isValid ($type, $itemInfo=NULL, $env=NULL)
 *  684:     function getIcon ($addAttribute='')
 *  703:     function getDescription ()
 *  714:     function _getCommand()
 *
 *
 *  735: class tx_dam_action_deleteQuickRec extends tx_dam_action_recordBase
 *  744:     function _getCommand()
 *
 *
 *  768: class tx_dam_action_lockWarningRec extends tx_dam_action_recordBase
 *  777:     function getIcon ($addAttribute='')
 *  793:     function getDescription ()
 *  804:     function _getCommand()
 *
 *
 *  813: class tx_dam_actionsRecord
 *
 * TOTAL FUNCTIONS: 31
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once (PATH_txdam.'lib/class.tx_dam_actionbase.php');



/**
 * Edit record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_recordBase extends tx_dam_actionbase {

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
			$valid = ($this->itemInfo['__type'] == 'record'); # AND ($this->itemInfo['__table'] == 'tx_dam');
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

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] == 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
			 	$valid = ($this->env['permsEdit'] AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']);
			}
		}
		return $valid;
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
class tx_dam_action_editRec extends tx_dam_action_recordBase {


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
			$valid = ($this->itemInfo['__type'] == 'record'); # AND ($this->itemInfo['__table'] == 'tx_dam');
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

		$valid = $this->isTypeValid ($type, $itemInfo, $env);
		if ($valid)	{
			$valid = (($this->itemInfo['__type'] == 'record') AND $this->itemInfo['__table']);
			if ($valid AND $this->editPermsNeeded) {
			 	$valid = ($this->env['permsEdit'] AND !$TCA[$this->itemInfo['__table']]['ctrl']['readOnly']);
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
class tx_dam_action_viewFileRec extends tx_dam_actionbase {

// see tx_dam_action_viewFile

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
			$valid = ($this->itemInfo['__type'] == 'record' AND $this->itemInfo['__table'] == 'tx_dam');
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
 * Info record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_infoRec extends tx_dam_action_recordBase {


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

		// what we want is to have info about the file
		// and while DAM registered an info viewer for file we use that instead of the record info view
		// $commands['onclick'] = 'top.launchView(\''.$this->itemInfo['__table'].'\', \''.$this->itemInfo['uid'].'\'); return false;';

		$filename = tx_dam::path_makeAbsolute($this->itemInfo['file_path']).$this->itemInfo['file_name'];
		$commands['onclick'] = 'top.launchView(\''.$filename.'\', \'\'); return false;';

		return $commands;
	}
}



/**
 * Revert record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_revertRec extends tx_dam_action_recordBase {

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
			$iconFile = 'gfx/history2_i.gif';
		} else {
			$iconFile = 'gfx/history2.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="13" height="12"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:history');
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$commands['href'] = '#';
		$commands['onclick'] = 'return jumpExt(\''.$GLOBALS['BACK_PATH'].'show_rechis.php?element='.rawurlencode($this->itemInfo['__table'].':'.$this->itemInfo['uid']).'\',\'#latest\');';

		return $commands;
	}
}



/**
 * Hide/Unhide record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_hideRec extends tx_dam_action_recordBase {

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
		global $TCA;

		if ($valid = parent::isPossiblyValid ($type, $itemInfo, $env)) {

			if ($valid AND $this->itemInfo['__table']) {
				$this->_hiddenField = $TCA[$this->itemInfo['__table']]['ctrl']['enablecolumns']['disabled'];
				if ($this->env['permsEdit'] && $this->_hiddenField && $TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]
					&& (!$TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields', $this->itemInfo['__table'].':'.$this->_hiddenField))) {
						$valid = true;
				}
			}
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

		$valid = parent::isValid ($type, $itemInfo, $env);
		if ($valid)	{
			if ($this->env['permsEdit'] && $this->_hiddenField && $TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]
				&& (!$TCA[$this->itemInfo['__table']]['columns'][$this->_hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields', $this->itemInfo['__table'].':'.$this->_hiddenField))) {
					$valid = true;
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
		global $TCA;

		$prefix = '';
		if ($this->itemInfo[$this->_hiddenField]) {
			$prefix = 'un';
		}

		if ($this->disabled) {
			$iconFile = 'gfx/button_'.$prefix.'hide_i.gif';
		} else {
			$iconFile = 'gfx/button_'.$prefix.'hide.gif';
		}
		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconFile, 'width="11" height="10"').$this->_cleanAttribute($addAttribute).' alt="" />';

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		$content = '';

		$prefix = 'hide';
		if ($this->itemInfo[$this->_hiddenField]) {
			$prefix = 'unHide';
		}
		$content = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_web_list.xml:'.$prefix.($this->itemInfo['__table'] == 'pages' ? 'Page' : ''));

		return $content;
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		if ($this->itemInfo[$this->_hiddenField]) {
			$params = '0';
		} else {
			$params = '1';
		}
		$params = '&data['.$this->itemInfo['__table'].']['.$this->itemInfo['uid'].']['.$this->_hiddenField.']='.$params;

		$commands['href'] = '#';
		$commands['onclick'] = 'return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\');';

		return $commands;
	}
}



/**
 * Delete record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteRec extends tx_dam_action_recordBase {

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

		$valid = parent::isValid ($type, $itemInfo, $env);
		if ($valid)	{
			 $valid = ($this->env['permsDelete']);
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


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$cmd = 'tx_dam_cmd_filedelete';
		$script = $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php?CMD='.$cmd.'&id='.rawurlencode($this->itemInfo['uid']).'&returnUrl='.$this->env['returnUrl'];

		$commands['href'] = $script;

		return $commands;
	}
}



/**
 * Delete without notification - record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_deleteQuickRec extends tx_dam_action_recordBase {


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {

		$params = '&cmd['.$this->itemInfo['__table'].']['.$this->itemInfo['uid'].'][delete]=1';
		$title = $this->itemInfo['title'].' ('.$this->itemInfo['file_name'].')';
//		$onClick = 'if (confirm('.$GLOBALS['LANG']->JScharCode(sprintf($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'), $title)).')) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\');} return false;';
		$onClick = 'jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params, -1).'\'); return false;';

		$commands['href'] = '#';
		$commands['onclick'] = $onClick;

		return $commands;
	}
}



/**
 * Record locking warning - record action
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Action
 * @see tx_dam_actionbase
 */
class tx_dam_action_lockWarningRec extends tx_dam_action_recordBase {

	/**
	 * Returns the icon image tag.
	 * Additional attributes to the image tagcan be added.
	 *
	 * @param	string		$addAttribute Additional attributes
	 * @return	string
	 */
	function getIcon ($addAttribute='') {
		global $TCA;

		if ($this->_lockInfo = t3lib_BEfunc::isRecordLocked($this->itemInfo['__table'], $this->itemInfo['uid'])) {
			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/recordlock_warning3.gif', 'width="17" height="12"').' title="'.htmlspecialchars($this->getDescription ()).'" alt="" />';
		}

		return $icon;
	}


	/**
	 * Returns a short description for tooltips for example like: Delete folder recursivley
	 *
	 * @return	string
	 */
	function getDescription () {
		return $this->_lockInfo['msg'] ? $this->_lockInfo['msg']: '';
	}


	/**
	 * Returns a command array for the current type
	 *
	 * @return	array		Command array
	 * @access private
	 */
	function _getCommand() {
		$commands['href'] = '#';
		$commands['onclick'] = 'alert('.$GLOBALS['LANG']->JScharCode($this->_lockInfo['msg']).');return false;';

		return $commands;
	}
}


class tx_dam_actionsRecord {
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsRecord.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_actionsRecord.php']);
}

?>