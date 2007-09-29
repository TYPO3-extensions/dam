<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2004 René Fritz (r.fritz@colorcube.de)
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
 *   69: class ux_SC_browse_links extends SC_browse_links 
 *   76:     function main_db()	
 *  134:     function main_file()	
 *  253:     function file_select($pArr,$noThumbs)	
 *  295:     function dam_select($pArr,$noThumbs,$extensionList='')	
 *  346:     function dam_getFileListArr ($extensionList, $mode) 
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 
 
define('PATH_txdam', t3lib_extMgm::extPath('dam'));

require_once(PATH_txdam.'lib/class.tx_dam_div.php');
require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');
require_once(PATH_txdam.'lib/class.tx_dam_scbase.php');



// workaround - shouldn't be needed
require_once (PATH_t3lib.'class.t3lib_pagetree.php');
/*
> > - BE: Verwendet man den Elementbrowser (für welche Aktion auch immer),
> > wählt eine Seite im Baum und sucht mit der Suchfunktion in der rechten
> > Hälfte des Elementbrowsers und schließt in die Suche Unterebenen ein,
> > erhält man im Fenster des ELementbrowsers:
> >
> > Fatal error: Cannot instantiate non-existent class: t3lib_pagetree in
> > /data/users/c145/htdocs/typo3_src-3.7.0_dam/t3lib/class.t3lib_div.php
> > on line 3238
*/





/**
 * Inserts the DAM in the element browser.
 * This is currently a little like a hack, because it's hard to extend the EL code.
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3
 * @subpackage tx_dam
 */
class ux_SC_browse_links extends SC_browse_links {


	var $damSC;


