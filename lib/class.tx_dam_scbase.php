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
 * Contains the parent class for 'ScriptClasses' in DAM backend modules.
 *
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Backend
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  118: class tx_dam_SCbase extends t3lib_SCbase
 *  193:     function init()
 *  295:     function menuConfig()
 *
 *              SECTION: Path related functions
 *  343:     function checkOrSetPath()
 *  380:     function checkPathAccess($pathInfo)
 *
 *              SECTION: GUI misc
 *  405:     function addDocJavaScript ()
 *  412:     function jumpToUrl(URL)
 *  418:     function toggleDisplay(toggleId, e)
 *  460:     function addDocStyles ()
 *  544:     function getResultBrowser()
 *  559:     function getResultInfoBar()
 *  577:     function getResultInfoHeader()
 *  589:     function getHeaderBar($left, $right='')
 *  646:     function contentLeftRight($left,$right)
 *  664:     function old_getHeaderBar($content, $options='')
 *  685:     function getResultInfo($showBrowseResult=true)
 *  721:     function renderResultBrowser($showResultInfo=true, $alwaysPrev=true, $tableParams='cellspacing="5"')
 *  799:     function list_browseresults($tableParams='cellspacing="5"', $alwaysPrev=true)
 *  846:     function getStoreControl()
 *  871:     function getSearchBox($mode='simple', $useFormTag=TRUE, $formAction='')
 *
 *              SECTION: GUI options
 *  912:     function getOptions()
 *  930:     function addOption($type, $paramName, $description, $items=array())
 * 1045:     function getCurrentSelectionBoxItems($sel, $queryType, $rows)
 * 1106:     function linkThisScriptSel($getParams=array())
 * 1123:     function linkThisScriptStraight($params)
 *
 *              SECTION: GUI files and folder
 * 1148:     function getPathInfoHeaderBar($pathInfo, $browsable=TRUE, $extraIconArr=array(), $allowedIcons=NULL)
 * 1190:     function getFolderNavBar($pathInfo, $browsable=true, $allowedIcons=NULL)
 * 1240:     function getBrowseableFolderList ($path, $folderParam='SET[tx_dam_folder]')
 *
 *              SECTION: GUI buttons and icons
 * 1276:     function buttonToggleDisplay($id, $title, $guiElement, $displayOpen=false)
 * 1306:     function button ($iconImgTag, $label, $hoverText, $href, $aTagAttribute='')
 * 1326:     function btn_openMod_inNewWindow($function_name=NULL, $addAttrib='')
 * 1347:     function btn_editRec_inNewWindow($table, $uid, $addAttrib='')
 * 1370:     function btn_removeRecFromSel($table, $uid, $addAttrib='')
 * 1388:     function icon_editRec($table, $uid, $addAttrib='')
 * 1411:     function icon_infoRec($table, $uid, $addAttrib='')
 * 1432:     function btn_back($params=array(), $absUrl='')
 * 1462:     function wrapLink_edit($str, $refTable, $id)
 * 1483:     function wrapLink ($href, $content)
 * 1499:     function getRecordInfoEditLink($refTable, $row, $showRootline=FALSE)
 *
 *              SECTION: misc
 * 1542:     function getFormTag($name='editform')
 * 1568:     function getTabMenu($mainParams, $elementName, $currentValue, $menuItems, $script='', $addparams='')
 *
 * TOTAL FUNCTIONS: 40
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_scbase.php');

require_once(PATH_t3lib.'class.t3lib_modsettings.php');

require_once(PATH_txdam.'lib/class.tx_dam_selectionquery.php');

require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');




/**
 * Parent class for 'ScriptClasses' in DAM backend modules.
 * See DAM modules for examples.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see t3lib_SCbase
 * @package DAM-BeLib
 * @subpackage Backend
 */
class tx_dam_SCbase extends t3lib_SCbase {


	/**
	 * Selection object
	 * @see tx_dam_selectionquerygen
	 */
	 var $selection;

	/**
	 * This is the current path for the file module
	 */
	var $path = '';

	/**
	 * This is the pathInfo array for the current path
	 */
	var $pathInfo = false;






	/**
	 * default pid to store DAM records
	 */
	var $defaultPid;

	/**
	 * storage object
	 */
	var $store;

	/**
	 * last storage message
	 */
	var $storeMsg = '';




	/**
	 * Command icons that shouldn't be displayed
	 */
	var $guiCmdIconsDeny = array();


	/**
	 * Array of HTML which will be print as options form
	 */
	var $modOptions = array();


	/**
	 * Single function
	 */
	var $forcedFunction = '';

	/**
	 * Parameter which should be addded in the current session to every url when the script calls itself. Usefull to add '&forcedFunction=...'
	 * @see t3lib_div::linkThisScript()
	 */
	var $addParams = array();






	/**
	 * Initializes the backend module by setting internal variables
	 *
	 * @return	void
	 */
	function init()	{
		global $TYPO3_CONF_VARS, $FILEMOUNTS;



		parent::init();

		tx_dam::config_init();

#		tx_dam::config_setValue('setup.debug', '1');


			// include the default language file
		$GLOBALS['LANG']->includeLLFile('EXT:dam/lib/locallang.xml');


		//
		// Get current folder
		//

			// tx_dam_folder could be set by GP or stored in module conf
		$SET = t3lib_div::_GP('SET');
		$this->path = $this->MOD_SETTINGS['tx_dam_folder'];

			// check if tx_dam_folder was set by GP which takes precedence, if not use command sent by navframe
			// order: GP (script), SLCMD (navframe), MOD_SETTINGS (stored)
		$SLCMD = t3lib_div::GParrayMerged('SLCMD');
		if (!$SET['tx_dam_folder'] AND is_array($SLCMD['SELECT']) AND is_array($SLCMD['SELECT']['txdamFolder'])) {
			$this->path = tx_dam::path_makeRelative(key($SLCMD['SELECT']['txdamFolder']));
		}
		$this->checkOrSetPath();
		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, array('tx_dam_folder' => $this->path), $this->MCONF['name'], 'ses');


