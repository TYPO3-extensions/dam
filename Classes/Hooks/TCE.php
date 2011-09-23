<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * TCE hook handling
 *
 * @package     TYPO3
 * @subpackage  speciality
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version $Id: class.tx_speciality_tcehook.php 535 2010-10-12 10:19:30Z fudriot $
 */
class Tx_Dam_Hooks_TCE {

	/**
	 * delete file when record is deleted
	 */
//	function processCmdmap_preProcess($command, $table, $id, $value, $tce) {
//	}
	
	/**
	 * status TXDAM_status_file_changed will be reset when record was edited
	 *
	 * @param	string		action status: new/update is relevant for us
	 * @param	string		db table
	 * @param	integer		record uid
	 * @param	array		record
	 * @param	object		parent object
	 * @return	void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj) {
		if($table === 'tx_dam_domain_model_asset') {
			$file = array();
			if (!empty($pObj->uploadedFileArray['tx_dam_domain_model_asset']['_userfuncFile']['file'])) {
				$file = $pObj->uploadedFileArray['tx_dam_domain_model_asset']['_userfuncFile']['file'];
				
//				t3lib_utility_Debug::debug($file, '$file');
//				exit();
				// @todo create a FAL instance
				
				// @todo extract metadata service
			}
			
			#if ($rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_dam', 'uid='.intval($id), '', '', 1, 'uid')) {
			#	$row = $rows[$id];
			#	if ($row['file_status']==TXDAM_status_file_changed) {
			#		$fieldArray['file_status'] = TXDAM_status_file_ok;
			#	}
			#}
		}
	}
	
	
	/**
	 * Track uploads/* files
	 */
//	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $tce) {
//
//	}
}

?>
