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
 * Command
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]

 *
 */



unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');

$LANG->includeLLFile('EXT:dam/mod_cmd/locallang.php');


// Module is available to everybody
// $BE_USER->modAccess($MCONF,1);



require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_filelist.php');



/**
 * Script class for the DAM command script
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_cmd extends tx_dam_SCbase {

	/**
	 * the action for the form tag
	 */
	var $actionTarget = '';

	/**
	 * the page title
	 */
	var $pageTitle = '[no title]';

	/**
	 * t3lib_basicFileFunctions object
	 */
	var $basicFF;



	/**
	 * Initializes the backend module
	 * 
	 * @return	void		
	 */
	function init()	{
		global $BE_USER, $SOBE, $TYPO3_CONF_VARS, $FILEMOUNTS;

#TODO
		$this->vC = t3lib_div::_GP('vC');

			// Checking referer / executing
		$refInfo=parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
		$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
		if ($httpHost!=$refInfo['host'] && $this->vC!=$BE_USER->veriCode() && !$TYPO3_CONF_VARS['SYS']['doNotCheckReferer'])	{
			t3lib_BEfunc::typo3PrintError ('Access Error','Referer did not match and veriCode was not valid either!','');
			exit;
		}



		parent::init();


#TODO			// Initialize GPvars:
		$this->data = t3lib_div::_GP('data');
		$this->returnUrl = t3lib_div::_GP('returnUrl');
		$this->returnUrl = $this->returnUrl ? $this->returnUrl : t3lib_div::getIndpEnv('HTTP_REFERER');


		$this->redirect = t3lib_div::_GP('redirect');
		$this->redirect = $this->redirect ? $this->redirect : $this->returnUrl;

		//
		// Init basic-file-functions object:
		//

		$this->basicFF = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->basicFF->init($FILEMOUNTS,$TYPO3_CONF_VARS['BE']['fileExtensions']);

	}



	/**
	 * Loads $this->extClassConf with the configuration for the CURRENT function of the menu.
	 * If for this array the key 'path' is set then that is expected to be an absolute path to a file which should be included - so it is set in the internal array $this->include_once
	 *
	 * @param	string		The key to MOD_MENU for which to fetch configuration. 'function' is default since it is first and foremost used to get information per "extension object" (I think that is what its called)
	 * @param	string		The value-key to fetch from the config array. If NULL (default) MOD_SETTINGS[$MM_key] will be used. This is usefull if you want to force another function than the one defined in MOD_SETTINGS[function]. Call this in init() function of your Script Class: handleExternalFunctionValue('function', $forcedSubModKey)
	 * @return	void
	 * @see getExternalItemConfig(), $include_once, init()
	 */
	function handleExternalFunctionValue($MM_key='function', $MS_value=NULL)	{
		if (is_null($MS_value)) {
			if ($this->CMD) {
				$MS_value = $this->CMD;
			} else {
				$MS_value = 'tx_dam_cmd_nothing';
			};
		}

		$this->extClassConf = $this->getExternalItemConfig($this->MCONF['name'],$MM_key,$MS_value);
		if (is_array($this->extClassConf) && $this->extClassConf['path'])	{
			$this->include_once[]=$this->extClassConf['path'];
		} else {
			$this->extClassConf = $this->getExternalItemConfig($this->MCONF['name'],$MM_key,'tx_dam_cmd_nothing');
			if (is_array($this->extClassConf) && $this->extClassConf['path'])	{
				$this->include_once[]=$this->extClassConf['path'];
			}
		}
#		$this->MOD_MENU['function'][$MS_value] = $MS_value;
#		$this->MOD_SETTINGS['function'] = $MS_value;
	}





	/**
	 * Main function of the module. Write the content to $this->content
	 * 
	 * @return	void		
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS;


		//
		// Initialize the template object
		//

		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';


#debug($HTTP_GET_VARS, '$HTTP_GET_VARS', __LINE__, __FILE__);
#debug(t3lib_div::_GET(), '_GET()', __LINE__, __FILE__);
#debug($HTTP_POST_VARS, '$HTTP_POST_VARS', __LINE__, __FILE__);
#debug($GLOBALS['SOBE']->MOD_SETTINGS, '$GLOBALS[SOBE]->MOD_SETTINGS', __LINE__, __FILE__);
#debug($this->SLCMD, 'SLCMD', __LINE__, __FILE__);
#debug($this->MOD_MENU,'MOD_MENU', __LINE__, __FILE__);
#debug($this->MOD_MENU['function'],'$this->MOD_MENU[function]', __LINE__, __FILE__);
#debug($this->MOD_SETTINGS['function'],'$this->MOD_SETTINGS[function]', __LINE__, __FILE__);


		// Access check...
		// The page will show only if there is a valid page and if this page may be viewed by the user
#		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
#		$access = is_array($this->pageinfo) ? 1 : 0;



		//
		// Validating the input path and checking access against the mounts of the user.
		//

		$this->path = $this->basicFF->is_directory(tx_dam_div::getAbsPath($this->path));
		$this->path = $this->path ? $this->path.'/' : '';
		$access = $this->path && ($this->fmountID = $this->basicFF->checkPathAgainstMounts($this->path));
		$this->path_mount = $FILEMOUNTS[$this->fmountID]['path'];

//debug($this->MOD_SETTINGS['tx_dam_folder'], 'tx_dam_folder');
//debug($this->path_mount, 'path_mount');
//debug($this->path, 'path');
//debug($FILEMOUNTS, '$FILEMOUNTS');
//debug($this->fmountID, 'fmountID');


$access = TRUE;

		//
		// Main
		//
		if ($access)	{


			//
			// Output page header
			//
			$this->actionTarget = $this->actionTarget ? $this->actionTarget : t3lib_div::linkThisScript();
			$this->doc->form='<form action="'.htmlspecialchars($this->actionTarget).'" method="POST" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

				// JavaScript
			$this->doc->JScodeArray['jumpToUrl'] = '
				var script_ended = 0;
				var changed = 0;

				function jumpToUrl(URL)	{
					document.location = URL;
				}

				function jumpBack()	{
					document.location = "'.$this->returnUrl.'";
				}
				';
			$this->doc->postCode.= $this->doc->wrapScriptTags('
				script_ended = 1;');


			$this->extObjHeader();


				// Draw the header.
			$this->content.= $this->doc->startPage($this->pageTitle);
			$this->content.= $this->doc->header($this->pageTitle);
			$this->content.= $this->doc->spacer(5);


			//
			// Call submodule function
			//

			$this->extObjContent();


			$this->content.= $this->doc->spacer(10);


		} else {
				// If no access
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);
#TODO
			$this->content.= $this->doc->spacer(10);
		}


	}

	/**
	 * Prints out the module HTML
	 * 
	 * @return	string		HTML
	 */
	function printContent()	{
		global $SOBE;

		$this->content.= $this->doc->middle();
		$this->content.= $this->doc->endPage();
		$this->content=$this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

	/**
	 * Returns a message that the passed command was wrong
	 * 
	 * @return	string 	HTML content
	 */
	function wrongCommandMessage()	{
		global $SOBE, $LANG;

		$content = $SOBE->doc->section('',$SOBE->doc->icons(2).' '.$LANG->getLL('tx_dam_cmd_nothing.message'));
		if ($SOBE->CMD) {
			$content.= $SOBE->doc->section('Command:',htmlspecialchars($SOBE->CMD), 0,0);
		}
		return $content;
	}

	/**
	 * Send redirect header
	 * 
	 * @return	void
	 */
	function redirect()	{
		if ($this->redirect) {
			Header('Location: '.t3lib_div::locationHeaderUrl($this->redirect));
			exit;
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/index.php']);
}






// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_cmd');
$SOBE->init();

// Include files?
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkExtObj();	// Checking for first level external objects

// Repeat Include files! - if any files has been added by second-level extensions
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{include_once($INC_FILE);}
$SOBE->checkSubExtObj();	// Checking second level external objects

$SOBE->main();
$SOBE->printContent();
?>