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
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  103: class tx_dam_browse_media extends browse_links
 *  118:     function isValid($type, &$pObj)
 *  140:     function initDAM ()
 *  165:     function render($type, &$pObj)
 *  244:     function main()
 *
 *              SECTION: Media Browser Module
 *  334:     function dam_select($allowedFileTypes='', $disallowedFileTypes='')
 *  393:     function renderFileList($files, $mode='file')
 *
 *              SECTION: Upload module
 *  547:     function dam_upload($allowedFileTypes='', $disallowedFileTypes='')
 *  612:     function createFolder($path)
 *
 *              SECTION: Collect Data
 *  639:     function getFileListArr ($allowedFileTypes, $disallowedFileTypes, $mode)
 *
 *              SECTION: Tools
 *  713:     function addDisplayOptions()
 *  739:     function displayThumbs()
 *  760:     function getModSettings($key='')
 *  802:     function processParams()
 *  840:     function isParamPassed ($paramName)
 *  850:     function reinitParams()
 *  873:     function quoteJSvalue($value)
 *
 * TOTAL FUNCTIONS: 16
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');
require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');



// TODO workaround - shouldn't be needed
require_once (PATH_t3lib.'class.t3lib_pagetree.php');





/**
 * Inserts the DAM in the element browser.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_browse_media extends browse_links {


	var $damSC = null;


	var $MCONF_name = 'txdam_elbrowser';

	/**
	 * Check if this object should be rendered.
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	boolean
	 * @see SC_browse_links::main()
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

		$pArr = explode('|', t3lib_div::_GP('bparams'));

		if ($type=='db' AND $pArr[3]=='tx_dam') {
			$isValid = true;

		} elseif ($type=='file') {
			$isValid = true;
		}

		return $isValid;
	}


	/**
	 * Initializes DAM object which is the base DAM script class.
	 * All the stuff from SC is not needed but the selections stuff is used.
	 *
	 * @return void
	 */
	function initDAM () {

		if (!is_object($this->damSC)) {
			$GLOBALS['LANG']->includeLLFile('EXT:dam/modfunc_file_upload/locallang.xml');
			$this->damSC = t3lib_div::makeInstance('tx_dam_SCbase');
			$this->damSC->MCONF['name'] = $this->MCONF_name;
			$this->damSC->menuConfig();
			$this->damSC->init();
			$this->damSC->doc = &$this->doc;
		}
	}


	/**
	 * Initializes DAM selection.
	 *
	 * @return void
	 */
	function initDAMSelection () {

		$this->damSC->addParams = $this->addParams;

		$txdamSel = $this->getModSettings('txdamSel');
		list($txdamSel,$key) = explode(':', $txdamSel);


		if ($txdamSel=='__txdam_current_selection') {
			$MOD_SETTINGS = $GLOBALS['BE_USER']->getModuleData('txdamM1_list', '');
			$selection = $MOD_SETTINGS['tx_dam_select'];
			$this->damSC->selection->sl->setFromSerialized($selection, false);

		} elseif ($txdamSel=='__txdam_stored_selection') {

						// Store settings gui element
			$store = t3lib_div::makeInstance('t3lib_modSettings');
			$store->init('tx_dam_select', 'tx_dam_select');
			$store->initStorage();
			if ($selection = $store->getStoredData($key)) {
				$this->damSC->selection->sl->setFromSerialized($selection['tx_dam_select'], false);
			} else {
				$txdamSel=='';
			}
		}

		if ($txdamSel=='__txdam_eb_selection' OR $txdamSel=='') {
			$this->damSC->selection->sl->initSelection_getStored_mergeSubmitted();
		}
	}


	/**
	 * Rendering
	 * Called in SC_browse_links::main() when isValid() returns true;
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	string		Rendered content
	 * @see SC_browse_links::main()
	 */
	function render($type, &$pObj)	{
		global $LANG, $BE_USER;

		$this->pObj = &$pObj;

			// init class browse_links
		$this->init();

			// init the DAM object
		$this->initDAM();

		$this->getModSettings();

		$this->processParams();

			// init the DAM selection after we've got the params
		$this->initDAMSelection();



		$content = '';

		switch((string)$this->mode)	{
			case 'db':
			case 'file':
				$content = $this->main();
			break;
			default:
				$content .= '<h3>ERROR</h3>';
				$content .= '<h3>Unknown or missing mode!</h3>';
				$debug = true;
			break;
		}

		# $debug = true;
		# tx_dam::config_setValue('setup.debug', true);

			// debug output
		if ($debug OR tx_dam::config_getValue('setup.debug')) {

			$bparams = explode('|', $this->bparams);

			$debugArr = array(
				'act' => $this->act,
				'mode' => $this->mode,
				'thisScript' => $this->thisScript,
				'bparams' => $bparams,
				'allowedTables' => $this->allowedTables,
				'allowedFileTypes' => $this->allowedFileTypes,
				'disallowedFileTypes' => $this->disallowedFileTypes,
				'addParams' => $this->addParams,
				'pointer' => $this->damSC->selection->pointer->page,
				'SLCMD' => t3lib_div::GParrayMerged('SLCMD'),
				'Selection' => $this->damSC->selection->sl->sel,
			);

			$this->damSC->debugContent['browse_links'] = '<h4>EB SETTINGS</h4>'.t3lib_div::view_array($debugArr);

			$dbgContent = '<div style="background-color:#eee; border:1px solid #888; padding:5px;">'.implode('', $this->damSC->debugContent).'</div>';
			$content.= $this->damSC->buttonToggleDisplay('debug', 'Debug output', $dbgContent);
		}

		return $content;
	}


 	/**
	 * TYPO3 Element Browser: Showing a browse trees and allows you to browse for records
	 *
	 * @return	string		HTML content for the module
	 */
	function main()	{
		global $LANG, $BE_USER, $TYPO3_CONF_VARS;


		$path = tx_dam::path_makeAbsolute($this->damSC->path);
		if (!$path OR !@is_dir($path))	{
			$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // mabe not needed


			// Starting content:
		$content = $this->doc->startPage('TBE file selector');



			// Initializing the action value, possibly removing blinded values etc:
		$allowedItems = array('file', 'upload');
		$allowedItems = array_diff($allowedItems, t3lib_div::trimExplode(',',$this->thisConfig['blindLinkOptions'],1));
		if (!in_array($this->act, $allowedItems))	{
				// hooray
			$this->act = 'file';
		}

		$this->reinitParams();


			// Making menu in top:
		$menuDef = array();
		if (in_array('file', $allowedItems)){
			$menuDef['file']['isActive'] = ($this->act=='file');
			$menuDef['file']['label'] = $LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.xml:mlang_tabs_tab',1);
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=file&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('upload', $allowedItems)) {
			$menuDef['upload']['isActive'] = ($this->act=='upload');
			$menuDef['upload']['label'] = $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['upload']['url'] = '#';
			$menuDef['upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);


			// Depending on the current action we will create the actual module content:
		switch($this->act)	{
			case 'file':
				$this->addDisplayOptions();
				$content.= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
			break;
			case 'upload':
				$content.= $this->dam_upload($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
				$content.='<br /><br />';
				if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	{
					$content.= $this->createFolder($path);
				}
			break;
		}

			// Add some space
		$content.='<br />';

			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$content = $this->damSC->doc->insertStylesAndJS($content);
		return $content;
	}





	/***************************************
	 *
	 *	 Media Browser Module
	 *
	 ***************************************/



 	/**
	 * TYPO3 Element Browser: Showing the DAM trees, allowing you to browse for media records.
	 *
	 * @param	string		$allowedFileTypes Comma list of allowed file types
	 * @param	string		$disallowedFileTypes Comma list of disallowed file types
	 * @return	string		HTML content for the module
	 */
	function dam_select($allowedFileTypes='', $disallowedFileTypes='')	{
		global $BE_USER;

		$content = '';

			// the browse trees
		$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$browseTrees->init($this->thisScript, 'elbrowser');
		$trees = $browseTrees->getTrees();


		$files = $this->getFileListArr($allowedFileTypes, $disallowedFileTypes, $this->mode);

		$fileList = '';
		$fileList .= $allowedFileTypes ? $this->barheader($allowedFileTypes.' ') : '<h3 class="bgColor5">&nbsp;</h3>';
		$fileList .= '<br/>';
		$fileList .= $this->renderFileList($files, $this->mode);


		$content .= $this->formTag;
		$content .= $this->getSelectionSelector();
		$content .= $this->damSC->getResultInfoBar();



			// Putting the parts together, side by side:
		$content .= '

			<!--
				Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
				<tr>
					<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree',1).':').$trees.'</td>
					<td class="c-wCell" valign="top">'.$fileList.'</td>
				</tr>
			</table>
			';

		$content .= '</form>';

			// current selection box
		$content .= $this->formTag;

		$selectionBox = '<div style="width:70%;">'.$this->damSC->getCurrentSelectionBox().'</div>';
		$content .= $this->damSC->buttonToggleDisplay('selectionBox', $GLOBALS['LANG']->getLL('selection',1), $selectionBox);
		$content .= '</form>';

		$content .= $this->formTag;
		$content .= $this->damSC->getSearchBox('simple', false);
		$content .= '</form>';

		return $content;
	}


	/**
	 *
	 */
	function getSelectionSelector () {
		$txdamSel = $this->getModSettings('txdamSel');
		list($txdamSelType,$key) = explode(':', $txdamSel);


		$selectionSelector = array();

		$selectionSelector['__txdam_eb_selection'] = $GLOBALS['LANG']->getLL('eb_selection');
		$selectionSelector['__txdam_current_selection'] = $GLOBALS['LANG']->getLL('current_selection');


			// Stored selections
		$store = t3lib_div::makeInstance('t3lib_modSettings');
		$store->init('tx_dam_select', 'tx_dam_select');
		$store->initStorage();
		if (count($store->storedSettings)) {
			$selectionSelector['__txdam_stored_selection-divider'] = '--- '.$GLOBALS['LANG']->getLL('selectionClipboard',1).' ---';
			foreach($store->storedSettings as $storeIndex => $data)	{
				$title = $data['title'];
				$selectionSelector['__txdam_stored_selection:'.$storeIndex] = $title;
			}
		}


		$selectionSelector = t3lib_BEfunc::getFuncMenu($this->addParams, 'SET[txdamSel]', $this->getModSettings('txdamSel'), $selectionSelector);

		$content .= '<div class="infobar-extraline">'.$GLOBALS['LANG']->getLL('selection',1).': '.$selectionSelector.'</div>';

		return $content;
	}


	/**
	 * Render list of files.
	 *
	 * @param	array		List of files. See t3lib_div::getFilesInDir
	 * @param	string		$mode EB mode: "db", "file", ...
	 * @return	string		HTML output
	 */
	function renderFileList($files, $mode='file') {
		global $LANG;

		$out = '';

			// Listing the files:
		if (is_array($files) AND count($files))	{

			$displayThumbs = $this->displayThumbs();

				// Traverse the file list:
			$lines=array();
			foreach($files as $fI)	{

				if (!$fI['__exists']) {
					continue;
				}

					// Create file icon:
				$titleAttrib = tx_dam_guiFunc::icon_getTitleAttribute($fI);
				$iconFile = tx_dam::icon_getFileType($fI);
				$iconTag = tx_dam_guiFunc::icon_getFileTypeImgTag($fI);
				$iconAndFilename = $iconTag.htmlspecialchars(t3lib_div::fixed_lgd_cs($fI['file_title'], $GLOBALS['BE_USER']->uc['titleLen']));


					// Create links for adding the file:
				if (strstr($fI['file_name_absolute'], ',') || strstr($fI['file_name_absolute'], '|'))	{	// In case an invalid character is in the filepath, display error message:
					$eMsg = $LANG->JScharCode(sprintf($LANG->getLL('invalidChar'), ', |'));
					$ATag_insert = '<a href="#" onclick="alert('.$eMsg.');return false;">';

					// If filename is OK, just add it:
				} else {

						// JS: insertElement(table, uid, type, filename, fpath, filetype, imagefile ,action, close)
					$onClick_params = implode (', ', array(
						"'".$fI['_ref_table']."'",
						"'".$fI['_ref_id']."'",
						"'".$mode."'",
						$this->quoteJSvalue($fI['file_name']),
						$this->quoteJSvalue($fI['_ref_file_path']),
						"'".$fI['file_type']."'",
						"'".$iconFile."'")
						);
					$onClick = 'return insertElement('.$onClick_params.');';
					$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
					$onClick = 'return insertElement('.$onClick_params.', \'\', 1);';
					$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
				}

					// Create link to showing details about the file in a window:
				if ($fI['__exists']) {
					$Ahref = $GLOBALS['BACK_PATH'].'show_item.php?table='.rawurlencode($fI['file_name_absolute']).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
					$ATag_info = '<a href="'.htmlspecialchars($Ahref).'">';
					$info = $ATag_info.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).'</a>';

				} else {
					$info = '&nbsp;';
				}

					// Thumbnail/size generation:
				$clickThumb = '';
				if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $fI['file_type']) AND $displayThumbs AND is_file($fI['file_name_absolute']))	{
					$clickThumb = t3lib_BEfunc::getThumbNail('thumbs.php', $fI['file_path_absolute'].$fI['file_name'], '');
					$clickThumb = '<div style="width:56px; overflow:auto; padding: 5px; background-color:#fff; border:solid 1px #ccc;">'.$ATag_insert.$clickThumb.'</a>'.'</div>';
				} elseif ($displayThumbs) {
					$clickThumb = '<div style="width:68px"></div>';
				}


					// Show element:
				$lines[] = '
					<tr class="bgColor4">
						<td valign="top" nowrap="nowrap" style="min-width:20em">'.$ATag_insert.$iconAndFilename.'</a>'.'&nbsp;</td>
						<td valign="top" width="1%">'.$ATag_add.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/plusbullet2.gif', 'width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" /></a></td>
						<td valign="top" nowrap="nowrap" width="1%">'.$info.'</td>
					</tr>';


				$infoText = '';
				if ($this->getModSettings('extendedInfo')) {
					$infoText = tx_dam_guiFunc::meta_compileInfoData ($fI, 'file_name, file_size:filesize, _dimensions, caption:truncate:50, instructions', 'table');
					$infoText = str_replace('<table>', '<table border="0" cellpadding="0" cellspacing="1">', $infoText);
					$infoText = str_replace('<strong>', '<strong style="font-weight:normal;">', $infoText);
					$infoText = str_replace('</td><td>', '</td><td class="bgColor-10">', $infoText);
				}


				if ($displayThumbs AND $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3">
							<table border="0" cellpadding="0" cellspacing="0"><tr>
								<td valign="top">'.$clickThumb.'</td>
								<td valign="top" style="padding-left:1em">'.$infoText.'</td></tr>
							</table>
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				} elseif ($clickThumb OR $infoText) {
					$lines[] = '
						<tr class="bgColor">
							<td valign="top" colspan="3" style="padding-left:22px">
							'.$clickThumb.$infoText.'
							<div style="height:0.5em;"></div>
							</td>
						</tr>';
				}

				$lines[] = '
						<tr>
							<td colspan="3"><div style="height:0.5em;"></div></td>
						</tr>';
			}

				// Wrap all the rows in table tags:
			$out .= '



		<!--
			File listing
		-->
				<table border="0" cellpadding="1" cellspacing="1" id="typo3-fileList">
					'.implode('',$lines).'
				</table>';
		}

			// Return accumulated content for filelisting:
		return $out;
	}









	/***************************************
	 *
	 *	 Upload module
	 *
	 ***************************************/



 	/**
	 * Display uploads module
	 *
	 * @param	string		$allowedFileTypes Comma list of allowed file types
	 * @param	string		$disallowedFileTypes Comma list of disallowed file types
	 * @return	string		HTML content for the module
	 */
	function dam_upload($allowedFileTypes='', $disallowedFileTypes='')	{
		global $BE_USER, $FILEMOUNTS, $TYPO3_CONF_VARS;


		$content = '';

		$path = tx_dam::path_makeAbsolute($this->damSC->path);

		$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		if (!$path OR !@is_dir($path) OR !$fileProcessor->checkPathAgainstMounts($path))	{
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // maybe not needed


		if (@is_dir($path))	{

			$content .= '<form action="'.htmlspecialchars(t3lib_div::linkThisScript()).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

					// Upload form
			require_once(PATH_txdam.'modfunc_file_upload/class.tx_dam_file_upload.php');
			$damUploadExtObj = t3lib_div::makeInstance('tx_dam_file_upload');
			$damUploadExtObj->init($this->damSC, array('path' => PATH_thisScript));
			$damUploadExtObj->enableBatchProcessing = false;
			$this->damSC->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->damSC->MOD_MENU, t3lib_div::_GP('SET'), $this->damSC->MCONF['name'], 'ses');


			$form = $damUploadExtObj->main();


				// Create folder tree:
			$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
			$browseTrees->init($this->thisScript, 'elbrowser', true);
			$trees = $browseTrees->getTrees();


				// Putting the parts together, side by side:
			$content .= '

				<!--
					Wrapper table for folder tree / file list:
				-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
					<tr>
						<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree',1).':').$trees.'</td>
						<td class="c-wCell" valign="top">'.$form.'</td>
					</tr>
				</table>
				';

			$content.= '</form>';
		}

		return $content;
	}


	/**
	 * Makes a form for creating new folders in the filemount the user is browsing.
	 * The folder creation request is sent to the tce_file.php script in the core which will handle the creation.
	 *
	 * @param	string		Absolute filepath on server in which to create the new folder.
	 * @return	string		HTML for the create folder form.
	 */
	function createFolder($path) {
		if ($path!='/' && @is_dir($path))	{
			return parent::createFolder($path);
		}
		return '';
	}





	/***************************************
	 *
	 *	 Collect Data
	 *
	 ***************************************/



	/**
	 * Makes a DAM db query and collects data to be used in EB display
	 *
	 * @param	string		$allowedFileTypes Comma list of allowed file types
	 * @param	string		$disallowedFileTypes Comma list of disallowed file types
	 * @param	string		$mode EB mode: "db", "file", ...
	 * @return	array		Array of file elements
	 */
 	function getFileListArr ($allowedFileTypes, $disallowedFileTypes, $mode) {

		$filearray = array();

 		//
		// Use the current selection to create a query and count selected records
		//

		$this->damSC->selection->addSelectionToQuery();
		$this->damSC->selection->qg->query['FROM']['tx_dam'] = tx_dam_db::getMetaInfoFieldList(true, array('hpixels','vpixels','caption'));
		#$this->damSC->selection->qg->addSelectFields(...
		if ($allowedFileTypes) {
			$extList = '"'.implode ('","', explode(',',$allowedFileTypes)).'"';
			$this->damSC->selection->qg->addWhere('AND tx_dam.file_type IN ('.$extList.')', 'WHERE', 'tx_dam.file_type');
		}
		if ($disallowedFileTypes) {
			$extList = '"'.implode ('","', explode(',',$disallowedFileTypes)).'"';
			$this->damSC->selection->qg->addWhere('AND NOT tx_dam.file_type IN ('.$extList.')', 'WHERE', 'NOT tx_dam.file_type');
		}
		$this->damSC->selection->execSelectionQuery(TRUE);

			// any records found?
		if($this->damSC->selection->pointer->countTotal) {

				// limit query for browsing
			$this->damSC->selection->addLimitToQuery();
			$this->damSC->selection->execSelectionQuery();

			if($this->damSC->selection->res) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->damSC->selection->res)) {

					$row['file_title'] = $row['title'] ? $row['title'] : $row['file_name'];
					$row['file_path_absolute'] = tx_dam::path_makeAbsolute($row['file_path']);
					$row['file_name_absolute'] = $row['file_path_absolute'].$row['file_name'];
					$row['__exists'] = @is_file($row['file_name_absolute']);

					if ($mode=='db') {
						$row['_ref_table'] = 'tx_dam';
						$row['_ref_id'] = $row['uid'];
						$row['_ref_file_path'] = '';
					} else {
						$row['_ref_table'] = '';
						$row['_ref_id'] = t3lib_div::shortMD5($row['file_name_absolute']);
						$row['_ref_file_path'] = $row['file_name_absolute'];
					}

					$filearray[] = $row;
					if (count($filearray) >= $this->damSC->selection->pointer->itemsPerPage) {
						break;
					}
				}
			}
		}

		return $filearray;
 	}





	/***************************************
	 *
	 *	 Tools
	 *
	 ***************************************/



	/**
	 * Create HTML checkbox to enable/disable thumbnail display
	 *
	 * @return	string HTML code
	 */
	function addDisplayOptions() {

			// Getting flag for showing/not showing thumbnails:
		$noThumbs = $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB');

		if ($noThumbs)	{
			$thumbNailCheckbox = '';
		} else {

			$thumbNailCheckbox = t3lib_BEfunc::getFuncCheck('', 'SET[displayThumbs]',$this->displayThumbs(), $this->thisScript, t3lib_div::implodeArrayForUrl('',$this->addParams));
			$description = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:displayThumbs',1);
			$id = 'l'.uniqid('tx_dam_scbase');
			$idAttr = ' id="'.$id.'"';
			$thumbNailCheckbox = str_replace('<input', '<input'.$idAttr, $thumbNailCheckbox);
			$thumbNailCheckbox .= ' <label for="'.$id.'">'.$description.'</label>';
			$this->damSC->addOption('html', 'thumbnailCheckbox', $thumbNailCheckbox);
		}
		$this->damSC->addOption('funcCheck', 'extendedInfo', $GLOBALS['LANG']->getLL('displayExtendedInfo',1));
	}


	/**
	 * Return true or false whether thumbs should be displayed or not
	 *
	 * @return	boolean
	 */
	function displayThumbs() {
		static $displayThumb=NULL;

		if ($displayThumb==NULL) {
				// Getting flag for showing/not showing thumbnails generally:
			$displayThumb = !$GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB');

			if ($displayThumb)	{
				$displayThumb = $this->getModSettings('displayThumbs');
			}
		}
		return $displayThumb;
	}


	/**
	 * Return $MOD_SETTINGS array
	 *
	 * @param 	string	$key Returns $MOD_SETTINGS[$key] instead of $MOD_SETTINGS
	 * @return	array $MOD_SETTINGS
	 */
	function getModSettings($key='') {
		static $MOD_SETTINGS=NULL;

		if ($MOD_SETTINGS==NULL) {
			$MOD_MENU = array(
				'displayThumbs' => '',
				'extendedInfo' => '',
				'act' => '',
				'mode' => '',
				'bparams' => '',
				'txdamSel' => '',
				);
			$settings = t3lib_div::_GP('SET');
				// save params in session
			if ($this->act) $settings['act'] = $this->act;
			if ($this->mode) $settings['mode'] = $this->mode;
			if ($this->bparams) $settings['bparams'] = $this->bparams;


			if (t3lib_div::_GP('SLCMD')) {
				$settings['txdamSel'] = '__txdam_eb_selection';
			}

			$MOD_SETTINGS = $GLOBALS['BE_USER']->getModuleData('txdamM1_list', '');
			$MOD_SETTINGS = array_merge($MOD_SETTINGS, t3lib_BEfunc::getModuleData($MOD_MENU, $settings, $this->MCONF_name));
			$GLOBALS['SOBE']->MOD_SETTINGS = $this->damSC->MOD_SETTINGS = $MOD_SETTINGS;
		}
		if($key) {
			return $MOD_SETTINGS[$key];
		} else {
			return $MOD_SETTINGS;
		}
	}


	/**
	 * Processes bparams parameter
	 * Example value: "data[pages][39][bodytext]|||tt_content|" or "data[tt_content][NEW3fba56fde763d][image]|||gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai|"
	 *
	 * Values:
	 * 0: form field name reference
	 * 1: old/unused?
	 * 2: old/unused?
	 * 3: allowed types. Eg. "tt_content" or "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 * 4: allowed file types when tx_dam table. Eg. "gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai"
	 *
	 * @return void
	 */
	function processParams() {

		$this->act = $this->isParamPassed('act') ? $this->act : $this->getModSettings('act');
		$this->mode = $this->isParamPassed('mode') ? $this->mode : $this->getModSettings('mode');
		$this->bparams = $this->isParamPassed('bparams') ? $this->bparams : $this->getModSettings('bparams');

		$this->reinitParams();

		$pArr = explode('|', $this->bparams);
		$this->formFieldName = $pArr[0];

		switch((string)$this->mode)	{
			case 'rte':
			break;
			case 'db':
				$this->allowedTables = $pArr[3];
				if ($this->allowedTables=='tx_dam') {
					$this->allowedFileTypes = $pArr[4];
					$this->disallowedFileTypes = $pArr[5];
				}
			break;
			case 'file':
			case 'filedrag':
				$this->allowedTables = $pArr[3];
				$this->allowedFileTypes = $pArr[3];
			break;
			case 'wizard':
			break;
		}
	}


	/**
	 * Check if a param was passed by GET OR POST
	 *
	 * @param string $paramName Param name
	 * @return boolean
	 */
	function isParamPassed ($paramName) {
		return isset($_POST[$paramName]) ? true : isset($_GET[$paramName]);
	}


	/**
	 * Set some variables with the current parameters
	 *
	 * @return void
	 */
	function reinitParams() {
		global $TYPO3_CONF_VARS;

			// needed for browsetrees and just to be save
		$this->addParams = array();
		$GLOBALS['SOBE']->act = $this->addParams['act'] = $this->damSC->addParams['act'] = $this->act;
		$GLOBALS['SOBE']->mode = $this->addParams['mode'] = $this->damSC->addParams['mode'] = $this->mode;
		$GLOBALS['SOBE']->bparams = $this->addParams['bparams'] = $this->damSC->addParams['bparams'] = $this->bparams;
		if (t3lib_div::_GP('SLCMD')) {
			$this->addParams['SET[txdamSel]'] = $this->damSC->addParams['SET[txdamSel]'] = '__txdam_eb_selection';
		}

		$this->formTag = '<form action="'.htmlspecialchars(t3lib_div::linkThisScript($this->addParams)).'" method="post" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';

	}



	/**
	 * Quotes a string for usage as JS parameter. Depends wheter the value is used in script tags (it doesn't need/must not get htmlspecialchared in this case)
	 *
	 * @param	string		The string to encode.
	 * @param	boolean		If the values get's used in <script> tags.
	 * @return	string	The encoded value already quoted
	 * @todo use in 4.0 t3lib_div::quoteJSvalue($value)
	 */
	function quoteJSvalue($value)	{
		$value = addcslashes($value, '\''.chr(10).chr(13));
		return '\''.$value.'\'';
	}



}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_media.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_browse_media.php']);
}

?>