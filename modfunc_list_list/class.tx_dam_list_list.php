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
 * Module extension (addition to function menu) 'list' for the 'Media>List' module.
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
 *   61: class tx_dam_list_list extends t3lib_extobjbase 
 *   69:     function main()    
 *  143:     function jumpExt(URL,anchor)	
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

$LANG->includeLLFile('EXT:lang/locallang_mod_web_list.php');

require_once(PATH_txdam.'modfunc_list_list/class.tx_dam_db_list.php');

/**
 * Module extension  'Media>List>List'
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_list_list extends t3lib_extobjbase {


	/**
	 * Initialize the class and set some HTML header code
	 * 
	 * @return	void		
	 */
	function head()	{

		//
		// Init gui items and ...
		//
		
		$this->pObj->guiItems_registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems_registerFunc('getResultBrowser', 'header');
		
		$this->pObj->guiItems_registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems_registerFunc('getSearchBox', 'footer');
		$this->pObj->guiItems_registerFunc('getOptions', 'footer');
		$this->pObj->guiItems_registerFunc('getStoreControl', 'footer');
	}
	

	/**
	 * Main function
	 * 
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER, $LANG, $BACK_PATH, $TCA;

		$content = '';

		//
		// Use the current selection to create a query and count selected records
		//
		
		$this->pObj->addSelectionToQuery();
		$this->pObj->execSelectionQuery(TRUE);
		$this->pObj->setSelectionCounter();


		//
		// output header: info bar, result browser, ....
		//
			
		$content.= $this->pObj->guiItems_getOutput('header');
		$content.= $this->pObj->doc->spacer(10);


		if($this->pObj->resCountAll) {
	
			$pid = $this->pObj->defaultFolder;
			$pageinfo = t3lib_BEfunc::readPageAccess($pid,$this->pObj->perms_clause);
	
			$dblist = t3lib_div::makeInstance('tx_dam_db_list');
			$dblist->init();
	
			$dblist->backPath = $BACK_PATH;
			$dblist->calcPerms = $BE_USER->calcPerms($pageinfo);
			$dblist->alternateBgColors=$this->pObj->modTSconfig['properties']['alternateBgColors']?1:0;

			$dblist->resCountAll = $this->pObj->resCountAll;
			$dblist->pointer = $this->pObj->pointer;
			$dblist->resultsPerPage= $this->pObj->resultsPerPage;
			$dblist->firstItemNum = $this->pObj->firstItemNum;
			$dblist->lastItemNum = $this->pObj->lastItemNum;
	
			$dblist->searchString = trim(t3lib_div::_GP('search_field'));
			$dblist->sortField = t3lib_div::_GP('sortField');
			$dblist->sortRev = t3lib_div::_GP('sortRev');
	
	
			$dblist->setDispFields();
			#debug($dblist->setFields);
			#		$fieldList	= 'tx_dam.'.implode(',tx_dam.',t3lib_div::trimExplode(',',$dblist->setFields['tx_dam'],1));
			#		$this->pObj->qg->query['FROM']['tx_dam']=$fieldList;
			#debug($fieldList);
			$orderBy = ($TCA['tx_dam']['ctrl']['sortby']) ? 'tx_dam.'.$TCA['tx_dam']['ctrl']['sortby'] : 'tx_dam.sorting';

			if ($dblist->sortField)	{
				if (in_array($dblist->sortField,$dblist->makeFieldList('tx_dam',1)))	{
					$orderBy = 'tx_dam.'.$dblist->sortField;
					if ($dblist->sortRev)	$orderBy.=' DESC';
				}
			}
	
			$this->pObj->qg->query['ORDERBY']['tx_dam'] = $orderBy;
	
			$this->pObj->addLimitToQuery();
			$dblist->res = $this->pObj->execSelectionQuery();
	

#TODO ???				// It is set, if the clickmenu-layer is active AND the extended view is not enabled.
			$dblist->dontShowClipControlPanels = $CLIENT['FORMSTYLE'] && !$BE_USER->uc['disableCMlayers'];
	
			$dblist->generateList();
	
	
				// JavaScript
			$this->pObj->doc->JScodeArray['redirectUrls'] = $this->pObj->doc->redirectUrls(t3lib_extMgm::extRelPath('dam').'mod_list/'.$dblist->listURL());
			$this->pObj->doc->JScodeArray['jumpExt'] = '
				function jumpExt(URL,anchor)	{
					var anc = anchor?anchor:"";
					document.location = URL+(T3_THIS_LOCATION?"&returnUrl="+T3_THIS_LOCATION:"")+anc;
				}
				';
			
	
			$content.= '<form action="'.$dblist->listURL().'" method="POST" name="dblistForm">';
			$content.= $dblist->HTMLcode;
			$content.= '<input type="hidden" name="cmd_table"><input type="hidden" name="cmd"></form>';
			$content.= $dblist->fieldSelectBox();


		} else {
				// no search result: showing selection box
			$content.= $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		}

		return $content;
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_list_list.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_list_list.php']);
}

?>