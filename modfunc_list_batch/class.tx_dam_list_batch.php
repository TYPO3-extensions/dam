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
 * Module extension (addition to function menu) 'batch processing' for the 'Media>List' module.
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
 *   58: class tx_dam_list_batch extends t3lib_extobjbase 
 *   67:     function main()    
 *  222:     function getPresetForm ($rec,$fixedFields,$langKeyDesc) 
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

require_once(PATH_txdam.'lib/class.tx_dam_batchprocess.php');

/**
 * Module extension  'Media>List>Process'
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_list_batch extends t3lib_extobjbase {


	/**
	 * Initialize the class and set some HTML header code
	 * 
	 * @return	void		
	 */
	function head()	{

		//
		// Init gui items and ...
		//
		
//		$this->pObj->guiItems_registerFunc('getResultInfo', 'header');

//		$this->pObj->guiItems_registerFunc('getOptions', 'footer');
//		$this->pObj->guiItems_registerFunc('getStoreControl', 'footer');
	}
	
	
	/**
	 * Main function
	 * 
	 * @return	string		HTML output
	 */
	function main()    {
		global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA,$TYPO3_CONF_VARS;
		
		$content = '';

		//
		// Use the current selection to create a query and count selected records
		//
		
		$this->pObj->addSelectionToQuery();
		$this->pObj->execSelectionQuery(TRUE);
		$this->pObj->setSelectionCounter();
		
		
		if($this->pObj->resCountAll) {
			$batch = t3lib_div::makeInstance('tx_dam_batchProcess');
			
			if(t3lib_div::_GP($batch->startParam)) {
				
					// result info
				$content.= $this->pObj->doc->section('',$this->pObj->getHeaderBar('', $this->pObj->btn_back()),0,1);
				$content.= $this->pObj->doc->spacer(10);
				
				
				$batch->processGP();
				
				$infoFields = tx_dam_db::getInfoFieldListDAM();
				$this->pObj->execSelectionQuery(FALSE, ' DISTINCT '.$infoFields);
				
				$batch->runBatch($this->pObj->res);
				
				$content.= $batch->showResult();
			} else {
				
					// header with back button
				$content.= $this->pObj->doc->section('',$this->pObj->getHeaderBar($this->pObj->getResultInfo()),0,1);
				$content.= $this->pObj->doc->spacer(10);
				
				$content.= $batch->showPresetForm();
				
			}
		}

		return $content;
	}
	

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_batch/class.tx_dam_list_batch.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_batch/class.tx_dam_list_batch.php']);
}

?>