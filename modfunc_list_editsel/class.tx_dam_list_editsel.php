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
 * Module extension (addition to function menu) 'edit selection' for the 'Media>List' module.
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
 *   58: class tx_dam_list_editsel extends t3lib_extobjbase 
 *   65:     function modMenu()    
 *   79:     function head() 
 *   92:     function main()    
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module extension  'Media>List>Selection'
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_list_editsel extends t3lib_extobjbase {

	/**
	 * Function menu initialization
	 * 
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return Array (
			'tx_dam_list_editsel_onlyDeselected' => '1',
		);
	}


	/**
	 * Do some init things and aet some styles in HTML header
	 * 
	 * @return	void		
	 */
	function head() {
		global $LANG;

		//
		// Init gui items and ...
		//
		
		$this->pObj->guiItems_registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems_registerFunc('getResultBrowser', 'header');
		
		$this->pObj->guiItems_registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems_registerFunc('getSearchBox', 'footer');
		$this->pObj->guiItems_registerFunc('getOptions', 'footer');
		$this->pObj->guiItems_registerFunc('getStoreControl', 'footer');
		
			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_editsel_onlyDeselected', $LANG->getLL('tx_dam_list_editsel.onlyDeselected'));
	}
	

	/**
	 * Main function
	 * 
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER, $LANG, $BACK_PATH;

		$content = '';

		//
		// current selection box
		//
		
		$content.= $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		$content.= $this->pObj->doc->spacer(25);
		
		
		//
		// get records by query depending on option 'Show deselected only'
		//	
	
		$origSel = $this->pObj->sl->sel;
		if($this->pObj->MOD_SETTINGS['tx_dam_list_editsel_onlyDeselected']) {
			if(is_array($this->pObj->sl->sel['DESELECT_ID']['tx_dam'])) {
				$ids = array_keys($this->pObj->sl->sel['DESELECT_ID']['tx_dam']);
			} else {
				$ids = array(0); //dummy
			}

			unset($this->pObj->sl->sel['DESELECT_ID']);
			$this->pObj->addSelectionToQuery();
			if(is_array($ids)) {
				$this->pObj->qg->query['WHERE']['WHERE']['DESELECT_ID'] = 'AND tx_dam.uid IN ('.implode(',',$ids).')';
			}

		} else {
			unset($this->pObj->sl->sel['DESELECT_ID']);
			$this->pObj->addSelectionToQuery();
		}
		$this->pObj->sl->sel = $origSel;



		//
		// Use the current selection to create a query and count selected records
		//
		
		$this->pObj->execSelectionQuery(TRUE);
		$this->pObj->setSelectionCounter();
		
		
		//
		// output header: info bar, result browser, ....
		//
			
		$content.= $this->pObj->guiItems_getOutput('header');
		$content.= $this->pObj->doc->spacer(10);


			// any records found?
		if($this->pObj->resCountAll) {
	
				// limit query for browsing
			$this->pObj->addLimitToQuery();
			$this->pObj->execSelectionQuery();

			//
			// create record table
			//
			
			$row_altBbgColor=' bgColor="'.t3lib_div::modifyHTMLColor($GLOBALS['SOBE']->doc->bgColor4,+10,+10,+10).'"';
			#$this->alternateBgColors = true;	
	
				// simple list
			$items=array();
			$cc=0;
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->pObj->res)) {
				$cc++;
				
				$iconfile = t3lib_iconWorks::getIcon('tx_dam',$row);
				$alttext = t3lib_BEfunc::getRecordIconAltText($row,'tx_dam');
				$theIcon = '<img src="'.$BACK_PATH.$iconfile.'" width="18" height="16" border="0" title="'.$alttext.'" />';				

				if($this->pObj->sl->sel['DESELECT_ID']['tx_dam'][$row['uid']]) {
					$bgColor = $row_altBbgColor;
					$params='SLCMD[DESELECT_ID][tx_dam]['.$row['uid'].']=0';
					$actionIcon='<a href="index.php?'.$params.'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/button_reselect.gif" width="11" height="10" border="0" title="'.$LANG->getLL('reselect').'" align="top" alt="" /></a>';
				} else {
					$bgColor = '';
					$params='SLCMD[DESELECT_ID][tx_dam]['.$row['uid'].']=1';
					$actionIcon='<a href="index.php?'.$params.'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/button_deselect.gif" width="11" height="10" border="0" title="'.$LANG->getLL('deselect').'" align="top" alt="" /></a>';
				}

				
				$itemOut=array();
				$itemOut[]= $actionIcon;
				$itemOut[]= $theIcon;
				$itemOut[]= htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'],25));
				$itemOut[]= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'] :'';
				#$itemOut[]= $row['color_space'] ? $LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tx_dam','color_space',$row['color_space'])) : '';
				$itemOut[]= htmlspecialchars(t3lib_div::fixed_lgd_cs(str_replace("\n",' ',trim($row['description'])),50));
	
				#$bgColor = $this->alternateBgColors ? (($cc%2)?$row_altBbgColor : '') : '';
				$items[] = '<td'.$bgColor.' style="padding-left:5px;">'.implode('</td><td'.$bgColor.' style="padding-left:5px;">',$itemOut).'</td>';
			}
			if(count($items)) {
				$content.= $this->pObj->doc->section('','<table cellspacing="1" cellpadding="0" border="0" width="100%"><tr>'.implode('</tr><tr>',$items).'</tr></table>',0,1);
			}
		}
			
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_editsel/class.tx_dam_list_editsel.php']);
}

?>