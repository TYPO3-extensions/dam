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
 * Module 'Media>List'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   74: class tx_dam_mod_list extends tx_dam_SCbase
 *   83:     function init()
 *  103:     function main()
 *  155:     function jumpToUrl(URL)
 *  213:     function printContent()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */








unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_selstorage.php');
require_once(PATH_txdam.'lib/class.tx_dam_guirenderlist.php');


$LANG->includeLLFile('EXT:dam/mod_list/locallang.xml');


$BE_USER->modAccess($MCONF,1);






/**
 * Script class for the DAM record list module
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_mod_list extends tx_dam_SCbase {



	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER, $LANG, $BACK_PATH, $TYPO3_CONF_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS;

			// Init guiRenderList object:

		$this->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');


			// Initialize the template object

		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->docType = 'xhtml_trans';


			// This will return content necessary for the context sensitive clickmenus to work: bodytag events, JavaScript functions and DIV-layers.
		$CMparts = $this->doc->getContextMenuCode();
		$this->doc->bodyTagAdditions = $CMparts[1];
		$this->doc->JScode.= $CMparts[0];
		$this->doc->postCode.= $CMparts[2];


		// Access check can not be checked in beforehand
		// The user has access when he has acces to the module and that's nothing to be checked here
		$access = true;


		// **************************
		// Main
		// **************************
		if ($access)	{

			$this->selection->sl->initSelection_getStored_mergeSubmitted();


				// Store settings gui element
			$this->store = t3lib_div::makeInstance('t3lib_modSettings');
			$this->store->init('tx_dam_select');
			$this->store->type = 'perm';
			$this->store->setStoreList('tx_dam_select');
			$this->store->processStoreControl();


				// Store settings gui element
			$this->selExport = t3lib_div::makeInstance('tx_dam_selStorage');
			$this->selExport->init();
			$this->selExport->processStoreControl();





			//
			// Output page header
			//

			$this->doc->form='<form action="'.htmlspecialchars(t3lib_div::linkThisScript($this->addParams)).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

			$this->addDocStyles();
			$this->addDocJavaScript();


			$this->doc->postCode.= $this->doc->wrapScriptTags('
				script_ended = 1;');


			$this->extObjHeader();

				// Draw the header.
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);


			//
			// Output tabmenu if not a single function was forced
			//

			if (!$this->forcedFunction AND count($this->MOD_MENU['function'])>1) {
				$this->content.= $this->doc->section('',     $this->getTabMenu($this->addParams,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function']),0,1);
			}

			//
			// Call submodule function
			//

			$this->extObjContent();


			//
			// output footer: search box, options, store control, ....
			//

			#$this->content.= $this->doc->spacer(10);
			$this->content.= $this->guiItems->getOutput('footer');


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.= $this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.= $this->doc->spacer(10);


		} else {
				// If no access
			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);
			$this->content.= $this->doc->section('', $LANG->sL('LLL:EXT:lang/locallang_mod_web_perm.xml:A_Denied',1));
			$this->content.= $this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	string		HTML
	 */
	function printContent()	{
		$this->content.= $this->doc->middle();
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_list/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_list/index.php']);
}






// Make instance:
$SOBE = t3lib_div::makeInstance('tx_dam_mod_list');
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