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
 * Contains the parent class for 'ScriptClasses' in DAM backend modules.
 *
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
 *  116: class tx_dam_SCbase extends t3lib_SCbase 
 *  212:     function init()	
 *  257:     function menuConfig()	
 *  314:     function getSelectionQueryParts($count=false) 
 *  326:     function execSelectionQuery($count=false) 
 *  344:     function prepareSelectionQuery($count=false) 
 *  363:     function addSelectionToQuery () 
 *  375:     function addLimitToQuery ($limit='', $begin='') 
 *
 *              SECTION: selection ...
 *  404:     function setSelectionCounter() 
 *
 *              SECTION: GUI misc
 *  443:     function getResultInfo() 
 *  476:     function getResultBrowser() 
 *  491:     function getResultInfoBar() 
 *  509:     function getHeaderBar($content, $options) 
 *  526:     function list_browseresults($tableParams='cellspacing="5"')	
 *  591:     function getStoreControl()	
 *  610:     function getSearchBox($mode='simple', $formAction='index.php')	
 *
 *              SECTION: GUI options
 *  653:     function getOptions() 
 *  670:     function addOption($type, $paramName, $description, $items=array()) 
 *  765:     function getCurrentSelectionBoxItems($queryType, $rows) 
 *
 *              SECTION: GUI files and folder
 *  839:     function getFolderInfoHeaderBar($path, $fmountArr, $browsable=TRUE, $extraIconArr=array()) 
 *  882:     function getBrowseableFolderList ($path, $folderParam) 
 *
 *              SECTION: helper
 *  907:     function getParentFolder($path) 
 *  922:     function getPathInfoText($path, $browsable=FALSE, $basePath='', $param='SET[tx_dam_folder]', $maxLength=35) 
 *
 *              SECTION: GUI buttons and icons
 *  972:     function btn_openMod_inNewWindow($MCONF_name=NULL, $addAttrib='')	
 *  993:     function btn_editRec_inNewWindow($table, $uid, $addAttrib='')	
 * 1016:     function btn_removeRecFromSel($table, $uid, $addAttrib='')	
 * 1035:     function icon_editRec($table, $uid, $addAttrib='')	
 * 1058:     function icon_infoRec($table, $uid, $addAttrib='')	
 * 1077:     function btn_back($params=array())	
 *
 *              SECTION: GUI registry
 * 1106:     function callUserFunction($func, &$obj)	
 * 1125:     function guiItems_callFunc($func)	
 * 1158:     function guiItems_getOutput($type='footer', $itemList='')	
 * 1184:     function guiItems_clear($type)	
 * 1199:     function guiItems_registerFunc($func, $type, $argArr=array(), $position='')	
 * 1288:     function guiItems_setParams($func, $argArr=array())	
 *
 * TOTAL FUNCTIONS: 35
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_t3lib.'class.t3lib_scbase.php');

require_once(PATH_t3lib.'class.t3lib_modsettings.php');

require_once(PATH_txdam.'lib/class.tx_dam_selection.php');
require_once(PATH_txdam.'lib/class.tx_dam_querygen.php');
require_once(PATH_txdam.'lib/class.tx_dam_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_types.php');
require_once(PATH_txdam.'lib/class.tx_dam_div.php');

$LANG->includeLLFile('EXT:dam/lib/locallang.php');


/**
 * Parent class for 'ScriptClasses' in DAM backend modules.
 * See DAM modules for examples.
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @see t3lib_SCbase
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_SCbase extends t3lib_SCbase {

	/**
	 * Selection object
	 */	
	var $sl;

	/**
	 * Query generator object
	 */	
	var $qg;

	/**
	 * selection counter and pointers
	 */
	var $pointer;
	var $resultsPerPage;
	var $firstItemNum;
	var $lastItemNum;

	/**
	 * Current SQL result
	 */
	var $res=FALSE;
	var $resCount=0;	// not preset - for own use
	var $resCountAll=0; // without LIMIT ...



	/**
	 * This is the current path for the file module
	 */
	var $path='';

	/**
	 * This is the current path of the file mount
	 * $fullpath = $path_mount.$path should be valid all the time
	 */
	var $path_mount='';

	/**
	 * This is the current file mount key
	 */
	var $fmountID='';




	/**
	 * the DAM folders
	 * currently only one is supported
	 */
	var $folderList='';

	/**
	 * default pid to store DAM records
	 */
	var $defaultPid; //depreciated
	var $defaultFolder;

	/**
	 * storage object
	 */
	var $store;

	/**
	 * last storage message
	 */
	var $storeMsg='';



	/**
	 * Items to output before module output
	 */
	var $guiItems_header = array();

	/**
	 * Items to output after module output
	 */
	var $guiItems_footer = array();

	/**
	 * Configuration parameters for output items
	 */	
	var $guiItems_params = array();
	var $guiItems_params_override = array();


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


		//
		// Get current folder
		//

			// tx_dam_folder could be set by GP or stored in module conf
		$SET = t3lib_div::_GP('SET');
		$this->path = $this->MOD_SETTINGS['tx_dam_folder'];

			// check if tx_dam_folder was set by GP which takes precedence, if not use command sent by navframe
			// order: GP (script), SLCMD (navframe), MOD_SETTINGS (stored)
		$CMD = t3lib_div::GParrayMerged('SLCMD');
		if (!$SET['tx_dam_folder'] AND is_array($CMD['SELECT']) AND is_array($CMD['SELECT']['txdamFolder'])) {
			$this->path = tx_dam_div::getRelPath(key($CMD['SELECT']['txdamFolder']));
		}
		$this->checkOrSetPath();
		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, array('tx_dam_folder' => $this->path), $this->MCONF['name'], 'ses');


		//
		// Detect and set forced single function and set params
		//

			// remove selection command from any params
		$this->addParams['SLCMD'] = '';

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

		list($this->defaultPid,$this->defaultFolder,$this->folderList) = tx_dam_db::initDAMFolders();

			// initializes the query generator object
		$this->qg = t3lib_div::makeInstance('tx_dam_querygen');
		$this->qg->initBESelect('tx_dam', $this->folderList);
		$this->addFilemountsToQuerygen();



			// initializes the selection object
		$this->sl = t3lib_div::makeInstance('tx_dam_selection');
		$this->sl->init($this, $this);
		$this->sl->store_MOD_SETTINGS = 'tx_dam_select';
		$this->sl->selectionClasses = $TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses'];
		$this->sl->paramPrefix = 'tx_dam';