		$this->defaultPid = tx_dam_db::getPid();
		$this->id = $this->defaultPid;


		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->defaultPid, $this->perms_clause);
		$this->calcPerms = $GLOBALS['BE_USER']->calcPerms($this->pageinfo);


		//
		// Detect and set forced single function and set params
		//

			// remove selection command from any params
		$this->addParams['SLCMD'] = '';
		$this->addParams['SET'] = '';

			// forced a module function?
		$forcedFunction = t3lib_div::_GP('forcedFunction');
		if ($this->MOD_MENU['function'][$forcedFunction]) {
			$this->forcedFunction = $forcedFunction;
			$this->addParams['forcedFunction'] = $this->forcedFunction;
			$this->handleExternalFunctionValue('function', $this->forcedFunction);
		}


		//
		// Init selection
		//


		$this->selection = t3lib_div::makeInstance('tx_dam_selectionQuery');

		$this->MOD_SETTINGS['tx_dam_resultPointer'] = $this->selection->initPointer($this->MOD_SETTINGS['tx_dam_resultPointer'], $this->MOD_SETTINGS['tx_dam_resultsPerPage']);

		$this->selection->initSelection($this /*$GLOBALS['SOBE']*/,
										$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['selectionClasses'],
										'tx_dam',
										'tx_dam_select');

		$this->selection->initQueryGen();
		$this->selection->qg->initBESelect('tx_dam', tx_dam_db::getPidList());


		$this->selection->addFilemountsToQuerygen();



			// debug output
		if (tx_dam::config_getValue('setup.debug')) {
			$this->debugContent['query'] = '<h4>MOD_SETTINGS</h4>'.t3lib_div::view_array($this->MOD_SETTINGS);
		}

			// BE Info output
		if (tx_dam::config_getValue('setup.debug') AND t3lib_extMgm::isLoaded('cc_beinfo')) {
			require_once(t3lib_extMgm::extPath('cc_beinfo').'class.tx_ccbeinfo.php');
			$beinfo = t3lib_div::makeInstance('tx_ccbeinfo');
			$beinfoContent = $beinfo->makeInfo($this);
			$this->debugContent['beinfo'] = '<h4>BE Info</h4>'.$beinfoContent;
		}

	}



	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;

		$this->MOD_MENU = array_merge($this->MOD_MENU,
			array(
				'tx_dam_select' => '',	// the current selection
				'tx_dam_select_undo' => '',	// undo data to revert selection changes

				'tx_dam_folder' => '',	// current folder for file operation

				'tx_dam_select_storedSettings' => '',	// t3lib_modsettings

				'tx_damindex_indexSetup' => '',
				'tx_damindex_storedSettings' => '',

				'tx_dam_resultPointer' => '',
				'tx_dam_resultsPerPage' => array(
						20 => '20',
						50 => '50',
						100 => '100',
						200 => '200',
					),
			)
		);
		parent::menuConfig();
	}







	/********************************
	 *
	 * Path related functions
	 *
	 ********************************/




	/**
	 * Checks if $this->path is a path under one of the filemounts
	 *
	 * @return	void
	 * @see init()
	 */
	function checkOrSetPath()	{
		global $FILEMOUNTS;

		if (!$this->path) {
			reset($FILEMOUNTS);
			$fmount = current($FILEMOUNTS);
			$path = $fmount['path'];
		} else {
			$path = tx_dam::path_makeAbsolute($this->path);
		}

		$pathInfo = tx_dam::path_compileInfo($path);

		if ($this->checkPathAccess($pathInfo))	{
			$this->path = $pathInfo['dir_path_relative'];
			$this->pathInfo = $pathInfo;
			$this->pathAccess = true;
		}
		else {
			$this->path = $path;
			$this->pathInfo = $pathInfo;
			$this->pathAccess = false;
		}
// TODO check path access in modules or set path to a valid path?

		if (tx_dam::config_getValue('setup.debug')) {
			$this->debugContent['pathInfo']= '<h4>pathInfo</h4>'.t3lib_div::view_array($this->pathInfo);
		}
	}


	/**
	 * Validating the input path and checking access against the mounts of the user.
	 *
	 * @param	array		$pathInfo PathInfo array
	 * @return	array		PathInfo array or false if invalid
	 */
	function checkPathAccess($pathInfo)	{
		$access = false;

		if ($pathInfo['mount_id']) {
			$access = $pathInfo;
		}
		return $access;
	}




	/********************************
	 *
	 * GUI misc
	 *
	 ********************************/



	/**
	 * Add a bunch of JS code used by different GUI elements to the header.
	 *
	 * @return	void
	 */
	function addDocJavaScript () {
		global $BACK_PATH;

		$this->doc->JScodeArray['jumpToUrl'] = '
			var script_ended = 0;
			var changed = 0;

			function jumpToUrl(URL)	{
				document.location = URL;
			}
			';

		$this->doc->JScodeArray['toggleDisplay'] = '
			function toggleDisplay(toggleId, e) {
				if (!e) {
					e = window.event;
				}
				if (!document.getElementById) {
					return false;
				}
				var body = document.getElementById(toggleId);
				if (!body) {
					return false;
				}
				var image = document.getElementById(toggleId + "_toggle");
				if (body.style.display == "none") {
					body.style.display = "block";
					if (image) {
						image.src = "'.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/button_down.gif', '', 1).'";
					}
				} else {
					body.style.display = "none";
					if (image) {
						image.src = "'.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/button_right.gif', '', 1).'";
					}
				}
				if (e) {
					// Stop the event from propagating, which
					// would cause the regular HREF link to
					// be followed, ruining our hard work.
					e.cancelBubble = true;
					if (e.stopPropagation) {
						e.stopPropagation();
					}
				}
			}
			';
	}


	/**
	 * Add a bunch of CSS styles used by different GUI elements to the header.
	 *
	 * @return	void
	 */
	function addDocStyles () {
		$borderColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor,-30,-30,-30);

		#$this->doc->buttonColor = '#d3cfcb';
		$this->doc->buttonColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor,-30,-30,-30);
		$this->doc->buttonColorAct = '#e7dba8'; #t3lib_div::modifyHTMLcolor($this->doc->bgColor5,25,25,25);
		$this->doc->buttonColorHover = t3lib_div::modifyHTMLcolor($this->doc->buttonColor,-20,-20,-20);
		$this->doc->buttonColorBorder = '#aaa';

		$this->doc->hoverColorTR = t3lib_div::modifyHTMLcolor($this->doc->bgColor,-20,-20,-20);


		$this->doc->inDocStylesArray['gui_general'] = '
				IMG.typo3-icon { vertical-align: middle; margin-right:0.1em; padding:0px 1px 2px 1px; text-decoration: none; }
				IMG.c-recicon { vertical-align: middle; margin-right:0.1em; padding:0px 1px 2px 1px; text-decoration: none; }';

		$this->doc->inDocStylesArray['typo3-dblist_add'] = '
				TABLE.typo3-dblist TR TD.c-headLine { background-color: #e0e0e0; }
				td.c-actionBar { background-color: #e0e0e0; }
				td.c-actionBar div { margin: 2px 0px 2px 0px; }
				td.c-actionBar select { background-color: #e0e0e0; height:2em; font-size:0.95em;}
				td.c-actionBar input { height:2em; font-size:0.95em;}
				.typo3-dblist TD { padding-top: 1px; }
				.typo3-dblist TD.item, .typo3-dblist TD.typo3-dblist-item { border-bottom: 1px dotted '.$borderColor.'; }';

		$this->doc->inDocStylesArray['typo3-filelist'] = '
				.typo3-foldernavbar { margin: 12px 0px 2px 5px; }
				.typo3-topactions { margin: 7px 0px 10px 0px; }
				.typo3-filelist TD { padding-top: 1px;  }
				.typo3-filelist TD.typo3-filelist-item { border-bottom: 1px dotted '.$borderColor.'; }';

		$this->doc->inDocStylesArray['gui_buttons'] = '
				.buttonToggleDisplay { background-color: '.$this->doc->buttonColor.'; border: 1px solid #888; padding: 0px 2px 0px 0px; width:30%; }
				.buttonToggleDisplay:hover { background-color: '.$this->doc->buttonColorHover.'; }
				.buttonToggleDisplay a { text-decoration: none; display: block; }
				.buttonToggleDisplay img { vertical-align: middle; margin-bottom:2px;}

				.guiElementBox { background-color: '.$this->doc->bgColor4.'; border: 1px solid #aaa; padding: 2px; }

				span.spacer1em { width:1em; height:1em; }
				span.spacer2em { width:2em; height:1em; }
				span.spacer3em { width:3em; height:1em; }

				.button { white-space:nowrap; padding: 1px 4px 2px 2px; background-color: '.$this->doc->buttonColor.'; border: 1px solid '.$this->doc->buttonColorBorder.'; margin-left: 0.5em; margin-right: 0.55em }
				.buttonAct { background-color: '.$this->doc->buttonColorAct.'; }
				.button img { vertical-align: middle; margin-right:0.1em; padding:0px 1px 2px 1px; }
				.button a { vertical-align: baseline; text-decoration:none; }
				.button:hover { background-color: '.$this->doc->buttonColorHover.'; }
				.bgColorBtn { background-color: '.$this->doc->buttonColor.'; }';
// was: .button { xvertical-align: middle;
		$this->doc->inDocStylesArray['list_browseresults'] = '
				.browsebox {  }
				.browsebox TD, .browsebox TD P { line-height:1em; padding:0; font-size:0.8em; }
				.browsebox TD.browsebox-Cell, .browsebox TD.browsebox-CellA { border: 1px solid #aaa; width:1.6em; text-align:center; background-color:'.$this->doc->buttonColor.'; padding: 1px 0.7em 1px 0.7em; }
				.browsebox TD.browsebox-CellA:hover { background-color: '.$this->doc->buttonColorHover.'; }
				.browsebox TD.browsebox-Select { }
				.browsebox TD.browsebox-Select SELECT { padding:0; font-size:0.8em; width:100%; font-weight:normal; border: 1px solid #aaa; background-color:'.$this->doc->buttonColor.'; }
				.browsebox TD P { font-weight:bold; color:#888; }
				.browsebox TD.browsebox-CellA P A { font-weight:bold; color:#000; text-decoration:none; display:block; width:auto; }';
		$this->doc->inDocStylesArray['list_browseresults'] = '
				.browsebox {  }
				.browsebox TD, .browsebox TD P { padding:0; font-size:0.9em; line-height:1em; }
				.browsebox TD { padding:0; height:2em; font-size:0.9em; }
				.browsebox TD.browsebox-Cell, .browsebox TD.browsebox-CellA { border: 1px solid #aaa; width:1.6em; text-align:center; background-color:'.$this->doc->buttonColor.'; padding: 1px 0.7em 1px 0.7em; }
				.browsebox TD.browsebox-CellA:hover { background-color: '.$this->doc->buttonColorHover.'; }
				.browsebox TD.browsebox-Select { }
				.browsebox TD.browsebox-Select SELECT { padding:0; font-size:1em; width:100%; height:2em; font-weight:normal; border: 1px solid #aaa; background-color:'.$this->doc->buttonColor.'; }
				.browsebox TD P { font-weight:bold; color:#888; }
				.browsebox TD.browsebox-CellA P A { font-weight:bold; color:#000; text-decoration:none; display:block; width:auto; }';

			// in typo3/stylesheets.css css is defined with id instead of a class: TABLE#typo3-tree
			// that's why we need TABLE.typo3-browsetree
		$this->doc->inDocStylesArray['typo3-browsetree'] = '
					/* Trees */
			TABLE.typo3-browsetree A { text-decoration: none;  }
			TABLE.typo3-browsetree TR TD { white-space: nowrap; vertical-align: middle; }
			TABLE.typo3-browsetree TR TD IMG { vertical-align: middle; }
			TABLE.typo3-browsetree TR TD IMG.c-recIcon { margin-right: 1px;}
			TABLE.typo3-browsetree { margin-bottom: 10px; width: 95%; }

			TABLE.typo3-browsetree TR TD.typo3-browsetree-control {
				padding: 0px;
			}
			TABLE.typo3-browsetree TR TD.typo3-browsetree-control a {
				padding: 0px 3px 0px 3px;
				background-color: '.$this->doc->buttonColor.';
			}
			TABLE.typo3-browsetree TR TD.typo3-browsetree-control > a:hover {
				background-color:'.$this->doc->buttonColorHover.';
			}';


		$this->doc->inDocStylesArray['uploads'] = '
			#c-override, #c-upload, #c-submit { margin: 0.3em 0 1em 0; }
			#c-upload input { margin: 0 0 0.3em 0; }';

		$this->doc->inDocStylesArray['mod_cmd'] = '
				#c-select { padding-bottom:0.8em; }
				#c-createFolders div { margin-bottom:0.8em; }';
	}



	/**
	 * shows a result browser
	 * "page1|page2
	 *
	 * @return	string
	 */
	function getResultBrowser() {
		global $LANG;

		$content = '';
		if($this->selection->pointer->countTotal > $this->selection->pointer->itemsPerPage) {
			$content = $this->renderResultBrowser();
		}
		return $content;
	}

	/**
	 * shows a result information and a "results per page" selector
	 *
	 * @return	string
	 */
	function getResultInfoBar($left = array()) {
		global $LANG;


		$left[] = $this->getResultInfo();
		if($this->selection->pointer->lastPage>0) {
			$left[] .= $this->renderResultBrowser();
		}
		$menu = t3lib_BEfunc::getFuncMenu($this->addParams, 'SET[tx_dam_resultsPerPage]', $this->MOD_SETTINGS['tx_dam_resultsPerPage'], $this->MOD_MENU['tx_dam_resultsPerPage']);

		return $this->getHeaderBar($left, sprintf($LANG->getLL('recordsPerPage'), $menu));
	}

	/**
	 * shows a result information
	 *
	 * @return	string
	 */
	function getResultInfoHeader() {
		$content = $this->getResultInfo();
		return $this->getHeaderBar($content);
	}

	/**
	 * shows a result information and a "results per page" selector
	 *
	 * @param	string		$content: Content
	 * @param	string		$options: options for $this->doc->funcMenu()
	 * @return	string
	 */
	function getHeaderBar($left, $right='') {

		$contentLeft = '';
		$contentRight = '';

		if ($left) {
			if (!is_array($left)) {
				$left = array($left);
			}
			foreach ($left as $leftContent) {
				$contentLeft .= '<div class="infobar-td">'.$leftContent.'</div>';
			}
			$contentLeft = '<div class="infobar-left-table">'.$contentLeft.'</div>';
		}

		if ($right) {
			if (!is_array($right)) {
				$right = array($right);
			}
			foreach ($right as $rightContent) {
				#$contentRight .= '<div class="infobar-td"><div style="height:100%;">'.$rightContent.'</div></div>';
				$contentRight .= '<div class="infobar-td">'.$rightContent.'</div>';
			}
			$contentRight = '<div class="infobar-right-table">'.$contentRight.'</div>';
			if ($contentLeft=='')
			$contentLeft = '<span>&nbsp;</span>';
		}

		if ($contentLeft AND $contentRight) {
			$content = $this->contentLeftRight($contentLeft,$contentRight);
		} else {
			$content = $contentLeft.$contentRight;
		}

		$bgColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor4,0,0,0);
		$this->doc->inDocStylesArray['getHeaderBar'] = '
			div.infobar { display:block; width:100%; min-height:2.5em; background-color:'.$bgColor.'; border-bottom: 1px solid #888; }

			div.infobar>table, div.infobar>div, div.infobar>table>td>div { vertical-align:middle; height:2.5em; line-height:2.5em; }
			div.infobar, div.infobar table td, div.infobar-td, div.infobar-td>span, div.infobar-td>div { vertical-align:middle; }
			div.infobar-left-table  { display:table; height:2.5em; }
			div.infobar-right-table { display:table; height:2.5em; }
			div.infobar-td { display:table-cell; vertical-align:middle; height:2.5em; }
			div.infobar-left-table div.infobar-td { padding-left:0.5em; }
			div.infobar-right-table div.infobar-td { padding-right:0.5em; text-align:right; }
			div.infobar-right-table div.infobar-td select { vertical-align:middle; }

			div.infobar-extraline { padding:0.2em; padding-left:0.5em; background-color:'.$bgColor.'; }
			';

		return '<div class="infobar">'.$content.'</div>';
	}


	/**
	 * Returns a one-row/two-celled table with content side by side.
	 * The table is a 100% width table and each cell is aligned left / right
	 *
	 * @param	string		Left cell content
	 * @param	string		Right cell content
	 * @return	string		HTML output
	 */
	function contentLeftRight($left,$right)	{
		return '
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="middle" nowrap="nowrap">'.$left.'</td>
					<td valign="middle" width="1%" style="text-align:right;" align="right" nowrap="nowrap">'.$right.'</td>
				</tr>
			</table>';
	}


	/**
	 * Render a box with a header and a message
	 *
	 * @param	string		$headerContent
	 * @param	mixed		$msg The message. Have to be htmlspechialchars() already. If it is an array the elements will be rendered in <p> tags.
	 * @param	string		$buttons Buttons - right aligned. Have to be htmlspechialchars() already.
	 * @param	integer		$iconThe number of an icon to show with the header (see the icon-function). -1,1,2,3
	 * @return	string		HTML output
	 */
	function getMessageBox ($headerContent, $msg, $buttons='', $icon=0) {
		$content = '';

		if ($headerContent OR $icon) {
			$content .= '<h3>'.$this->doc->icons($icon).htmlspecialchars($headerContent).'</h3>';
		}
		if (is_array($msg)) {
			$msg = '<p>'.implode('</p><p>', $msg).'</p>';
		}
		$content .= '<div class="msgboxContent">'.$msg.'</div>';

		if ($buttons) {
			$content .= '<div class="msgboxButtons">'.$buttons.'</div>';
		}

		$this->doc->inDocStylesArray['getHeaderBar'] = '
			div.msgbox-wrap { display:block; width:auto; padding:1.5em; }
			div.msgbox { display:block; width:auto; border: 1px solid #888; }
			div.msgboxContent { display:block; width:auto; padding:1.5em;  }
			div.msgboxButtons { display:block; text-align:right; padding:1.5em;  }';

		return '<div class="msgbox-wrap"><div class="msgbox bgColor-10">'.$content.'</div></div>';
	}


	/**
	 * shows a result information and a "results per page" selector
	 *
	 * @param	string		$content: Content
	 * @param	string		$options: options for $this->doc->funcMenu()
	 * @return	string
	 */
	function old_getHeaderBar($content, $options='') {
		$content = $options ? $this->doc->funcMenu($content, $options) : $content;
		$bgColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor4,0,0,0);
		$this->doc->inDocStylesArray['getHeaderBar'] = '
				div.infobar { display:table; width:100%; height:3em; background-color:'.$bgColor.'; border-bottom: 1px solid #888; }
				div.infobar div.infobar-content { display:table-cell; width:100%; vertical-align:middle; padding: 2px 5px 2px 5px; }
				div.infobar div.infobar-content td { vertical-align:middle; }';

		return '<div class="infobar"><div class="infobar-content">'.$content.'</div></div>';
	}



	/**
	 * shows an info bar about the current selection
	 * "1-20 of 76"
	 * "16 records found."
	 *
	 * @param 	boolean 	$showBrowseResult If set (is default) "1-20 of 76" will be shown when needed, otherwise "16 records found."
	 * @return	string
	 */
	function getResultInfo($showBrowseResult=true) {
		global $LANG;

		if($this->selection->pointer->countTotal) {

			if($this->selection->pointer->countTotal == 1) {
				$content = sprintf($LANG->getLL('oneRecordInSelection'));
			}
			elseif (($showBrowseResult==false) OR ($this->selection->pointer->countTotal <= $this->selection->pointer->itemsPerPage)) {
				$content = sprintf($LANG->getLL('recordsInSelection'), $this->selection->pointer->countTotal);
			}
			else {
				$part = ($this->selection->pointer->page*$this->selection->pointer->itemsPerPage);
				$part = ($part+1).'-'.min($this->selection->pointer->countTotal,($part+$this->selection->pointer->itemsPerPage));
				$content = sprintf($LANG->getLL('recordsFromSelection'), $part, $this->selection->pointer->countTotal);
			}
		}
		elseif(!$this->selection->sl->hasSelection()) {
			$content = $LANG->getLL('noSelection');
		}
		else {
			$content = $LANG->getLL('noRecordsInSelection');
		}

		return '<span>'.htmlspecialchars($content).'</span>';
	}

	/**
	 * Returns a results browser. This means a bar of page numbers plus a "previous" and "next" link.
	 * Using $this->selection->pointer->page as pointer to the page to display
	 *
	 * @param	boolean		$showResultInfo If set an info about the result set will be prepended
	 * @param	boolean		$alwaysPrev If set show always previous/forward browse links no matter if last or first page is active
	 * @param	string		$tableParams Attributes for the table tag which is wrapped around the table cells containing the browse links
	 * @return	string		Output HTML, wrapped in <div>-tags with a class attribute
	 */
	function renderResultBrowser($showResultInfo=true, $alwaysPrev=true, $tableParams='cellspacing="5"')	{

		$links = array();

		if ($alwaysPrev)	{
			$class = 'browsebox-Cell';
			$href = '';
			if ($this->selection->pointer->page > 0)	{
				$class = 'browsebox-CellA';
				$href = t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($this->selection->pointer->firstPage)));
			}
			$links[] = '<td class="'.$class.'" nowrap="nowrap"><p>'.$this->wrapLink($href, htmlspecialchars('|<')).'</p></td>';
		}

		if ($alwaysPrev)	{
			$class = 'browsebox-Cell';
			$href = '';
			if ($this->selection->pointer->page > 0)	{
				$class = 'browsebox-CellA';
				$href = t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($this->selection->pointer->getPagePointer(-1))));
			}
			$links[] = '<td class="'.$class.'" nowrap="nowrap"><p>'.$this->wrapLink($href, htmlspecialchars('<')).'</p></td>';
		}


		$options = array();
		for($a=0; $a <= $this->selection->pointer->lastPage; $a++)	{
			$options[] = '<option value="'.htmlspecialchars((string)$a).'"'.($this->selection->pointer->page == $a?' selected="selected"':'').'>'.
							t3lib_div::deHSCentities(htmlspecialchars($a+1)).
							'</option>';
		}
		if (count($options))	{
			$onChange = 'jumpToUrl(\''.t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>((string)$a))).'&SET[tx_dam_resultPointer]=\'+this.options[this.selectedIndex].value,this);';
			$links[] = '<td class="browsebox-Select" nowrap="nowrap"><p><select name="_tx_dam_resultPointer" onchange="'.htmlspecialchars($onChange).'">
					'.implode('
					',$options).'
				</select></p></td>';
		}


		if ($alwaysPrev)	{
			$class = 'browsebox-Cell';
			$href = '';
			if ($this->selection->pointer->page < $this->selection->pointer->lastPage)	{
				$class = 'browsebox-CellA';
				$href = t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($this->selection->pointer->getPagePointer(1))));
			}
			$links[] = '<td class="'.$class.'" nowrap="nowrap"><p>'.$this->wrapLink($href, htmlspecialchars('>')).'</p></td>';
		}

		if ($alwaysPrev)	{
			$class = 'browsebox-Cell';
			$href = '';
			if ($this->selection->pointer->page < $this->selection->pointer->lastPage)	{
				$class = 'browsebox-CellA';
				$href = t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($this->selection->pointer->lastPage)));
			}
			$links[] = '<td class="'.$class.'" nowrap="nowrap"><p>'.$this->wrapLink($href, htmlspecialchars('>|')).'</p></td>';
		}

		$sTables = '<span class="browsebox">'.
		'<'.trim('table '.$tableParams).'>
			<tr>'.implode('', $links).'</tr>
		</table></span>';


		return $sTables;
	}

	/**
	 * Returns a results browser. This means a bar of page numbers plus a "previous" and "next" link.
	 * Using $this->selection->pointer->page as pointer to the page to display
	 *
	 * @param	string		$tableParams Attributes for the table tag which is wrapped around the table cells containing the browse links
	 * @param	boolean		$alwaysPrev If set show always previous/forward browse links no matter if last or first page is active
	 * @return	string		Output HTML, wrapped in <div>-tags with a class attribute
	 * @deprecated version - 06.04.2006
	 */
	function list_browseresults($tableParams='cellspacing="5"', $alwaysPrev=true)	{

		$links = array();
			// Make browse-table/links:
		if ($alwaysPrev >= 0)	{
			if ($this->selection->pointer->page > 0)	{
				$links[] = '<td class="browsebox-Cell" nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($this->selection->pointer->page-1)))).'">'.htmlspecialchars('<').'</a>'.
				'</p></td>';
			}
			elseif ($alwaysPrev)	{
				$links[] = '<td class="" nowrap="nowrap"><p>'.htmlspecialchars('<').'</p></td>';
			}
		}

		for($a=0; $a <= $this->selection->pointer->lastPage; $a++)	{
			$links[] = '<td '.($this->selection->pointer->page == $a ? 'class="browsebox-SCell"' : 'class="browsebox-Cell"').' nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>((string)$a)))).'">'.htmlspecialchars($a+1).'</a>'.
				'</p></td>';
		}

		if ($this->selection->pointer->page < $this->selection->pointer->lastPage)	{
			$links[] = '<td class="browsebox-Cell" nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]' => $this->selection->pointer->page+1))).'">'.htmlspecialchars('>').'</a>'.
				'</p></td>';
		}
		elseif ($alwaysPrev)	{
				$links[] = '<td class="" nowrap="nowrap"><p>'.htmlspecialchars('>').'</p></td>';
		}

		$sTables = '<div class="browsebox">'.
		'<'.trim('table '.$tableParams).'>
			<tr>'.implode('', $links).'</tr>
		</table></div>';


		return $sTables;
	}


	/**
	 * Creates the search box
	 *
	 * @param	string		Mode. Currently only 'simple' is supported
	 * @param	boolean		If true, the search box is wrapped in its own form-tags
	 * @return	string		HTML
	 */
	function getStoreControl()	{
		global $LANG;

		$content = '';

		if(is_object($this->store)) {
			$content.= '<p><strong>'.$LANG->getLL('selectionClipboard',1).'</strong></p>'.$this->store->getStoreControl();
		}
		if(is_object($this->selExport)) {
			if($content) {
				$content.= '</div><div class="guiElementBox" style="margin-top: 3px;">';
			}
			$content.= '<p><strong>'.$LANG->getLL('selectionExport',1).'</strong></p>'.$this->selExport->getStoreControl();
		}
		return $this->buttonToggleDisplay('storeControl', $LANG->getLL('selection',1), $content);
	}

	/**
	 * Creates the search box
	 *
	 * @param	string		Mode. Currently only 'simple' is supported
	 * @param	boolean		If true, the search box is wrapped in its own form-tags
	 * @param	string		The action target for the form. Default is this script.
	 * @return	string		HTML for the search box
	 */
	function getSearchBox($mode='simple', $useFormTag=TRUE, $formAction='')	{

			// Setting form-elements, if applicable:
		$formElements = array('', '');
		if ($useFormTag)	{
			$formAction = $formAction ? $formAction : $this->linkThisScriptSel($this->addParams);
			$formElements = array('<form action="'.htmlspecialchars($formAction).'" method="post">', '</form>');
		}

			// Table with the search box:
		$content.= $formElements[0].'
				<!--
					Search box:
				-->
				<table border="0" cellpadding="2" cellspacing="0" class="bgColor4">
					<tr>
						<td> '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.enterSearchString',1).' <input type="text" name="SLCMD[SEARCH][txdamStrSearch][0]" value="'.htmlspecialchars($this->selection->sl->sel['SEARCH']['txdamStrSearch'][0]).'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(10).' /></td>
						<td><input type="submit" name="search" value="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.search',1).'" /></td>
					</tr>
				</table>
			'.$formElements[1];

		return $this->buttonToggleDisplay('searchbox', $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.search',1), $content, $this->selection->sl->sel['SEARCH']['txdamStrSearch'][0]);
	}



	/********************************
	 *
	 * GUI options
	 *
	 ********************************/




	/**
	 * Creates the options form
	 *
	 * @return	string		HTML for the search box
	 */
	function getOptions() {
		global $LANG;

		if (count($this->modOptions)) {
			# return $this->doc->spacer(15).$this->doc->section($LANG->getLL('options').':',implode('<br />', $this->modOptions),0,0);
			return $this->buttonToggleDisplay('options', $LANG->getLL('options'), '<div>'.implode('<br />', $this->modOptions).'</div>');
		}
	}

	/**
	 * Add options
	 *
	 * @param	string		$type Option type: html, funcCheck, funcMenu, funcInput
	 * @param	string		$paramName Name of the MOD_MENU/MOD_SETTINGS parameter
	 * @param	string		$description Description as plain text or as HTML for html type
	 * @param	array		$items for funcMenu
	 * @return	void
	 */
	function addOption($type, $paramName, $description, $items=array()) {
		$id = 'l'.uniqid('tx_dam_scbase');
		$idAttr = ' id="'.$id.'"';
		$descriptionLabel = htmlspecialchars($description);
		$descriptionLabel = '<label for="'.$id.'">'.$descriptionLabel.'</label>';

		switch ($type) {
		case 'funcCheck':
			$this->modOptions[$paramName] = t3lib_BEfunc::getFuncCheck($this->addParams, 'SET['.$paramName.']', $this->MOD_SETTINGS[$paramName]).' '.$descriptionLabel;
			$this->modOptions[$paramName] = str_replace('<input', '<input'.$idAttr, $this->modOptions[$paramName]);
		break;
		case 'funcInput':
			$this->modOptions[$paramName] = $descriptionLabel.' '.t3lib_BEfunc::getFuncInput($this->addParams, 'SET['.$paramName.']', $this->MOD_SETTINGS[$paramName]);
			$this->modOptions[$paramName] = str_replace('<input', '<input'.$idAttr, $this->modOptions[$paramName]);
		break;
		case 'funcMenu':
			$this->modOptions[$paramName] = $descriptionLabel.' '.t3lib_BEfunc::getFuncMenu($this->addParams, 'SET['.$paramName.']', $this->MOD_SETTINGS[$paramName], $items);
			$this->modOptions[$paramName] = str_replace('<select', '<select'.$idAttr, $this->modOptions[$paramName]);
		break;
		case 'html':
			$this->modOptions[$paramName] = $description;
		break;
		}
	}





	/********************************
	 *
	 * GUI selection
	 *
	 ********************************/


	/**
	 * Current selection box
	 *
	 * @param	string		$showElements Comma list of elements to be shown
	 * @return	string
	 */
	function getCurrentSelectionBox($showElements='box,btn_revert') { // $showElements has no function for now
		global $LANG, $BACK_PATH;

		$content = '';

		$showElements = explode(',', $showElements);

		if (in_array('box', $showElements)) {
			$rows = array();
			$lastHeader = '';
			$headBbgColor = ' bgColor="'.$GLOBALS['SOBE']->doc->bgColor6.'"';
			foreach (array('SELECT','OR','AND','NOT','SEARCH') as $queryType) {
				if(is_array($this->selection->sl->sel[$queryType])) {

					switch($queryType) {
						case 'SELECT':
						case 'OR':
							if($lastHeader!='SELECT') {
								$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/plus_16.gif', 'width="12" height="16"').' class="absmiddle" alt="" />';
								$rows[] = '<td'.$headBbgColor.' colspan="3" valign="middle"><strong>'.$icon.' &nbsp;'.
								$LANG->getLL('selEquals').'</strong></td>';
							}
							$lastHeader = 'SELECT';
							$rows = $this->getCurrentSelectionBoxItems($this->selection->sl->sel, $queryType, $rows);
						break;

						case 'AND':
							$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/equals_16.gif', 'width="12" height="16"').' class="absmiddle" alt="" />';
							$rows[] = '<td'.$headBbgColor.' colspan="3" valign="middle"><strong>'.$icon.' &nbsp;'.
							$LANG->getLL('selPlus').'</strong></td>';
							$rows = $this->getCurrentSelectionBoxItems($this->selection->sl->sel, $queryType, $rows);
						break;

						case 'NOT':
							$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/minus_16.gif', 'width="12" height="16"').' class="absmiddle" alt="" />';
							$rows[] = '<td'.$headBbgColor.' colspan="3" valign="middle"><strong>'.$icon.' &nbsp;'.
							$LANG->getLL('selMinus').'</strong></td>';
							$rows = $this->getCurrentSelectionBoxItems($this->selection->sl->sel, $queryType, $rows);
						break;

						case 'SEARCH':
							$rows[] = '<td'.$headBbgColor.' colspan="3" valign="middle"><strong> '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.search',1).'</strong></td>';
							$rows = $this->getCurrentSelectionBoxItems($this->selection->sl->sel, $queryType, $rows);
						break;

						default:
							$rows[] = '<td'.$headBbgColor.' colspan="3" valign="middle"><strong> '.$queryType.'</strong></td>';
							$rows = $this->getCurrentSelectionBoxItems($this->selection->sl->sel, $queryType, $rows);
						break;
					}
					$rows[] = '<td colspan="3"><span></span></td>';
				}
			}
			$content .= '<table cellspacing="1" cellpadding="2" border="0" width="100%"><tr>'.implode('</tr><tr>', $rows).'</tr></table>';
		}
		if (in_array('btn_revert', $showElements)) {
			$content .= '<input type="submit" value="'.$LANG->getLL('revertSelection',1).'" name="'.$this->selection->sl->paramPrefix.'_undo" />';
		}
		return $content;
	}

	/**
	 * Current selection box items
	 *
	 * @param array selection
	 * @param	string
	 * @param	array		table rows
	 * @return	array		table rows
	 */
	function getCurrentSelectionBoxItems($sel, $queryType, $rows) {
		global $LANG, $BACK_PATH, $BE_USER;
		static $selClasses = array();

		$rowBbgColor = ' bgColor="'.t3lib_div::modifyHTMLColor($GLOBALS['SOBE']->doc->bgColor4,+10,+10,+10).'"';

		foreach ($sel[$queryType] as $selectionRuleName => $items) {
			if(is_array($items)) {
				foreach($items as $id => $value) {
					if($value) {
						$categoryTitle = '';
						$deselectValue = '0';

						if (!is_object($selClasses[$selectionRuleName]) AND $this->selection->sl->selectionClasses[$selectionRuleName]) {
							if (is_object($obj = &t3lib_div::getUserObj($this->selection->sl->selectionClasses[$selectionRuleName], 'user_',TRUE)))	{
								$selClasses[$selectionRuleName] = &$obj;
							}
						}
						if (is_object($selClasses[$selectionRuleName])) {
							$categoryTitle = $selClasses[$selectionRuleName]->getTreeTitle();
							$itemTitle = $selClasses[$selectionRuleName]->selection_getItemTitle($id, $value);
							$itemIcon = $selClasses[$selectionRuleName]->selection_getItemIcon($id, $value);
							$deselectValue = $selClasses[$selectionRuleName]->deselectValue;

						}

						if(!((string)$categoryTitle == '')) {
							$params = array('SLCMD['.$queryType.']['.$selectionRuleName.']['.(string)$id.']' => $deselectValue);
							$actionIcon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_remove.gif', 'width="11" height="10"').' title="'.$LANG->getLL('remove',1).'" alt="" />';
							$actionIcon = '<a href="'.htmlspecialchars($this->linkThisScriptSel($params)).'">'.$actionIcon.'</a>';

							$rows[] = '<td width="1%"'.$rowBbgColor.'>'.$actionIcon.'</td><td nowrap="nowrap"'.$rowBbgColor.'>'.$categoryTitle.'</td><td width="70%"'.$rowBbgColor.'>'.$itemIcon.htmlspecialchars(t3lib_div::fixed_lgd_cs($itemTitle, $BE_USER->uc['titleLen'])).'</td>';
						}

					}
				}
			}
		}

		return $rows;
	}

	/**
	 * Returns the link-url to the current script.
	 * In $getParams you can set associative keys corresponding to the GET-vars you wish to add to the URL.
	 *
	 * @param	array		Array of GET parameters to include
	 * @return	string
	 */
	function linkThisScriptSel($getParams=array())	{

		$parts = t3lib_div::getIndpEnv('SCRIPT_NAME');
		$params = t3lib_div::_GET();
		$params = t3lib_div::array_merge_recursive_overrule($params, $getParams);
		$pString = t3lib_div::implodeArrayForUrl('', $params, $str='', $skipBlank=0, $rawurlencodeParamName=true);

		return $pString ? $parts.'?'.preg_replace('#^&#', '', $pString) : $parts;
	}

	/**
	 * Returns the link-url to the current script.
	 * In $params you can set associative keys corresponding to the GET-vars you wish to add to the URL.
	 *
	 * @param	array		$params Array of GET parameters to include
	 * @return	string
	 */
	function linkThisScriptStraight($params)	{
		$parts = t3lib_div::getIndpEnv('SCRIPT_NAME');
		$pString = t3lib_div::implodeArrayForUrl('',$params);

		return $pString ? $parts.'?'.ereg_replace('^&','',$pString) : $parts;
	}



	/********************************
	 *
	 * GUI files and folder
	 *
	 ********************************/


	/**
	 * Output header with path info and folder browser
	 *
	 * @param	string		$pathInfo Path to show
	 * @param	boolean		$browsable Define if the info header includes browsable links
	 * @param	array		$extraIconArr Array of icons with extra HTML code which should be shown additionally at the end of the bar. Could be also a list of icon keys which should not be shown - is the same like array with empty value of a key
	 * @param	mixed		$allowedIcons Show the icon keys which are allowed to show (array or comma list)
	 * @return	string		HTML content
	 */
	 function getPathInfoHeaderBar($pathInfo, $browsable=TRUE, $extraIconArr=array(), $allowedIcons=NULL) {
	 	global $LANG, $BACK_PATH, $FILEMOUNTS;

		$pathInfo = is_array($pathInfo) ? $pathInfo : tx_dam::path_compileInfo($pathInfo);

		$fileheader = $this->getFolderNavBar($pathInfo, $browsable, $allowedIcons);

		$cmdIcons = array();
		$cmdIconRight = array();
		$extraIconArr = is_array($extraIconArr) ? $extraIconArr : t3lib_div::trimExplode(',', $extraIconArr,TRUE);
		$allowedIcons = is_null($allowedIcons) ? ('up,refresh,popup,'.implode(',', array_keys($extraIconArr))) : $allowedIcons;
		$allowedIcons = is_array($allowedIcons) ? $allowedIcons : t3lib_div::trimExplode(',', $allowedIcons,TRUE);

		$allowedIcons = array_diff($allowedIcons, $this->guiCmdIconsDeny);

		if ($browsable AND in_array('popup', $allowedIcons)) {
				// open in new window button
			if (!$this->forcedFunction) {
				$cmdIconRight['popup'] = $this->btn_openMod_inNewWindow();
			}
		}
			// put func menu to the end
		if ($extraIconArr['funcMenu']) {
				$cmdIconRight['funcMenu'] = $extraIconArr['funcMenu'];
				unset($extraIconArr['funcMenu']);
		}

		$cmdIcons = t3lib_div::array_merge_recursive_overrule($cmdIcons, $extraIconArr);
		$cmdIcons = t3lib_div::array_merge_recursive_overrule($cmdIcons, $cmdIconRight);

		return $this->getHeaderBar($fileheader, implode('<span class="spacer2em"><span>', $cmdIcons));
	}


	/**
	 * Makes the code for the foldericon in the top
	 *
	 * @param	array		$pathInfo Path info array: $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$browsable If set the path is browsable to navigate into the folders.
	 * @param	string		$allowedIcons Comma list that defines the icons to display.
	 * @return	string		HTML code
	 */
	function getFolderNavBar($pathInfo, $browsable=true, $allowedIcons=NULL)	{
		global $BACK_PATH, $LANG;

		if (is_array($pathInfo))	{

			$allowedIcons = is_null($allowedIcons) ? 'up,refresh' : $allowedIcons;
			$allowedIcons = is_array($allowedIcons) ? $allowedIcons : t3lib_div::trimExplode(',', $allowedIcons,TRUE);

			$allowedIcons = array_diff($allowedIcons, $this->guiCmdIconsDeny);


				// refresh button
			if ($browsable AND in_array('refresh', $allowedIcons)) {
				$icon = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/refresh_n.gif', 'width="14" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.reload',1).'" class="absmiddle" alt="" />';
				$elements['refresh'] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('unique' => uniqid('tx_dam_scbase')))).'">'.$icon.'</a>';
				$elements['refreshSpacer'] = '<span class="spacer2em"><span>';
			}

				// folder up button
			if ($browsable AND in_array('up', $allowedIcons) AND $pathInfo['mount_id'] AND strcmp($pathInfo['mount_path'], $pathInfo['dir_path_absolute']))	{
				$icon = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/i/folder_up.gif', 'width="18" height="16"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.upOneLevel',1).'" class="absmiddle" alt="" />';
				$elements['up'] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_folder]' => dirname($pathInfo['dir_path_absolute'])))).'">'.$icon.'</a>';
			}

			$iconFolder = tx_dam::icon_getFolder($pathInfo);
			$elements['icon'] = '<img'.t3lib_iconWorks::skinImg($BACK_PATH, $iconFolder, 'width="18" height="16"').' alt="" />';

			$elements['path'] = tx_dam_guiFunc::getPathBreadcrumbMenu($pathInfo, $browsable, $maxLength=35);

			$out = '

		<!--
			Page header for file list
		-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-pathBreadcrumbBar">
					<tr><td>'.implode('</td><td>', $elements).'</td></tr>
				</table>';

		}
		return $out;
	}


	/**
	 * Creates a browsable file/folder list
	 *
	 * @param	string		$path Path
	 * @param	string		$folderParam Parameter name to be used for folder browsing
	 * @return	string		Output
	 */
	function getBrowseableFolderList ($path, $folderParam='SET[tx_dam_folder]') {
		global $TYPO3_CONF_VARS;

		$content = '';

		require_once (PATH_txdam.'lib/class.tx_dam_filebrowser.php');
		$filelist = t3lib_div::makeInstance('tx_dam_filebrowser');
		$filelist->SOBE = &$this;
		$filelist->paramName['setFolder'] = $folderParam;
		$content.= $filelist->getBrowseableFolderList(tx_dam::path_makeAbsolute($path));

		return $content;
	}






	/********************************
	 *
	 * GUI buttons and icons
	 *
	 ********************************/



	/**
	 * Renders a box which can be toggled to be expanded or shrinked to display or hide the content inside.
	 *
	 * @param	string		$id Unique id for the box. Needs to be CSS valid.
	 * @param	string		$title Title/label
	 * @param	string		$guiElement The content inside the box
	 * @param	boolean		$displayOpen When set the box is initially open
	 * @return	string HTML content
	 * @see addDocStyles()
	 */
	function buttonToggleDisplay($id, $title, $guiElement, $displayOpen=false) {
		global $BACK_PATH;

		$this->addDocJavaScript();
		$this->addDocStyles();

		$content = '';
		$content.= '<div style="display:block; margin: 1em 0 1em 0;">
						<div class="buttonToggleDisplay"><a href="#" onclick="toggleDisplay(\''.$id.'\', event);return false;" style="white-space:nowrap;">
						<img id="'.$id.'_toggle" '.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/button_right.gif', 'width="11" height="10"').' alt="" />
						'.$title.'</a></div>';

		$displayOpen = $displayOpen ? 'block' : 'none';
		$content.= '<div id="'.$id.'" style="display:'.$displayOpen.';"><div class="guiElementBox">'.$guiElement.'</div></div>';
		$content.= '</div>';
		return $content;
	}



	/**
	 * Render a GUI button in HTML
	 *
	 * @param	string		$iconImgTagReady Icon image tag. Might contain title="", if not the hover text will be inserted.
	 * @param	string		$label The button label. Expected to be already htmlspecialchars().
	 * @param	string		$hoverText The hover text. Expected to be already htmlspecialchars().
	 * @param	string		$href The url
	 * @param	string		$aTagAttribute Additional A tag attribute (eg onclick="").
	 * @return	string		Button as HTML
	 */
	function button ($iconImgTag, $label, $hoverText, $href, $aTagAttribute='') {
		$this->addDocStyles();

		$aTagAttribute = $aTagAttribute ? ' '.$aTagAttribute : '';
		$aTag = '<a href="'.htmlspecialchars($href).'"'.$aTagAttribute.'>';
		if ($iconImgTag AND $hoverText AND stripos($iconImgTag, 'title="') === false) {
			$iconImgTag = str_ireplace('<img ', '<img title="'.$hoverText.'" ', $iconImgTag);
		}
		$hoverText = $hoverText ? ' title="'.$hoverText.'" ' : '';
		return '<span class="button"'.$hoverText.'>'.$aTag.$iconImgTag.$label.'</a></span>';
	}


	/**
	 * Button: open module in new window
	 *
	 * @param	string		$function_name This is function name that should be passed to the script. Default: $this->MOD_SETTINGS['function']
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function btn_openMod_inNewWindow($function_name=NULL, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$name = is_null($function_name) ? $this->MOD_SETTINGS['function'] : $function_name;
		if (!$name) return;
		$onClick = 'vHWin=window.open(\''.t3lib_div::linkThisScript(array('forcedFunction' => $name)).'\',\''.$name.'\',\''.($BE_USER->uc['edit_wideDocument']?'width=670,height=550':'width=600,height=550').',status=0,menubar=0,scrollbars=1,resizable=1\');vHWin.focus();return false;';
		$content = '<span><a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/open_in_new_window.gif', 'width="19" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.openInNewWindow',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a></span>';

		return $content;
	}

	/**
	 * Button: edit record in new window
	 *
	 * @param	string		$table Table name
	 * @param	integer		$uid The uid
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function btn_editRec_inNewWindow($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array();
		$params['edit['.$table.']['.$uid.']'] = 'edit';
		$params['noView'] = 1;
		$params['returnUrl'] = PATH_txdam_rel.'close.html';
		$onClick = 'vHWin=window.open(\''.t3lib_div::linkThisUrl($BACK_PATH.'alt_doc.php', $params).'\',\''.md5(t3lib_div::getIndpEnv('TYPO3_REQUEST_SCRIPT')).'\',\''.($BE_USER->uc['edit_wideDocument']?'width=670,height=550':'width=600,height=550').',status=0,menubar=0,scrollbars=1,resizable=1\');vHWin.focus();return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/open_in_new_window.gif', 'width="19" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.openInNewWindow',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: remove record from selection
	 *
	 * @param	string		$table Table name
	 * @param	integer		$uid The uid
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function btn_removeRecFromSel($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array('SLCMD[NOT][txdamRecords]['.$uid.']' => '1');
		$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],PATH_txdam_rel.'i/button_deselect.gif', 'width="11" height="10"').' title="'.$LANG->getLL('deselect').'" class="absmiddle" '.$addAttrib.' alt="" />';
		$content = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript($params)).'">'.$icon.'</a>';

		return $content;
	}

	/**
	 * Button: edit record
	 *
	 * @param	string		$table Table name
	 * @param	integer		$uid The uid
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function icon_editRec($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array();
		$params['edit['.$table.']['.$uid.']'] = 'edit';
		$params = t3lib_div::implodeArrayForUrl('', $params);
		# $onClick = t3lib_BEfunc::editOnClick($params, $BACK_PATH,t3lib_div::getIndpEnv('REQUEST_URI').'?'.t3lib_div::implodeArrayForUrl('SET', $GLOBALS['HTTP_POST_VARS']['SET']),1);
		$onClick = t3lib_BEfunc::editOnClick($params, $BACK_PATH,t3lib_div::getIndpEnv('REQUEST_URI'),1);
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/edit2.gif', 'width="11" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:edit',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: record info
	 *
	 * @param	string		$table Table name
	 * @param	integer		$uid The uid
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function icon_infoRec($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$onClick = 'top.launchView(\''.$table.'\','.$uid.',\''.$BACK_PATH.'\');return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:showInfo',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: file info
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$addAttrib Attribute to be added to the icon
	 * @return	string		Button HTML code
	 */
	function icon_infoFile($fileInfo, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$filepath = tx_dam::file_absolutePath($fileInfo);
		$onClick = 'top.launchView(\''.$filepath.'\',\'\',\''.$BACK_PATH.'\');return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/zoom2.gif', 'width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:showInfo',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: go back
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		Button HTML code
	 */
	function btn_infoFile($fileInfo)	{
		global $LANG, $BACK_PATH;

		$filepath = tx_dam::file_absolutePath($fileInfo);
		$onClick = 'top.launchView(\''.$filepath.'\',\'\',\''.$BACK_PATH.'\');return false;';
		$aTagAttribute = ' onclick="'.htmlspecialchars($onClick).'"';
		$label = $LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.info',1);
		$iconImgTag = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/zoom2.gif', 'width="12" height="12"').' alt="" />';
		$hoverText = $LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:showInfo',1);
		$content = $this->button ($iconImgTag, $label, $hoverText, $url='#', $aTagAttribute);
		return $content;
	}

	/**
	 * Button: go back
	 *
	 * @param	array		Params array. Used to build a url with t3lib_div::linkThisScript()
	 * @param	string		Full url which should be the link href
	 * @return	string		Button HTML code
	 */
	function btn_back($params=array(), $absUrl='')	{
		global $LANG, $BACK_PATH;

		if ($absUrl) {
			$url = $absUrl;
		} else {
			$url = t3lib_div::linkThisScript($params);
		}

//		$content = '<a href="'.htmlspecialchars($url).'" class="typo3-goBack">'.
//					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/goback.gif"', 'width="14" height="14"').' class="absmiddle" alt="" /> '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack',1).
//					'</a>';

		$iconImgTag = '<img'.t3lib_iconWorks::skinImg($BACK_PATH, PATH_txdam_rel.'i/goback_flat.gif', 'width="10" height="14"').' alt="" />';
		$label = $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack',1);
		$content = $this->button ($iconImgTag, $label, '', $url);
		return $content;
	}



	/**
	 * Wraps an edit link around a string.
	 * Creates a page module link for pages, edit link for other tables.
	 *
	 * @param	string		The string to be wrapped
	 * @param	string		Table name (tt_content,...)
	 * @param	integer		uid of the record
	 * @return	string		Rendered link
	 */
	function wrapLink_edit($str, $refTable, $id)    {
		global $BACK_PATH, $BE_USER;

		if($refTable == 'pages') {
			$onClick = "top.fsMod.recentIds['web']=".$id.";top.goToModule('web_layout',1);";
		} else {
			$params = '&edit['.$refTable.']['.$id.']=edit';
			$onClick = t3lib_BEfunc::editOnClick($params, $BACK_PATH);
		}
		return '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$str.'</a>';
	}



	/**
	 * Wraps a link around a string if the href if given
	 *
	 * @param	string		href
	 * @param	string		The string to be wrapped
	 * @return	string		Rendered link
	 */
	function wrapLink ($href, $content) {
		if ($href) {
			$content = '<a href="'.htmlspecialchars($href).'">'.$content.'</a>';
		}
		return $content;
	}


	/**
	 * Returns a record icon with title and edit link
	 *
	 * @param	string		Table name (tt_content,...)
	 * @param	array		Record array
	 * @param	boolean		For pages records the rootline will be rendered
	 * @return	string		Rendered icon
	 */
	function getRecordInfoEditLink($refTable, $row, $showRootline=FALSE) {
		global $BACK_PATH, $LANG, $TCA;

		$iconAltText = t3lib_BEfunc::getRecordIconAltText($row, $refTable);

			// Prepend table description for non-pages tables
		if(!($refTable=='pages')) {
			$iconAltText = htmlspecialchars($LANG->sl($TCA[$refTable]['ctrl']['title']).': ').$iconAltText;
		}

			// Create record title or rootline for pages if option is selected
		if($refTable=='pages' AND $showRootline) {
			$elementTitle = t3lib_BEfunc::getRecordPath($row['uid'], '1=1', 0);
			$elementTitle = t3lib_div::fixed_lgd_cs($elementTitle, -($BE_USER->uc['titleLen']));
		} else {
			$elementTitle = t3lib_BEfunc::getRecordTitle($refTable, $row, 1);
		}

			// Create icon for record
		if ($refTable=='tx_dam') {
			$elementIcon = tx_dam_guiFunc::icon_getFileTypeImgTag($row, 'class="c-recicon" align="top" title="'.$iconAltText.'"', false);
		} else {
			$elementIcon = t3lib_iconworks::getIconImage($refTable, $row, $BACK_PATH, 'class="c-recicon" align="top" title="'.$iconAltText.'"');
		}

			// Return item with edit link
		return tx_dam_SCbase::wrapLink_edit($elementIcon. $elementTitle, $refTable, $row['uid']);
	}




	/********************************
	 *
	 * misc
	 *
	 ********************************/




	/**
	 * Returns a form tag with the current configured params
	 *
	 * @param 	string $name Name of the form tag
	 * @return	string HTML form tag
	 */
	function getFormTag($name='editform') {
		global $TYPO3_CONF_VARS;

		$formAction =t3lib_div::linkThisScript($this->addParams);
		return '<form action="'.htmlspecialchars($formAction).'" method="post" name="'.$name.'" id="'.$name.'" enctype="'.$TYPO3_CONF_VARS['SYS']['form_enctype'].'">';
	}




//  workaround

	/**
	 * Creates a tab menu from an array definition
	 *
	 * Returns a tab menu for a module
	 * Requires the JS function jumpToUrl() to be available
	 *
	 * @param	mixed		$id is the "&id=" parameter value to be sent to the module, but it can be also a parameter array which will be passed instead of the &id=...
	 * @param	string		$elementName it the form elements name, probably something like "SET[...]"
	 * @param	string		$currentValue is the value to be selected currently.
	 * @param	array		$menuItems is an array with the menu items for the selector box
	 * @param	string		$script is the script to send the &id to, if empty it's automatically found
	 * @param	string		$addParams is additional parameters to pass to the script.
	 * @return	string		HTML code for tab menu
	 */
	function getTabMenu($mainParams, $elementName, $currentValue, $menuItems, $script='', $addparams='')	{
		$content = '';

		if (is_array($menuItems))	{
			if (!is_array($mainParams)) {
				$mainParams = array('id' => $mainParams);
			}
			$mainParams = t3lib_div::implodeArrayForUrl('', $mainParams);

			if (!$script) {$script=basename(PATH_thisScript);}

			$menuDef = array();
			foreach($menuItems as $value => $label) {
				$menuDef[$value]['isActive'] = !strcmp($currentValue, $value);
				$menuDef[$value]['label'] = t3lib_div::deHSCentities(htmlspecialchars($label));
				// original: $menuDef[$value]['url'] = htmlspecialchars($script.'?'.$mainParams.$addparams.'&'.$elementName.'='.$value);
				$menuDef[$value]['url'] = $script.'?'.$mainParams.$addparams.'&'.$elementName.'='.$value;
			}
			$content = $this->doc->getTabMenuRaw($menuDef);

		}
		return $content;
	}





}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_scbase.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_scbase.php']);
}

?>