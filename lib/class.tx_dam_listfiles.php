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
/**
 * DAM file listing class
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
 *   78: class tx_dam_listfiles extends tx_dam_listbase
 *
 *              SECTION: Setup
 *  117:     function tx_dam_listfiles()
 *  127:     function __construct()
 *
 *              SECTION: Set data
 *  169:     function setPathInfo($pathInfo)
 *
 *              SECTION: Column rendering
 *  191:     function getItemColumns ($item)
 *
 *              SECTION: Column rendering
 *  275:     function getItemAction ($item)
 *  286:     function getItemIcon ($item)
 *
 *              SECTION: Controls
 *  355:     function getItemControl($item)
 *  395:     function getHeaderControl()
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/class.tx_dam_listbase.php');
require_once (PATH_txdam.'lib/class.tx_dam_actioncall.php');


/**
 * Class for rendering of Media>File>List
 * The class is not really abstract but on a good way to become so ...
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listfiles extends tx_dam_listbase {


	/**
	 * stores two tx_dam_dir objects
	 */
	var $dataObjects = array();

	/**
	 * pathInfo array of the current folder
	 */
	var $pathInfo = NULL;

	/**
	 * Display file sizes in bytes or formatted
	 */
	var $showDetailedSize = false;

	/**
	 * enable/disbale auto indexing while showing file list
	 */
	var $enableAutoIndexing = false;



	/***************************************
	 *
	 *	 Setup
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_listfiles()	{
		$this->__construct();
	}


	/**
	 * Initialization of class
	 *
	 * @return	void
	 */
	function __construct() {
		global $BE_USER;

		parent::__construct();

		$this->paramName['setFolder'] = 'SET[tx_dam_folder]';

		$this->showIcon = true;

		$this->clearColumns();
		$this->addColumn('title', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_file'));
		$this->addColumn('file_type', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_fileext'));
		$this->addColumn('mtime', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_tstamp'));
		$this->addColumn('size', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_size'));
		$this->columnTDAttr['size'] = ' align="right"';
		$this->addColumn('perms', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_mod_file_list.xml:c_rw'));
		$this->addColumn('_CONTROL_', '');
#		$this->addColumn('_CLIPBOARD_', '');

		if (!count($this->showControls)) {
			$this->showControls = array('viewPage','editPage','newPage','unHidePage','movePage','pasteIntoPage','clearPageCache',
							'refresh',
							'permsRec','revertRec','editRec','infoRec','newRec','sortRec','unHideRec','delRec');
		}

		$this->elementAttr['table'] = ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist typo3-filelist"';
	}




	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/


	/**
	 * Set pathInfo array needed for header control (create folder etc)
	 *
	 * @param	array		$pathInfo
	 * @return	void
	 */
	function setPathInfo($pathInfo)	{
		$this->pathInfo = $pathInfo;
	}






	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/


	/**
	 * Renders the data columns
	 *
	 * @param	array		$item item array
	 * @return	array
	 */
	function getItemColumns ($item) {
		$type = $item['__type'];

			// 	Columns rendering
		$columns = array();
		foreach($this->columnList as $field => $descr)	{

			switch($field)	{
				case 'perms':
					if ($this->showUnixPerms) {
						$columns[$field] = $this->getFilePermString($item[$type.'_perms']);
					}
					else {
						$columns[$field] = (($item[$type.'_readable']) ? 'R' : '').(($item[$type.'_writable']) ? 'W' : '');
					}
				break;
				case 'size':
					if ($type=='file') {
						$columns[$field] = (string)($item[$type.'_size']);
					}
					else {
						$columns[$field] = '';
					}
				break;
				case 'file_type':
					$columns[$field] = strtoupper($item[$field]);
				break;
				case 'mtime':
					$columns[$field] = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $item[$type.'_mtime']);
				break;
				case 'title':
					if ($type=='file') {
						$columns[$field] = $this->linkWrapFile($this->cropTitle($item[$type.'_title'], $field), $item);
					}
					else {
						$columns[$field] = $this->linkWrapDir($this->cropTitle($item[$type.'_title'], $field), $item[$type.'_path_absolute']);
					}
				break;
				case '_CLIPBOARD_':
					$columns[$field] = $this->clipboard_getItemControl($item);
				break;
				case '_CONTROL_':
					 $columns[$field] = $this->getItemControl($item);
					 $this->columnTDAttr[$field] = ' nowrap="nowrap"';
				break;
				default:
					if(isset($item[$type.$field])) {
						$content = $item[$type.$field];
					}
					else {
						$content = $item[$field];
					}
					$columns[$field] = htmlspecialchars(t3lib_div::fixed_lgd($content, $this->titleLength));
				break;
			}
			if ($columns[$field] === '') {
				$columns[$field] = '&nbsp;';
			}
		}

			// Thumbsnails?
		if ($this->showThumbs AND $this->thumbnailPossible($item))	{
			$columns['title'] .= '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item).'</div>';
		}
		if (!$this->showDetailedSize) {
			$columns['size'] = t3lib_div::formatSize($columns['size']);
		}
		return $columns;
	}


	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/


	/**
	 * Renders the action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemAction ($item) {
		return '';
	}


	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon ($item) {
		static $titleNotIndexed;
		static $iconNotIndexed;

		if(!$iconNotIndexed) {
			$titleNotIndexed = 'title="'.$GLOBALS['LANG']->getLL('fileNotIndexed').'"';
			$iconNotIndexed = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/required_h.gif', 'width="10" height="10"').' '.$titleNotIndexed.' alt="" />';
		}

		$type = $item['__type'];

		if ($type == 'file') {
			$titleAttr = '';
			$attachToIcon = '';



				// we don't index indexing setup files
			if ($item['file_name']=='.indexing.setup.xml') {

			} else {

	// TODO doing autoindexing here is ugly - move to somewhere else!!
				if(!($uid = tx_dam::file_isIndexed($item))) {
					$attachToIcon = $iconNotIndexed;
					$titleAttr = $titleNotIndexed;
					if($this->enableAutoIndexing) {
						if ($metaRow = tx_dam::index_autoProcess($item)) {
							$attachToIcon = '';
							$titleAttr = '';
							$item = $metaRow['fields'];
						}
					}
				}
			}



			$icon = tx_dam::icon_getFileType($item);

			$iconTag = tx_dam::icon_getFileTypeImgTag($item, $titleAttr);
			if ($this->clickMenus) $theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($iconTag, $item);
			$iconTag .= $attachToIcon;
		}
		else {

			$icon = tx_dam::icon_getFolder($item);
			$iconTag = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $icon, 'width="18" height="16"').' title="'.htmlspecialchars($item[$type.'_title']).'" alt="" />';
			if ($this->clickMenus) $theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($iconTag, $item);
		}
		return $iconTag;
	}



	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/



	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item)	{
		static $actionCall = array();;

		$content = '';

		if ($this->showControls) {
			if (!is_object($actionCall[$item['__type']])) {
				$actionCall[$item['__type']] = t3lib_div::makeInstance('tx_dam_actionCall');
				$actionCall[$item['__type']]->setRequest('control', array('__type' => $item['__type']), '', $GLOBALS['MCONF']['name']);
				$actionCall[$item['__type']]->setEnv('returnUrl', t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
				$actionCall[$item['__type']]->setEnv('defaultCmdScript', $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php');
// TODO pObj exist?
				$actionCall[$item['__type']]->setEnv('pathInfo', $this->pObj->pathInfo);
				$actionCall[$item['__type']]->initActions(true);
			} elseif ($actionCall[$item['__type']]->itemInfo['__type']!=$item['__type']){
				$actionCall[$item['__type']]->setRequest('control', array('__type' => $item['__type']), '', $GLOBALS['MCONF']['name']);
				$actionCall[$item['__type']]->initActions(true);
			}
// TODO set allow deny: $this->showControls
			$actionCall[$item['__type']]->setRequest('control', $item, '', $GLOBALS['MCONF']['name']);
			$actions = $actionCall[$item['__type']]->renderActionsHorizontal(true);

				// Compile items into a DIV-element:
			$content = '
											<!-- CONTROL PANEL: '.htmlspecialchars($item['file_name']).' -->
											<div class="typo3-DBctrl">'.implode('', $actions).'</div>';
		}

		return $content;

// TODO how to add spacer with actions?
		$actions[] = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clear.gif', 'width="12" height="12"').' alt="" />';

	}


	/**
	 * Creates the control panel for the path: create folder etc.
	 *
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getHeaderControl() {

		$content = '';

		$pathInfo = $this->pathInfo;
		$path = $pathInfo['dir_path_absolute'];

		//
		// actions
		//


// FIXME use API !!!!
		if ($this->showControls AND $pathInfo['dir_writable']) {
			$actions = array();
			$cmd = 'tx_dam_cmd_foldernew';
			$script = $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php?CMD='.$cmd.'&folder='.rawurlencode($path).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
			$actions[] = '<a href="'.htmlspecialchars($script).'">'.
						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], PATH_txdam_rel.'i/new_webfolder.gif', 'width="17" height="12"').' title="'.$GLOBALS['LANG']->getLL('newFolder').'" alt="" valign="top" />'.
						'</a>';

//			$cmd = 'tx_dam_cmd_filenewtextfile';
//			$script = $GLOBALS['BACK_PATH'].PATH_txdam_rel.'mod_cmd/index.php?CMD='.$cmd.'&folder='.rawurlencode($path).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
//			$actions[] = '<a href="'.htmlspecialchars($script).'">'.
//						'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/new_file.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('newTextFile').'" alt="" valign="top" />'.
//						'</a>';


				// Compile items into a DIV-element:
			$content = '
												<!-- CONTROL PANEL: path -->
												<div class="typo3-DBctrlGlobal">'.implode('', $actions).'</div>';

		}
		return $content;

	}






}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listfiles.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listfiles.php']);
}
?>