//debug($this->MCONF, 'MCONF');
//debug($this->MOD_MENU, 'MOD_MENU');
//debug($this->MOD_SETTINGS, 'MOD_SETTINGS');
//debug($GLOBALS['HTTP_GET_VARS'], 'HTTP_GET_VARS');
//debug($GLOBALS['HTTP_POST_VARS'], 'HTTP_POST_VARS');
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
	 * selection to SQL
	 *
	 ********************************/


	function addFilemountsToQuerygen() {
			// init filemounts
		if(!$GLOBALS['BE_USER']->user['admin'] AND count($GLOBALS['FILEMOUNTS'])){
			$whereArr = array();
			foreach($GLOBALS['FILEMOUNTS'] as $mount){
				$whereArr[] = "tx_dam.file_path LIKE BINARY '".$GLOBALS['TYPO3_DB']->quoteStr(tx_dam_div::getRelPath($mount['path']), 'tx_dam')."%'";
			}
			$where = implode(' OR ', $whereArr);
			$where = $where ? '('.$where.')' : '';
			$this->qg->addWhere($where, 'AND', 'tx_dam.FILEMOUNTS');
		}
	}


	/**
	 * Generates the query from the db select array.
	 * 
	 * @param	[type]		$count: ...
	 * @return	string		query
	 */
	function getSelectionQueryParts($count=false) {
		$this->prepareSelectionQuery($count);
		$query = $this->qg->getQueryParts();
		return $query;
	}

	/**
	 * Executes the query from the db select array.
	 * 
	 * @param	[type]		$count: ...
	 * @return	string		query
	 */
	function execSelectionQuery($count=FALSE, $select='') {

		if(!$this->sl->hasSelection() AND !$select) {
			$this->resCountAll = 0;
			$this->res = false;
			return $this->res;

		}

		$this->prepareSelectionQuery($count);
		$queryArr = $this->qg->getQueryParts();
		if ($select) {
			$queryArr['SELECT'] = $select;
		}

		if (!$count AND $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['setup']['devel']) {
			t3lib_div::debug($queryArr,'$queryArr');
			$query = $GLOBALS['TYPO3_DB']->SELECTquery(
						$queryArr['SELECT'],
						$queryArr['FROM'],
						$queryArr['WHERE'],
						$queryArr['GROUPBY'],
						$queryArr['ORDERBY'],
						$queryArr['LIMIT']
					);
			t3lib_div::debug($query,'$query');
		}

		$this->res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArr);
		echo $GLOBALS['TYPO3_DB']->sql_error();
		if($count) {
			list($this->resCountAll) = $GLOBALS['TYPO3_DB']->sql_fetch_row($this->res);
		}
		return $this->res;
	}

	/**
	 * Generates the query from the db select array.
	 * 
	 * @param	[type]		$count: ...
	 * @return	string		query
	 */
	function prepareSelectionQuery($count=false) {
		$this->qg->setCount($count);
	}

	/**
	 * Adds the current selection to the db select array..
	 * 
	 * @return	void		
	 */
	function addSelectionToQuery () {
		if($this->sl->hasSelection()) {
			$this->qg->mergeWhere($this->sl->getSelectionWhereClauseArray());
		}
	}


	/**
	 * Adds a LIMIT to the db select array.
	 * 
	 * @param	[type]		$limit: ...
	 * @param	[type]		$begin: ...
	 * @return	void		
	 */
	function addLimitToQuery ($limit='', $begin='') {

		if($limit=='') {
			$limit=$this->resultsPerPage;
			$begin=$this->pointer*$this->resultsPerPage;
		}
		$this->qg->addLimit ($limit, $begin);
	}







	/********************************
	 *
	 * selection ...
	 *
	 ********************************/




	/**
	 * init some variables from MOD_SETTINGS etc.
	 * 
	 * @return	void		
	 */
	function setSelectionCounter() {
		$this->pointer = $this->MOD_SETTINGS['tx_dam_resultPointer'];
		$this->resultsPerPage = $this->MOD_SETTINGS['tx_dam_resultsPerPage'];
		$this->firstItemNum = $this->pointer*$this->resultsPerPage+1;
		$this->lastItemNum = min($this->firstItemNum+$this->resultsPerPage-1,$this->resCountAll);

		#debug($this->resCountAll,'resCountAll');
		#debug($this->pointer,'pointer');
		#debug($this->resultsPerPage,'resultsPerPage');
		#debug($this->firstItemNum,'firstItemNum');
		#debug($this->lastItemNum,'lastItemNum');

		if($this->resCountAll AND ($this->firstItemNum>$this->resCountAll)) {
			$this->MOD_SETTINGS['tx_dam_resultPointer'] = max($this->MOD_SETTINGS['tx_dam_resultPointer']-1,0);
			$this->setSelectionCounter();
		}
	}






	/********************************
	 *
	 * GUI misc
	 *
	 ********************************/




	/**
	 * my
	 * shows an info bar about the current selection
	 * "45 items in current selection"
	 * 
	 * @return	string		
	 */
	function getResultInfo() {
		global $LANG;

		if($this->resCountAll) {

			if (($this->pointer*$this->resultsPerPage) > $this->resCountAll) {
				$this->pointer = floor($this->resCountAll/$this->resultsPerPage);
			}


			if($this->resCountAll == 1) {
				$content = sprintf($LANG->getLL('oneRecordInSelection'));
			} elseif($this->resCountAll <= $this->resultsPerPage) {
				$content = sprintf($LANG->getLL('recordsInSelection'),$this->resCountAll);
			} else {
				$part = ($this->pointer*$this->resultsPerPage);
				$part = ($part+1).'-'.min($this->resCountAll,($part+$this->resultsPerPage));
				$content = sprintf($LANG->getLL('recordsFromSelection'),$part,$this->resCountAll);
			}
		} elseif(!$this->sl->hasSelection()) {
			$content = $LANG->getLL('noSelection');
		} else {
			$content = $LANG->getLL('noRecordsInSelection');
		}

		return $content;
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
		if($this->resCountAll > $this->resultsPerPage) {
			$content = $this->list_browseresults();
		}
		return $content;
	}

	/**
	 * shows a result information and a "results per page" selector
	 * 
	 * @return	string		
	 */
	function getResultInfoBar() {
		global $LANG;

		$content = $this->getResultInfo();
#TODO
		$showPerPage = $showPerPage ? $showPerPage : $LANG->getLL('recordsPerPage');
		$menu = t3lib_BEfunc::getFuncMenu($this->addParams,'SET[tx_dam_resultsPerPage]',$this->MOD_SETTINGS['tx_dam_resultsPerPage'],$this->MOD_MENU['tx_dam_resultsPerPage']);

		return $this->getHeaderBar($content, sprintf($showPerPage,$menu));
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
	 * @param	[type]		$content: ...
	 * @param	[type]		$options: ...
	 * @return	string		
	 */
	function getHeaderBar($content, $options='') {
		$content = $options ? $this->doc->funcMenu($content,$options) : $content;
#TODO
		$bgColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor4,0,0,0);
		$this->doc->inDocStylesArray['getHeaderBar'] = '				div.infobar {background-color:'.$bgColor.'; padding: 2px 5px 2px 5px; }';

		return '<div class="infobar">'.$content.'</div>';
	}

	/**
	 * Returns a results browser. This means a bar of page numbers plus a "previous" and "next" link.
	 * Using $this->pointer as pointer to the page to display
	 * Using $this->resCountAll, $this->resultsPerPage #TODO: and $this->internal['maxPages'] for count number, how many results to show and the max number of pages to include in the browse bar.
	 * 
	 * @param	string		Attributes for the table tag which is wrapped around the table cells containing the browse links
	 * @return	string		Output HTML, wrapped in <div>-tags with a class attribute
	 */
	function list_browseresults($tableParams='cellspacing="5"')	{
		global $LANG;

			// Initializing variables:
		$pointer=$this->pointer;
		$count=$this->resCountAll;
		$results_at_a_time = t3lib_div::intInRange($this->resultsPerPage,1,1000);
		$maxPages = t3lib_div::intInRange(20 /*$this->res['maxPages']*/,1,100);
		$max = t3lib_div::intInRange(ceil($count/$results_at_a_time),1,$maxPages);
		$pointer=intval($pointer);
		$links=array();
$alwaysPrev=1;
			// Make browse-table/links:
		if ($alwaysPrev>=0)	{
			if ($pointer>0)	{
				$links[]='<td class="browsebox-Cell" nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>($pointer-1?$pointer-1:'')))).'">'.htmlspecialchars('<').'</a>'.
				'</p></td>';
			} elseif ($alwaysPrev)	{
				$links[]='<td class="browsebox-Cell" nowrap="nowrap"><p>'.htmlspecialchars('<').'</p></td>';
			}
		}

		for($a=0;$a<$max;$a++)	{
			$links[]='<td '.($pointer==$a?'class="browsebox-SCell"':'class="browsebox-Cell"').' nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>((string)$a)))).'">'.htmlspecialchars($a+1).'</a>'.
				'</p></td>';
		}
		if ($pointer<ceil($count/$results_at_a_time)-1)	{
			$links[]='<td class="browsebox-Cell" nowrap="nowrap"><p>'.
				'<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_resultPointer]'=>$pointer+1))).'">'.htmlspecialchars('>').'</a>'.
				'</p></td>';
		} elseif ($alwaysPrev)	{
				$links[]='<td class="browsebox-Cell" nowrap="nowrap"><p>'.htmlspecialchars('>').'</p></td>';
		}

		$pR1 = $pointer*$results_at_a_time+1;
		$pR2 = $pointer*$results_at_a_time+$results_at_a_time;
		$sTables = '<div class="browsebox">'.
		'<'.trim('table '.$tableParams).'>
			<tr>'.implode('',$links).'</tr>
		</table></div>';

			//include CSS
		$bgColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor4,0,0,0);
		$bgColorAct = t3lib_div::modifyHTMLcolor($this->doc->bgColor5,25,25,25);
		$this->doc->inDocStylesArray['list_browseresults'] = '
				.browsebox { margin-right:20px; }
				.browsebox TD { width:20px; text-align:center; background-color:'.$bgColor.'; padding: 0px 2px 0px 2px; }
				.browsebox TD.browsebox-SCell { background-color:'.$bgColorAct.'; }
				.browsebox TD P { color:#888; }
				.browsebox TD.browsebox-Cell  P a { font-weight:bold; color:#000; display:block; width:auto; }
				.browsebox TD.browsebox-SCell P a { font-weight:bold; color:#000; display:block; width:auto; }';

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
		$content = '';

		if(is_object($this->store)) {
			$content.= $this->doc->spacer(15);
				// store control
			$content.= $this->doc->section('Selection:',$this->store->getStoreControl(),0,0);
		}
		return $content;
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
		$formElements=array('','');
		if ($useFormTag)	{
			$formAction = $formAction ? $formAction : $this->linkThisScriptSel($this->addParams);
			$formElements=array('<form action="'.htmlspecialchars($formAction).'" method="post">','</form>');
		}

			// Table with the search box:
		$content.= '
			'.$formElements[0].'

				<!--
					Search box:
				-->
				<table border="0" cellpadding="2" cellspacing="0" class="bgColor4" id="typo3-dblist-search">
					<tr>
						<td> '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.enterSearchString',1).' <input type="text" name="SLCMD[SEARCH][txdamStrSearch][0]" value="'.htmlspecialchars($this->sl->sel['SEARCH']['txdamStrSearch'][0]).'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(10).' /></td>
						<td><input type="submit" name="search" value="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.search',1).'" /></td>
					</tr>
				</table>
			'.$formElements[1];
#		$content.=t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'list_searchbox', $GLOBALS['BACK_PATH'],'|<br/>');
		return $content;
	}



	/**
	 * Returns a dia like thumbnail
	 * 
	 * @param	array		tx_dam record
	 * @param	integer		dia size
	 * @param	integer		dia margin
	 * @param	array		Extra elements to show: "title,info,icons"
	 * @return	string		HTML output
	 */
	function getDia($row, $diaSize=115, $diaMargin=10, $showElements='', $onClick=NULL, $makeIcon=TRUE) {
		global $SOBE;

		if(!is_array($showElements)) {
			$showElements = t3lib_div::trimExplode(',',$showElements,1);
		}


			// extra CSS code for HTML header
		if(!isset($this->doc->inDocStylesArray['tx_dam_SCbase_dia'])) {
			$SOBE->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_SCbase::getDiaStyles($diaSize, $diaMargin);
		}

#TODO
		$iconBgColor = t3lib_div::modifyHTMLcolor($SOBE->doc->bgColor,-10,-10,-10);
		$titleLen = ceil( (30*($diaSize-$diaMargin))/(200-$diaMargin) );

		$hpixels = $row['hpixels'];
		$vpixels = $row['vpixels'];

		if ($hpixels AND $vpixels) {
			$maxpx = max($hpixels, $vpixels);
			$minpx = min($hpixels, $vpixels);
			$px = intval(round($minpx * $diaSize / $maxpx));
			if ($hpixels > $vpixels) {
				$hpixels = $diaSize;
				$vpixels = $px;
			} else {
				$hpixels = $px;
				$vpixels = $diaSize;
			}
		} else {
			if($hpixels > $diaSize) {
				$hpixels = $diaSize;
			}
			if($vpixels > $diaSize) {
				$vpixels = $diaSize;
			}
		}


		$uid = $row['uid'];
		$tooltip = str_replace("\n",'',t3lib_div::fixed_lgd_cs($row['description'], 50));
		if ($hpixels) {
			$attribs = ' width="'.$hpixels.'" height="'.$vpixels.'" style="margin-top:'.(ceil(($diaSize-$vpixels)/2)+$diaMargin).'px;"';
		} else {
			$attribs = ' style="margin-top:'.$diaMargin.'px;"';
		}
		#$attribsIcon = ' style="margin-top:'.$diaMargin.'px;padding:'.(ceil(($diaSize-18)/2)).'px"';
		$thumb = tx_dam_div::thumbnail($row['file_path'].$row['file_name'], $diaSize, $tooltip, $attribs, $attribsIcon, $onClick, FALSE);
		#$attribsIcon = ' style="margin-top:'.$diaMargin.'px;padding:'.(ceil(($diaSize-29)/2)).'px"';
		#$thumb = $thumb ? $thumb : '<a href="#"'.$onClick.'><span'.$attribsIcon.'>'.tx_dam_div::mediatypeIcon($row).'</span></a>';
		if (!$makeIcon AND empty($thumb)) { return; }
		$thumb = $thumb ? $thumb : '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.tx_dam_div::mediatypeIcon($row).'</a>';

		$descr = '';
		if (in_array('title', $showElements)) {
			$descr.= htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'],$titleLen)).'<br />';
		}
		if (in_array('info', $showElements)) {
			$code = strtoupper($row['file_type']).', ';
			$code.= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'].', ' :'';
			$code.= t3lib_div::formatSize($row['file_size']);
			# $code.= $row['color_space'] ? ', '.$LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tx_dam','color_space',$row['color_space'])) : '';
			$descr .= '<span class="txdam-descr">'.htmlspecialchars($code).'</span>';
		}
		if($descr) {
			$descr = '<div class="txdam-title">'.$descr.'</div>';
		}

		$icons  = '';
		if (in_array('icons', $showElements)) {
			$iconArr = array();
			$iconArr[] = tx_dam_SCbase::icon_editRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_editRec_inNewWindow('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::icon_infoRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_removeRecFromSel('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');

			$icons = '<div style="margin:3px;">'.implode('<span style="width:40px;"></span>', $iconArr).'</div>';
		}

		$diaCode = '
		<table class="txdam-dia" cellspacing="0" cellpadding="0" border="0">
		<tr><td><span><span class="txdam-dia">'.$thumb.'</span></td></tr>
		'. ( ($descr.$icons) ? '<tr><td align="center" bgcolor="'.$iconBgColor.'">'.$descr.$icons.'</td></tr>' : '').'
		</table> ';

		return $diaCode;
	}


	function getDiaStyles($diaSize=115, $diaMargin=10, $margin=0) {
			// extra CSS code for HTML header
		$styles = '

			.txdam-title, .txdam-descr {
				font-face:verdana,sans-serif;
				font-size:9.5px;
				line-height:12px;
				margin:2px;
			}
			.txdam-descr {
				color:#777;
			}
			table.txdam-dia {
				float:left;
				margin-bottom:8px;
			}
			span.txdam-dia {
				float:left;
				width:'.($diaSize+($diaMargin*2)+2).'px;
				height:'.($diaSize+($diaMargin*2)+2).'px;

				text-align:center;
				vertical-align:middle;

				margin:'.$margin.'px;
				padding:0px;
				background-color:#fbfbfb;
				border:solid #999 1px;
				border-top:solid #ddd 1px;
				border-bottom:solid #000 1px;
			}

			span.txdam-dia > a {
				text-decoration:none;
			}
			span.txdam-dia > a >img {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				vertical-align:50%;
			}
			span.txdam-dia > a >div {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				padding:'.($diaMargin).'px;
				width:'.($diaSize-$diaMargin-$diaMargin).'px;
				height:'.($diaSize-$diaMargin-$diaMargin).'px;
				vertical-align:middle;
			}
			';
		return $styles;
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
			return $this->doc->spacer(15).$this->doc->section($LANG->getLL('options').':',implode('<br />',$this->modOptions),0,0);
		}
	}

	/**
	 * Add options
	 * 
	 * @param	string		Option type: html, funcCheck, funcMenu, funcInput
	 * @param	string		Name of the MOD_MENU/MOD_SETTINGS parameter
	 * @param	string		Description text or HTML for html type
	 * @param	array		$items for funcMenu
	 * @return	void		
	 */
	function addOption($type, $paramName, $description, $items=array()) {
		switch ($type) {
		case 'funcCheck':
			$this->modOptions[$paramName] = t3lib_BEfunc::getFuncCheck($this->addParams,'SET['.$paramName.']',$this->MOD_SETTINGS[$paramName]).' '.$description;
		break;
		case 'funcInput':
			$this->modOptions[$paramName] = $description.' '.t3lib_BEfunc::getFuncInput($this->addParams,'SET['.$paramName.']',$this->MOD_SETTINGS[$paramName]);
		break;
		case 'funcMenu':
			$this->modOptions[$paramName] = $description.' '.t3lib_BEfunc::getFuncMenu($this->addParams,'SET['.$paramName.']',$this->MOD_SETTINGS[$paramName], $items);
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
	 * @param	[type]		$showElements: ...
	 * @return	string		
	 */
	function getCurrentSelectionBox($showElements='box,btn_revert') { // $showElements has no function for now
		global $LANG, $SOBE, $BACK_PATH;

		$headBbgColor=' bgColor="'.$SOBE->doc->bgColor6.'"';
		$rowBbgColor=' bgColor="'.t3lib_div::modifyHTMLColor($GLOBALS['SOBE']->doc->bgColor4,+10,+10,+10).'"';

		$rows=array();
		$lastHeader = '';
		foreach (array('SELECT','OR','AND','NOT','DESELECT_ID','SEARCH') as $queryType) {
			if(is_array($this->sl->sel[$queryType])) {

				switch($queryType) {
					case 'SELECT':
					case 'OR':
						if($lastHeader!='SELECT') {
							$rows[]='<td'.$headBbgColor.' colspan="3" valign="middle"><strong><img src="'.$BACK_PATH.PATH_txdam_rel.'i/plus_16.gif" width="12" height="16" border="0" align="top" alt="" /> &nbsp;'.
							$LANG->getLL('selEquals').'</strong></td>';
						}
						$lastHeader='SELECT';
						$rows=$this->getCurrentSelectionBoxItems($queryType, $rows);
					break;

					case 'AND':
						$rows[]='<td'.$headBbgColor.' colspan="3"><strong><img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals_16.gif" width="12" height="16" border="0" align="top" alt="" /> &nbsp;'.
						$LANG->getLL('selPlus').'</strong></td>';
						$rows=$this->getCurrentSelectionBoxItems($queryType, $rows);
					break;

					case 'NOT':
					case 'DESELECT_ID':
						if($lastHeader!='NOT') {
							$rows[]='<td'.$headBbgColor.' colspan="3"><strong><img src="'.$BACK_PATH.PATH_txdam_rel.'i/minus_16.gif" width="12" height="16" border="0" align="top" alt="" /> &nbsp;'.
							$LANG->getLL('selMinus').'</strong></td>';
						}
						$lastHeader='NOT';
						$rows=$this->getCurrentSelectionBoxItems($queryType, $rows);
					break;

					case 'SEARCH':
						$rows[]='<td'.$headBbgColor.' colspan="3"><strong> '.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.search',1).'</strong></td>';
						$rows=$this->getCurrentSelectionBoxItems($queryType, $rows);
					break;

					default:
						$rows[]='<td'.$headBbgColor.' colspan="3"><strong> '.$queryType.'</strong></td>';
						$rows=$this->getCurrentSelectionBoxItems($queryType, $rows);
					break;
				}
			}
		}
		#$rawOutput=t3lib_div::view_array($this->sl->sel);
		return '<br/><table cellspacing="1" cellpadding="2" border="0" width="100%"><tr'.$rowBbgColor.'>'.implode('</tr><tr'.$rowBbgColor.'>',$rows).'</tr></table>'.$rawOutput.'<br /><input type="submit" value="'.$LANG->getLL('revertSelection').'" name="'.$this->sl->paramPrefix.'_undo" \>';

	}

	/**
	 * Current selection box items
	 * 
	 * @param	string		
	 * @param	array		table rows
	 * @return	array		table rows
	 */
	function getCurrentSelectionBoxItems($queryType, $rows) {
		global $LANG, $BACK_PATH, $BE_USER;
		static $selClasses=array();

		$sel = $this->sl->sel;

		foreach ($sel[$queryType] as $cat => $items) {
			if(is_array($items)) {
				foreach($items as $id => $value) {
					if($value) {
						$categoryTitle = '';
						$deselectValue = '0';

						if (!is_object($selClasses[$cat]) AND $this->sl->selectionClasses[$cat]) {
							if (is_object($obj = &t3lib_div::getUserObj($this->sl->selectionClasses[$cat],'user_',TRUE)))	{
								$selClasses[$cat] = &$obj;
							}
						}
						if (is_object($selClasses[$cat])) {
							$categoryTitle = $selClasses[$cat]->dam_treeTitle();
							$itemTitle = $selClasses[$cat]->dam_itemTitle($id, $value);
							$deselectValue = $selClasses[$cat]->deselectValue;

							// DESELECT_ID
						} elseif($cat=='tx_dam') {

#TODO move this into TBE_MODULES_EXT['txdam']['addSelectionClasses']
							$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,file_type,media_type', 'tx_dam', 'uid='.$id);

							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
								$iconfile = t3lib_iconWorks::getIcon('tx_dam',$row);
								$titletext = t3lib_BEfunc::getRecordIconAltText($row,'tx_dam');
								$theIcon = '<img src="'.$BACK_PATH.$iconfile.'" width="18" height="16" border="0" title="'.$titletext.'" alt="" />';

								$categoryTitle = $theIcon;
								$itemTitle = htmlspecialchars(t3lib_div::fixed_lgd($row['title'],25));
							}

						}

						if(!((string)$categoryTitle=='')) {
							$params = array('SLCMD['.$queryType.']['.$cat.']['.(string)$id.']' => $deselectValue);
							$actionIcon='<a href="'.htmlspecialchars($this->linkThisScriptSel($params)).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/button_remove.gif" width="11" height="10" border="0" title="'.$LANG->getLL('remove').'" align="top" alt="" /></a>';

							$rows[] = '<td width="1%">'.$actionIcon.'</td><td nowrap="nowrap">'.$categoryTitle.'</td><td width="70%">'.htmlspecialchars(t3lib_div::fixed_lgd_cs($itemTitle, $BE_USER->uc['titleLen'])).'</td>';
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
		$pString = t3lib_div::implodeArrayForUrl('',$params,$str='',$skipBlank=0,$rawurlencodeParamName=true);

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
	 * @param	string		Path to show
	 * @param	string		Filemount array
	 * @param	boolean		Define if the info header includes browsable links
	 * @param	array		Array of icons with extra HTML code which should be shown additionally at the end of the bar. Could be also a list of icon keys which should not be shown - is the same like array with empty value of a key
	 * @param	boolean		Show the icon keys which are allowed to show (array or comma list)
	 * @return	string		HTML content
	 */
	 function getPathInfoHeaderBar($path, $fmountArr=array(), $browsable=TRUE, $extraIconArr=array(), $allowedIcons=NULL) {
	 	global $LANG, $BACK_PATH, $FILEMOUNTS;

			// Finding the icon
		switch($fmountArr['type'])	{
			case 'user':	$iconfile = 'gfx/i/_icon_ftp_user.gif';	break;
			case 'group':	$iconfile = 'gfx/i/_icon_ftp_group.gif';	break;
			default:		$iconfile = 'gfx/i/_icon_ftp.gif';	break;
		}

		$shortPath = tx_dam_div::getRelPath($path, $fmountArr['path']);

		if ($browsable) {
			$mountTitle = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_folder]' => $fmountArr['path']))).'">'.htmlspecialchars($fmountArr['name']).'</a>';
		} else {
			$mountTitle = htmlspecialchars($fmountArr['name']);
		}
		$browsePath = $fmountArr['name'] ? ($mountTitle.': ') : '';
		$browsePath.= $this->getPathInfoText($shortPath, $browsable, $fmountArr['path']);
		$fileheader = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,$iconfile,'width="18" height="16"').' title="'.htmlspecialchars($shortPath).'" class="absmiddle" alt="" /> '.$browsePath;

		$cmdIcons = array();
		$cmdIconRight = array();
		$extraIconArr = is_array($extraIconArr) ? $extraIconArr : t3lib_div::trimExplode(',',$extraIconArr,TRUE);
		$allowedIcons = is_null($allowedIcons) ? ('up,refresh,popup,'.implode(',', array_keys($extraIconArr))) : $allowedIcons;
		$allowedIcons = is_array($allowedIcons) ? $allowedIcons : t3lib_div::trimExplode(',',$allowedIcons,TRUE);

		$allowedIcons = array_diff($allowedIcons, $this->guiCmdIconsDeny);

			// folder up button
		if ($browsable AND in_array('up', $allowedIcons) AND $shortPath)	{
			$icon = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/i/folder_up.gif','width="18" height="16"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.upOneLevel',1).'" class="absmiddle" alt="" />';
			$cmdIcons['up'] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array('SET[tx_dam_folder]' => $this->getParentFolder($path)))).'">'.$icon.'</a>';
		}

			// refresh button
		if ($browsable AND in_array('refresh', $allowedIcons)) {
			$icon = '<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/refresh_n.gif','width="14" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.reload',1).'" class="absmiddle" alt="" />';
			$cmdIcons['refresh'] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript()).'">'.$icon.'</a>';
		}

		if ($browsable AND in_array('popup', $allowedIcons)) {
				// open in new window button
			if (!$this->forcedFunction) {
				$cmdIconRight['popup'] = '&nbsp;&nbsp;&nbsp;'.$this->btn_openMod_inNewWindow();
			}
		}
			// put func menu to the end
		if ($extraIconArr['funcMenu']) {
				$cmdIconRight['funcMenu'] = $extraIconArr['funcMenu'];
				unset($extraIconArr['funcMenu']);
		}

		$cmdIcons = t3lib_div::array_merge_recursive_overrule($cmdIcons, $extraIconArr);
		$cmdIcons = t3lib_div::array_merge_recursive_overrule($cmdIcons, $cmdIconRight);

		return $this->getHeaderBar($fileheader, implode('&nbsp;',$cmdIcons));
	}



	/**
	 * Creates a browsable file/folder list
	 * 
	 * @param	string		Path
	 * @param	string		Path
	 * @return	string		Output
	 */
	function getBrowseableFolderList ($path, $folderParam) {
		$filelist = t3lib_div::makeInstance('tx_dam_fileList');
		$filelist->folderParam = $folderParam;
		$content = $filelist->getBrowseableFolderList($path);

		$cnBgColor = t3lib_div::modifyHTMLcolor($this->doc->bgColor3,-5,-5,-5);
		$content = '<div style="width:100%;background-color:'.$cnBgColor.'">'.$content.'</div>';
		return $content;
	}



	/********************************
	 *
	 * helper
	 *
	 ********************************/


	/**
	 * Returns the path of the parent folder from the given path
	 * 
	 * @param	[type]		$path: ...
	 * @return	string		Path
	 */
	 function getParentFolder($path) {
		$pathInfo = t3lib_div::split_fileref(preg_replace('#/$#', '', $path));
		return  $pathInfo['path'];
	}

	/**
	 * Returns a path with links to browse to directly to the path
	 * 
	 * @param	[type]		$path: ...
	 * @param	[type]		$browsable: ...
	 * @param	[type]		$basePath: ...
	 * @param	[type]		$param: ...
	 * @param	[type]		$maxLength: ...
	 * @return	string		Linked Path
	 */
	 function getPathInfoText($path, $browsable=FALSE, $basePath='', $param='SET[tx_dam_folder]', $maxLength=35) {
	 	$pathArr = explode('/', $path);
	 	$pathArrRev = array_reverse($pathArr, TRUE);

	 	$newPathArr = array();
	 	$len = 0;
	 	foreach ($pathArrRev as $key => $part) {

		 	if ($part) {
		 		$len += strlen($part)+1;
		 		if ($len > $maxLength) {
		 			$part = '...';
		 		}
			 	if ($browsable) {
		 			$linkPath = implode('/', $pathArr).'/';
					$newPathArr[] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array($param => $basePath.$linkPath))).'">'.htmlspecialchars($part).'</a>';
			 	} else {
					$newPathArr[] = htmlspecialchars($part);
			 	}
		 	} else {
		 		$len ++;
		 		$newPathArr[] = '';
		 	}
		 	if ($len > $maxLength) { break; }
			array_pop($pathArr);
	 	}
	 	$newPathArr = array_reverse($newPathArr);

		return  implode('/', $newPathArr);
	}

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
			$path = tx_dam_div::getAbsPath($this->path);
		}

		if ($path && t3lib_div::validPathStr($path) && is_array($FILEMOUNTS))	{
			foreach($FILEMOUNTS as $val)	{
				if (t3lib_div::isFirstPartOfStr($path,$val['path']))	{

					$this->path = tx_dam_div::getRelPath($path);
					return;
				}
			}
		}
		$this->path = '';
	}


	/********************************
	 *
	 * GUI buttons and icons
	 *
	 ********************************/	




	/**
	 * Button: open module in new window
	 * 
	 * @param	[type]		$function_name: ...
	 * @param	[type]		$addAttrib: ...
	 * @return	string		Button HTML code
	 */
	function btn_openMod_inNewWindow($function_name=NULL, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$name = is_null($function_name) ? $this->MOD_SETTINGS['function'] : $function_name;
		if (!$name) return;
		$onClick = 'vHWin=window.open(\''.t3lib_div::linkThisScript(array('forcedFunction' => $name)).'\',\''.$name.'\',\''.($BE_USER->uc['edit_wideDocument']?'width=670,height=550':'width=600,height=550').',status=0,menubar=0,scrollbars=1,resizable=1\');vHWin.focus();return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/open_in_new_window.gif','width="19" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.openInNewWindow',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: edit record in new window
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$addAttrib: ...
	 * @return	string		Button HTML code
	 */
	function btn_editRec_inNewWindow($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array();
		$params['edit['.$table.']['.$uid.']'] = 'edit';
		$params['noView'] = 1;
		$params['returnUrl'] = 'close.html';
		$onClick = 'vHWin=window.open(\''.t3lib_div::linkThisUrl($BACK_PATH.'alt_doc.php',$params).'\',\''.md5(t3lib_div::getIndpEnv('TYPO3_REQUEST_SCRIPT')).'\',\''.($BE_USER->uc['edit_wideDocument']?'width=670,height=550':'width=600,height=550').',status=0,menubar=0,scrollbars=1,resizable=1\');vHWin.focus();return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/open_in_new_window.gif','width="19" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.openInNewWindow',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: remove record from selection
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$addAttrib: ...
	 * @return	string		Button HTML code
	 */
	function btn_removeRecFromSel($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array('SLCMD[DESELECT_ID]['.$table.']['.$uid.']' => '1');
		$content = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript($params)).'">'.
					'<img src="'.$BACK_PATH.PATH_txdam_rel.'i/button_deselect.gif" width="11" height="10" border="0" title="'.$LANG->getLL('deselect').'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: edit record
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$addAttrib: ...
	 * @return	string		Button HTML code
	 */
	function icon_editRec($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array();
		$params['edit['.$table.']['.$uid.']'] = 'edit';
		$params = t3lib_div::implodeArrayForUrl('', $params);
		# $onClick = t3lib_BEfunc::editOnClick($params,$BACK_PATH,t3lib_div::getIndpEnv('REQUEST_URI').'?'.t3lib_div::implodeArrayForUrl('SET',$GLOBALS['HTTP_POST_VARS']['SET']),1);
		$onClick = t3lib_BEfunc::editOnClick($params,$BACK_PATH,t3lib_div::getIndpEnv('REQUEST_URI'),1);
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/edit2.gif','width="11" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.php:edit',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

		return $content;
	}

	/**
	 * Button: record info
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$addAttrib: ...
	 * @return	string		Button HTML code
	 */
	function icon_infoRec($table, $uid, $addAttrib='')	{
		global $LANG, $BACK_PATH;

		$params = array();
		$params['edit['.$table.']['.$uid.']'] = 'edit';
		$params = t3lib_div::implodeArrayForUrl('', $params);
		$onClick = 'top.launchView(\''.$table.'\','.$uid.',\''.$BACK_PATH.'\');return false;';
		$content = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/zoom2.gif"','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.php:showInfo',1).'" class="absmiddle" '.$addAttrib.' alt="" />'.
					'</a>';

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

		$content = '<a href="'.htmlspecialchars($url).'" class="typo3-goBack">'.
					'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/goback.gif"','width="14" height="14"').' class="absmiddle" alt="" /> Go back'.
					'</a>';

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

		if($refTable=='pages') {
			$onClick = "top.fsMod.recentIds['web']=".$id.";top.goToModule('web_layout',1);";
		} else {
			$params = '&edit['.$refTable.']['.$id.']=edit';
			$onClick = t3lib_BEfunc::editOnClick($params, $BACK_PATH);
		}
		return '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$str.'</a>';
	}


	/********************************
	 *
	 * GUI registry
	 *
	 ********************************/	


	/**
	 * Call a user func with variable parameter list
	 * 
	 * @param	string		Name of the user function
	 * @param	string		Already existing object or NULL (using $this)
	 * @return	mixed		Function output
	 */
	function callUserFunction($func, &$obj)	{
		if (!is_object($obj)) {
			$obj = &$this;
		}
		if (@is_callable(array($obj,$func)))	{
			$arg_list = func_get_args();
			unset($arg_list[0]); //$func
			unset($arg_list[1]); //$obj
			return call_user_func_array(array($obj, $func),$arg_list);
		}
	}


	/**
	 * Call a gui function
	 * 
	 * @param	mixed		Name of the user function or an array like array($object, 'function_name')
	 * @return	mixed		Function output
	 */
	function guiItems_callFunc($func)	{

		if (is_array($func)) {
			list($obj, $func) = each($func);
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $func;
		}
		$arg_list = array($func, &$obj);

		$prefix = is_object($obj) ? get_class($obj).'>' : '';

		if (is_array($this->guiItems_params_override[$prefix.$func])) {
			$arg_list = $arg_list + $this->guiItems_params_override[$prefix.$func];
		} elseif (is_array($this->guiItems_params[$prefix.$func])) {
			$arg_list = $arg_list + $this->guiItems_params[$prefix.$func];
		}

		return call_user_func_array(array($this, 'callUserFunction'), $arg_list);
	}



	/**
	 * Call gui item functions and return the output
	 * 
	 * @param	string		Type name: header, footer
	 * @param	string		List of item function which should be called instead of the default defined
	 * @return	string		Items output
	 */
	function guiItems_getOutput($type='footer', $itemList='')	{
		if (is_null($itemList)) return;

		if($itemList) {
			$itemListArr = t3lib_div::trimExplode(',', $itemList, 1);
		} else {
			$type = 'guiItems_'.$type;

			if(!is_array($this->$type)) return;
			$itemListArr = array_keys($this->$type);
		}

		$out = '';
		foreach ($itemListArr as $item) {
			$content = $this->guiItems_callFunc($item);
			$out .= $this->doc->section('',$content,0,1);
		}
		return $out;
	}

	/**
	 * Clears all registered gui items
	 * 
	 * @param	string		Type name: header, footer
	 * @return	void		
	 */
	function guiItems_clear($type)	{
		$type = 'guiItems_'.$type;
		if(!is_array($this->$type)) return;
		$this->$type = array();
	}

	/**
	 * Register a gui function
	 * 
	 * @param	mixed		Name of the function or an array like array(&$object, 'function_name')
	 * @param	string		Type name: header, footer
	 * @param	array		Array of parameters which should be passed to the function
	 * @param	string		$position can be used to set the position of the item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or blank which is default: bottom).
	 * @return	void		
	 */
	function guiItems_registerFunc($func, $type, $argArr=array(), $position='')	{
		if (is_array($func)) {
			list($obj, $func) = each($func);
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $func;
		}

		$prefix = is_object($obj) ? get_class($obj).'>' : '';


		$type = 'guiItems_'.$type;
		if(!is_array($this->$type)) return;
		$itemArr = &$this->$type;

		$argArr = is_array($argArr) ? $argArr : array();
		$this->guiItems_params[$prefix.$func] = $argArr;

		$newItem = array();
		$newItem[$prefix.$func] = array('obj'=>&$obj, 'func'=>$func);

		$pointer = count($itemArr);
		if($position) {

			$posArr = t3lib_div::trimExplode(',', $position, 1);
			foreach($posArr as $pos) {
				list($place,$itemEntry) = t3lib_div::trimExplode(':', $pos, 1);

					// bottom
				$pointer = count($itemArr);

				$found=FALSE;

				if ($place) {
					switch(strtolower($place))	{
						case 'after':
						case 'before':
							if ($itemEntry) {
								$p=1;
								reset ($itemArr);
								while (true) {
									if (!strcmp(key($itemArr), $itemEntry))	{
										$pointer = $p;
										$found=TRUE;
										break;
									}
									if (!next($itemArr)) break;
									$p++;
								}
								if (!$found) break;

								if ($place=='before') {
									$pointer--;
								} elseif ($place=='after') {
								}
							}
						break;
						case 'top':
							$pointer = 0;
							$found=TRUE;
						break;
						default:
							$pointer = count($itemArr);
							$found=TRUE;
						break;
					}
				}
				if($found) break;
			}
		}

		$pointer=max(0,$pointer);
		$itemsBefore = array_slice($itemArr, 0, ($pointer?$pointer:0));
		$itemsAfter = array_slice($itemArr, $pointer);
		$itemArr = $itemsBefore + $newItem + $itemsAfter;

	}


	/**
	 * Set (override) parameters for a registered  gui function
	 * 
	 * @param	mixed		Name of the user function or an array like array($object, 'function_name')
	 * @param	array		Array of parameters which should be passed to the function
	 * @return	void		
	 */
	function guiItems_setParams($func, $argArr=array())	{
		if (is_array($func)) {
			list($obj, $func) = each($func);
			if (!is_object($obj)) {
				$obj = NULL;
			}
		} else {
			$obj = NULL;
			$func = $func;
		}
		$prefix = is_object($obj) ? get_class($obj).'>' : '';
		$this->guiItems_params[$prefix.$func] = $argArr;
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
	 * @author	René Fritz <r.fritz@colorcube.de>
	 */
	function getTabMenu($mainParams,$elementName,$currentValue,$menuItems,$script='',$addparams='')	{
		$content='';

		if (is_array($menuItems))	{
			if (!is_array($mainParams)) {
				$mainParams = array('id' => $mainParams);
			}
			$mainParams = t3lib_div::implodeArrayForUrl('',$mainParams);

			if (!$script) {$script=basename(PATH_thisScript);}

			$menuDef = array();
			foreach($menuItems as $value => $label) {
				$menuDef[$value]['isActive'] = !strcmp($currentValue,$value);
				$menuDef[$value]['label'] = t3lib_div::deHSCentities(htmlspecialchars($label));
#$menuDef[$value]['url'] = htmlspecialchars($script.'?'.$mainParams.$addparams.'&'.$elementName.'='.$value);
				$menuDef[$value]['url'] = $script.'?'.$mainParams.$addparams.'&'.$elementName.'='.$value;
			}
			$content = $this->doc->getTabMenuRaw($menuDef);

		}
		return $content;
	}





}


// No XCLASS inclusion code: this is a base class
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_scbase.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_scbase.php']);
//}

?>