	function initDAM () {
		if (!is_object($this->damSC)) {
			$this->damSC = t3lib_div::makeInstance('tx_dam_scbase');
			$this->damSC->MCONF['name']='elbrowser';
			$this->damSC->menuConfig();
			$this->damSC->init();
			$this->damSC->doc = &$this->doc;
		}
	}

 
 	/**
	 * TYPO3 Element Browser: Showing a page tree and allows you to browse for records
	 * 
	 * @return	string		HTML content for the module
	 */
	function main_db()	{
		global $LANG, $BE_USER;

			// Init variable:
		$pArr = explode('|',$this->bparams);
		
		if($pArr[3]!='tx_dam') {
			return parent::main_db();		
		}
		
		
		$GLOBALS['LANG']->includeLLFile('EXT:dam_file/modfunc_upload/locallang.php');
		$this->initDAM();
		
		
			// Starting content:
		$content.=$this->doc->startPage('TBE file selector');

		$this->act = ($this->act=='file' OR $this->act=='page') ? 'file' : $this->act;

			// Initializing the action value, possibly removing blinded values etc:
		$allowedItems = array_diff(explode(',','file,upload'),t3lib_div::trimExplode(',',$this->thisConfig['blindLinkOptions'],1));
		reset($allowedItems);

		
			// Init variable:
		$pArr = explode('|',$this->bparams);
	
	
			// Getting flag for showing/not showing thumbnails:
		$noThumbs = $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB');
	
		if (!$noThumbs)	{
				// MENU-ITEMS, fetching the setting for thumbnails from File>List module:
			$_MOD_MENU = array('displayThumbs' => '');
			$_MCONF['name']='file_list';
			$_MOD_SETTINGS = t3lib_BEfunc::getModuleData($_MOD_MENU, t3lib_div::_GP('SET'), $_MCONF['name']);
			$addParams = '&act='.$this->act.'&mode='.$this->mode.'&expandFolder='.rawurlencode($path).'&bparams='.rawurlencode($this->bparams);
			$thumbNailCheck = t3lib_BEfunc::getFuncCheck('','SET[displayThumbs]',$_MOD_SETTINGS['displayThumbs'],'browse_links.php',$addParams).' '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.php:displayThumbs',1);
		} else {
			$thumbNailCheck='';
		}
		


			// Making menu in top:
		$menuDef = array();

		if (in_array('file',$allowedItems)){
			$menuDef['file']['isActive'] = $this->act=='file';
			$menuDef['file']['label'] = $LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.php:mlang_tabs_tab');
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onclick="jumpToUrl(\'?act=file\');return false;"';
		}
		if (in_array('upload',$allowedItems)) {
			$menuDef['upload']['isActive'] = $this->act=='upload';
			$menuDef['upload']['label'] = $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['upload']['url'] = '#';
			$menuDef['upload']['addParams'] = 'onclick="jumpToUrl(\'?act=upload\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);


			// Depending on the current action we will create the actual module content for selecting a link:
		switch($this->act)	{
			case 'file':
				$content.= $this->dam_select($pArr,($noThumbs?$noThumbs:!$_MOD_SETTINGS['displayThumbs']),$pArr[4]);
				$content.=$thumbNailCheck;
			break;
			case 'upload':		
				$content.= $this->dam_upload($pArr,($noThumbs?$noThumbs:!$_MOD_SETTINGS['displayThumbs']),$pArr[4]);
			break;
		}	

		

			// Add some space
		$content.='<br /><br />';
		
			// Ending page, returning content:
		$content.= $this->doc->endPage();
		return $content;	
	}
	
		
	/**
	 * TYPO3 Element Browser: Showing a folder tree, allowing you to browse for files.
	 * 
	 * @return	string		HTML content for the module
	 */
	function main_file()	{
		global $LANG, $BE_USER;



		$GLOBALS['LANG']->includeLLFile('EXT:dam_file/modfunc_upload/locallang.php');
		$this->damSC = t3lib_div::makeInstance('tx_dam_scbase');
		$this->damSC->doc = &$this->doc;
		

		$CMD = t3lib_div::_GP('SLCMD');
		if (is_array($CMD['SELECT']['txdamFolder'])) {
			$this->expandFolder = key($CMD['SELECT']['txdamFolder']);
		}	
		$CMD = t3lib_div::_GP('SET');
		if ($CMD['tx_dam_folder']) {
			$this->expandFolder = $CMD['tx_dam_folder'];
		}	

			// Starting content:
		$content.=$this->doc->startPage('TBE file selector');

		$this->act = ($this->act=='file' OR $this->act=='page') ? 'file' : $this->act;

			// Initializing the action value, possibly removing blinded values etc:
		$allowedItems = array_diff(explode(',','file,fileraw'),t3lib_div::trimExplode(',',$this->thisConfig['blindLinkOptions'],1));
		reset($allowedItems);

			// Making menu in top:
		$menuDef = array();

		if (in_array('file',$allowedItems)){
			$menuDef['file']['isActive'] = $this->act=='file';
			$menuDef['file']['label'] = $LANG->sL('LLL:EXT:dam/mod_main/locallang_mod.php:mlang_tabs_tab');
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onclick="jumpToUrl(\'?act=file\');return false;"';
		}
		if (in_array('fileraw',$allowedItems)) {
			$menuDef['fileraw']['isActive'] = $this->act=='fileraw';
			$menuDef['fileraw']['label'] = $LANG->getLL('file',1);
			$menuDef['fileraw']['url'] = '#';
			$menuDef['fileraw']['addParams'] = 'onclick="jumpToUrl(\'?act=fileraw\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);


		
			// Init variable:
		$pArr = explode('|',$this->bparams);
	
			// Create upload/create folder forms, if a path is given:
		$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$path=$this->expandFolder;
		if (!$path || !@is_dir($path))	{
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		if ($path!='/' && @is_dir($path))	{
			$uploadForm=$this->uploadForm($path);
			$createFolder=$this->createFolder($path);
		} else {
			$createFolder='';
			$uploadForm='';
		}
		if ($BE_USER->getTSConfigVal('options.uploadFieldsInTopOfEB'))	$content.=$uploadForm;
	
			// Getting flag for showing/not showing thumbnails:
		$noThumbs = $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB');
	
		if (!$noThumbs)	{
				// MENU-ITEMS, fetching the setting for thumbnails from File>List module:
			$_MOD_MENU = array('displayThumbs' => '');
			$_MCONF['name']='file_list';
			$_MOD_SETTINGS = t3lib_BEfunc::getModuleData($_MOD_MENU, t3lib_div::_GP('SET'), $_MCONF['name']);
			$addParams = '&act='.$this->act.'&mode='.$this->mode.'&expandFolder='.rawurlencode($path).'&bparams='.rawurlencode($this->bparams);
			$thumbNailCheck = t3lib_BEfunc::getFuncCheck('','SET[displayThumbs]',$_MOD_SETTINGS['displayThumbs'],'browse_links.php',$addParams).' '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.php:displayThumbs',1);
		} else {
			$thumbNailCheck='';
		}
		

			// Depending on the current action we will create the actual module content for selecting a link:
		switch($this->act)	{
			case 'file':
				$content.= $this->dam_select($pArr,($noThumbs?$noThumbs:!$_MOD_SETTINGS['displayThumbs']),$pArr[3]);
			break;
			case 'fileraw':		
				$content.= $this->file_select($pArr,($noThumbs?$noThumbs:!$_MOD_SETTINGS['displayThumbs']));
			break;
		}		
		
		$content.=$thumbNailCheck;
	
			// Adding create folder + upload forms if applicable:
		if (!$BE_USER->getTSConfigVal('options.uploadFieldsInTopOfEB'))	$content.=$uploadForm;
		if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	$content.=$createFolder;
			
			// Add some space
		$content.='<br /><br />';
		
		
			// Debugging:
		if (FALSE) debug(array(
			'pointer' => $this->pointer,
			'act' => $this->act,
			'mode' => $this->mode,
			'curUrlInfo' => $this->curUrlInfo,
			'curUrlArray' => $this->curUrlArray,
			'P' => $this->P,
			'bparams' => $this->bparams,
			'RTEtsConfigParams' => $this->RTEtsConfigParams,
			'expandPage' => $this->expandPage,
			'expandFolder' => $this->expandFolder,
			'PM' => $this->PM,
		),'Internal variables of Script Class:');
		
				
			// Ending page, returning content:
		$content.= $this->doc->endPage();
		return $content;
	}
 

 	/**
	 * TYPO3 Element Browser: Showing a folder tree, allowing you to browse for files.
	 * 
	 * @param	[type]		$pArr: ...
	 * @param	[type]		$noThumbs: ...
	 * @return	string		HTML content for the module
	 */
	function file_select($pArr,$noThumbs)	{
		global $BE_USER;

			// Create folder tree:
		$foldertree = t3lib_div::makeInstance('TBE_FolderTree');
		$foldertree->script='browse_links.php';
		$foldertree->ext_noTempRecyclerDirs = ($this->mode == 'filedrag');
		$tree=$foldertree->getBrowsableTree();

		list(,,$specUid) = explode('_',$this->PM);
	
		if ($this->mode=='filedrag')	{
			$files = $this->TBE_dragNDrop($foldertree->specUIDmap[$specUid],$pArr[3]);
		} else {
			$files = $this->TBE_expandFolder($foldertree->specUIDmap[$specUid],$pArr[3],$noThumbs);
		}

			// Putting the parts together, side by side:		
		$content.= '

			<!--
				Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
				<tr>
					<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree').':').$tree.'</td>
					<td class="c-wCell" valign="top">'.$files.'</td>
				</tr>
			</table>
			';
		return $content;
	}	
	
	 
 	/**
	 * TYPO3 Element Browser: Showing the DAM trees, allowing you to browse for media records.
	 * 
	 * @param	[type]		$pArr: ...
	 * @param	[type]		$noThumbs: ...
	 * @param	[type]		$extensionList: ...
	 * @return	string		HTML content for the module
	 */
	function dam_select($pArr,$noThumbs,$extensionList='')	{
		global $BE_USER;


###		$foldertree->ext_noTempRecyclerDirs = ($this->mode == 'filedrag');

			// the trees
		$browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$browseTrees->init('browse_links.php', true);
		$tree = $browseTrees->getTrees();

#		list(,,$specUid) = explode('_',$this->PM);
		
		if ($this->mode=='filedrag')	{
#TODO			$files = $this->TBE_dragNDrop($foldertree->specUIDmap[$specUid],$pArr[3]);
		} else {
#			if (t3lib_div::_GP('expandFolder')) {
#				$files = $this->TBE_expandFolder($foldertree->specUIDmap[$specUid],$pArr[3],$noThumbs);
#			} else {

				$fileList = '';
				$files = $this->dam_getFileListArr($extensionList, $this->mode);
				#$fileList.= $this->renderFileHeader($expandFolder, count($files));				
				$fileList .= $this->barheader(($extensionList?$extensionList.' ':'').sprintf($GLOBALS['LANG']->getLL('files').' (%s):',count($files)));
				$fileList.= $this->renderFileList($files, $noThumbs, $this->mode);
#			}
		}

			// Putting the parts together, side by side:		
		$content.= '

			<!--
				Wrapper table for folder tree / file list:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
				<tr>
					<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree').':').$tree.'</td>
					<td class="c-wCell" valign="top">'.$fileList.'</td>
				</tr>
			</table>
			';
			
		$content.= $this->damSC->getSearchBox();
		$content.= '<br /><br/>';
		
		return $content;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$extensionList: ...
	 * @param	[type]		$mode: ...
	 * @return	[type]		...
	 */
 	function dam_getFileListArr ($extensionList, $mode) {
 		$_MCONF = $GLOBALS['MCONF'];
 		$GLOBALS['MCONF']['name'] = 'dam_elbrowser';
		$this->damSC = t3lib_div::makeInstance('tx_dam_SCbase');
		$this->damSC->init();
		$this->damSC->initDB();
		$this->damSC->sl->mergeSelection(t3lib_div::_GP('SLCMD'));
		
		$this->damSC->addSelectionToQuery();
		$this->damSC->qg->query['FROM']['tx_dam'] = 'tx_dam.uid,tx_dam.file_name,tx_dam.file_path,tx_dam.file_size,tx_dam.hpixels,tx_dam.vpixels';
		if ($extensionList) {
			$extList = '"'.implode ('","', explode(',',$extensionList)).'"';
			$this->damSC->qg->query['WHERE']['WHERE']['tx_dam.file_type'] = 'AND tx_dam.file_type IN ('.$extList.')';
		}

		$res = $this->damSC->execSelectionQuery();

		$filearray = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$filepath = tx_dam_div::getAbsPath ($row['file_path']).$row['file_name'];
			$fI = pathinfo($filepath);
			$fI['file_path'] = $filepath;
			$fI['file_size'] = $row['file_size'];
			$fI['info_dimension'] = $row['hpixels'] ? ($row['hpixels'].'x'.$row['vpixels'].' pixels') : '';
			$fI['info_hover'] = $fI['basename'].' ('.t3lib_div::formatSize($fI['file_size']).'bytes'.($fI['info_dimension']?', '.$fI['info_dimension']:'').')';
			$fI['info_text'] = $fI['info_dimension'];			
			$fI['icon_file'] = '';
			$fI['icon_tag'] = '';
			
			if ($mode=='db') {
				$fI['ref_table'] = 'tx_dam';
				$fI['ref_id'] = $row['uid'];
				$fI['ref_file_path'] = '';
			} else {
				$fI['ref_table'] = '';
				$fI['ref_id'] = t3lib_div::shortMD5($fI['file_path']);
				$fI['ref_file_path'] = $fI['file_path'];
			}
			
			$filearray[] = $fI;
		}
		$GLOBALS['MCONF'] = $_MCONF;
		
		return $filearray; 		
 	}

 	/**
	 * TYPO3 Element Browser: Showing the DAM trees, allowing you to browse for media records.
	 * 
	 * @param	[type]		$pArr: ...
	 * @param	[type]		$noThumbs: ...
	 * @param	[type]		$extensionList: ...
	 * @return	string		HTML content for the module
	 */
	function dam_upload($pArr,$noThumbs,$extensionList='')	{
		global $BE_USER, $FILEMOUNTS, $TYPO3_CONF_VARS;


		$content = '';
	
		$CMD = t3lib_div::_GP('SET');
		if ($CMD['tx_dam_folder']) {
			$this->expandFolder = $CMD['tx_dam_folder'];
		}	

		if (!$this->expandFolder) {
			reset($FILEMOUNTS);
			$fmount = current($FILEMOUNTS);
			$this->expandFolder = $fmount['path'];
		}
		
		$this->initDAM();
		$this->damSC->path = $this->expandFolder;


			// Create upload/create folder forms, if a path is given:
		$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$path = $this->damSC->path;
		if (!$path || !@is_dir($path))	{
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		if ($path!='/' && @is_dir($path))	{
			$this->damSC->path = $path;
			$this->damSC->fmountID = $fileProcessor->checkPathAgainstMounts($this->damSC->path);



			$content.= '<form action="'.t3lib_div::linkThisScript().'" method="POST" name="editform" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';
			
					// Upload form			
			require_once(t3lib_extMgm::extPath('dam_file').'modfunc_upload/class.tx_damfile_upload.php');
			$this->damUL = t3lib_div::makeInstance('tx_damfile_upload');
			$this->damUL->pObj = &$this->damSC;
			
			$form = '';
			$form.= $this->damUL->main();
				
	
	
				// Create folder tree:
			$foldertree = t3lib_div::makeInstance('TBE_FolderTree');
			$foldertree->script='browse_links.php';
			$foldertree->ext_noTempRecyclerDirs = ($this->mode == 'filedrag');
			$tree=$foldertree->getBrowsableTree();
	
			list(,,$specUid) = explode('_',$this->PM);
		
	//		if ($this->mode=='filedrag')	{
	//			$files = $this->TBE_dragNDrop($foldertree->specUIDmap[$specUid],$pArr[3]);
	//		} else {
	//			$files = $this->TBE_expandFolder($foldertree->specUIDmap[$specUid],$pArr[3],$noThumbs);
	//		}
	
				// Putting the parts together, side by side:		
			$content.= '
	
				<!--
					Wrapper table for folder tree / file list:
				-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-EBfiles">
					<tr>
						<td class="c-wCell" valign="top">'.$this->barheader($GLOBALS['LANG']->getLL('folderTree').':').$tree.'</td>
						<td class="c-wCell" valign="top">'.$form.'</td>
					</tr>
				</table>
				';
				
			$content.= '</form>';
		}
		
		return $content;
		
	}










	/**
	 * For TYPO3 Element Browser: Expand folder of files.
	 *
	 * @param	string		The folder path to expand
	 * @param	string		List of fileextensions to show
	 * @param	boolean		Whether to show thumbnails or not. If set, no thumbnails are shown.
	 * @return	string		HTML output
	 */
	function TBE_expandFolder($expandFolder=0,$extensionList='',$noThumbs=0)	{
		global $LANG;

		$expandFolder = $expandFolder ? $expandFolder : $this->expandFolder;
		$out='';
		if ($expandFolder && $this->checkFolder($expandFolder))	{
				// Listing the files:
			$files = $this->getFileListArr($expandFolder,$extensionList);
			$out.= $this->renderFileHeader($expandFolder, count($files));
			$out.= $this->renderFileList($files, $noThumbs);
		}

			// Return accumulated content for filelisting:
		return $out;
	}

	
	/**
	 * Create headline (showing number of files).
	 *
	 * @param	string		The folder path or just it's name.
	 * @param	integer		If set a bar with count of files will be printed..
	 * @return	string		HTML output
	 */
	function renderFileHeader($path, $fileCount=NULL) {
		global $LANG;

		$out = '';

		if (!is_null($fileCount)) {
				// Create headline (showing number of files):
			$out .= $this->barheader(sprintf($GLOBALS['LANG']->getLL('files').' (%s):',$fileCount));
		}

			// Create the header of current folder:
		if ($path) {
			$picon = '<img'.t3lib_iconWorks::skinImg('','gfx/i/_icon_webfolders.gif','width="18" height="16"').' alt="" />';
			$picon .= htmlspecialchars(t3lib_div::fixed_lgd_cs(basename($path), $GLOBALS['BE_USER']->uc['titleLen']));
			$out .= $picon.'<br />';
		}
		
		return $out;
	}

	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		$path: Is the path to the file
	 * @param	string		$extensionList is the comma list of extensions to read only (blank = all)
	 * @return	array		Array of the files found
	 */
	function getFileListArr($path,$extensionList='') {
		
			// Init graphic object for reading file dimensions:
		$imgObj = t3lib_div::makeInstance('t3lib_stdGraphic');
		$imgObj->init();
		$imgObj->mayScaleUp=0;
		$imgObj->tempPath=PATH_site.$imgObj->tempPath;
			
		$files = t3lib_div::getFilesInDir($path,$extensionList,1 /*$prependPath*/ ,1 /*$order*/);

		$filearray = array();
		foreach($files as $filepath)	{
			$fI = pathinfo($filepath);
			$fI['file_path'] = $filepath;
			$fI['file_size'] = filesize($fI['file_path']);
			if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],$fI['extension'])) {
				$imgInfo = $imgObj->getImageDimensions($filepath);
				$fI['info_dimension'] = $imgInfo[0] ? ($imgInfo[0].'x'.$imgInfo[1].' pixels') : '';
			}
			$fI['info_hover'] = $fI['basename'].' ('.t3lib_div::formatSize($fI['file_size']).'bytes'.($fI['info_dimension']?', '.$fI['info_dimension']:'').')';
			$fI['info_text'] = $fI['info_dimension'];
			
			$fI['icon_file'] = '';
			$fI['icon_tag'] = '';
			
			$fI['ref_table'] = '';
			$fI['ref_id'] = t3lib_div::shortMD5($fI['file_path']);
			$fI['ref_file_path'] = $fI['file_path'];
			
			$filearray[] = $fI;
		}
		return $filearray;
	}
	
	/**
	 * Render list of files.
	 *
	 * @param	array		List of files. See t3lib_div::getFilesInDir
	 * @param	boolean		Whether to show thumbnails or not. If set, no thumbnails are shown.
	 * @return	string		HTML output
	 */
	function renderFileList($files, $noThumbs=0, $mode='file') {
		global $LANG;

		$out='';

			// Listing the files:
		if (is_array($files) AND count($files))	{

				// Traverse the file list:
			$lines=array();
			foreach($files as $fI)	{

					// Thumbnail/size generation:
				$clickThumb = '';
				if (t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],$fI['extension']) && !$noThumbs)	{
					$clickThumb = t3lib_BEfunc::getThumbNail('thumbs.php',$fI['file_path'],'hspace="5" vspace="5" border="1"');
				}

					// Create file icon:
				$titleAttrib = ' title="'.htmlspecialchars($fI['info_hover']).'"';
				$iconFile = $fI['icon_file'] ? $fI['icon_file'] :t3lib_BEfunc::getFileIcon(strtolower($fI['extension']));
				$iconTag = $fI['icon_tag'] ? $fI['icon_tag'] : '<img'.t3lib_iconWorks::skinImg('','gfx/fileicons/'.$iconFile,'width="18" height="16"').' hspace="2"'.$titleAttrib.' class="absmiddle" alt="" />';
				
					// Create links for adding the file:
				if (strstr($fI['file_path'],',') || strstr($fI['file_path'],'|'))	{	// In case an invalid character is in the filepath, display error message:
					$eMsg = $LANG->JScharCode(sprintf($LANG->getLL('invalidChar'),', |'));
					$ATag = $ATag_keepOpen = "<a href=\"#\" onclick=\"alert(".$eMsg.");return false;\">";
				} else {	// If filename is OK, just add it:
					
						// JS: insertElement(table, uid, type, filename, fpath, filetype, imagefile ,action, close)
					$onClick_params = implode (', ', array(
						"'".$fI['ref_table']."'", 
						"'".$fI['ref_id']."'", 
						"'".$mode."'", 
						"unescape('".rawurlencode($fI['basename'])."')", 
						"unescape('".rawurlencode($fI['ref_file_path'])."')", 
						"'".$fI['extension']."'", 
						"'".$iconFile."'")
						);
					$onClick = 'return insertElement('.$onClick_params.');';
					$ATag_add = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
					$onClick = 'return insertElement('.$onClick_params.', \'\', 1);';
					$ATag_insert = '<a href="#" onclick="'.htmlspecialchars($onClick).'"'.$titleAttrib.'>';
				}

					// Create link to showing details about the file in a window:
				$Ahref = 'show_item.php?table='.rawurlencode($fI['file_path']).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
				$ATag_info='<a href="'.htmlspecialchars($Ahref).'">';

					// Combine the stuff:
				$filenameAndIcon=$ATag_insert.$iconTag.htmlspecialchars(t3lib_div::fixed_lgd_cs(basename($fI['file_path']),$GLOBALS['BE_USER']->uc['titleLen'])).'</a>';

					// Show element:
				if ($clickThumb)	{		// Image...
					$lines[]='
						<tr class="bgColor4">
							<td nowrap="nowrap">'.$filenameAndIcon.'&nbsp;</td>
							<td>'.$ATag_add.'<img'.t3lib_iconWorks::skinImg('','gfx/plusbullet2.gif','width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" /></a></td>
							<td nowrap="nowrap">'.($ATag_info.'<img'.t3lib_iconWorks::skinImg('','gfx/zoom2.gif','width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).$ATag2_e).'</td>
							<td nowrap="nowrap">&nbsp;'.$fI['info_text'].'</td>
						</tr>';
					$lines[]='
						<tr>
							<td colspan="4">'.$ATag_insert.$clickThumb.'</a></td>
						</tr>';
				} else {
					$lines[]='
						<tr class="bgColor4">
							<td nowrap="nowrap">'.$filenameAndIcon.'&nbsp;</td>
							<td>'.$ATag_add.'<img'.t3lib_iconWorks::skinImg('','gfx/plusbullet2.gif','width="18" height="16"').' title="'.$LANG->getLL('addToList',1).'" alt="" /></a></td>
							<td nowrap="nowrap">'.($ATag_info.'<img'.t3lib_iconWorks::skinImg('','gfx/zoom2.gif','width="12" height="12"').' title="'.$LANG->getLL('info',1).'" alt="" /> '.$LANG->getLL('info',1).'</a>').'</td>
							<td nowrap="nowrap">&nbsp;'.$fI['info_text'].'</td>
						</tr>';
				}
				$lines[]='
						<tr>
							<td colspan="3"><img src="clear.gif" width="1" height="3" alt="" /></td>
						</tr>';
			}

				// Wrap all the rows in table tags:
			$out.='



		<!--
			File listing
		-->
				<table border="0" cellpadding="0" cellspacing="1" id="typo3-fileList">
					'.implode('',$lines).'
				</table>';
		}

			// Return accumulated content for filelisting:
		return $out;
	}
		
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_elbrowser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/class.tx_dam_elbrowser.php']);
}

 ?>