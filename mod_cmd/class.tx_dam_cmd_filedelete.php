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
 * Command module 'file delete'
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
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */





require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file delete command
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_cmd_filedelete extends t3lib_extobjbase {


	var $rec = array();


	/**
	 * Do some init things and set some things in HTML header
	 * 
	 * @return	void		
	 */
	function head() {
		global $SOBE, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;


		$SOBE->pageTitle = $LANG->getLL('tx_dam_cmd_filedelete.title');
		
		$id = FALSE;
		if(is_array($this->pObj->data['delete'])) {
			$id = intval(key($this->pObj->data['delete']));
		}
		$id = $id ? $id : intval(t3lib_div::_GP('id'));
		if ($id) {
			$row = t3lib_BEfunc::getRecord('tx_dam', $id);		
			$this->rec = $row;
		}
			
		
	}

	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global $SOBE, $LANG;

			// Make page header:
		$content='';

		if (is_array($this->rec)) {

			$error = '';
			
			if (is_array($this->pObj->data['delete'])) {
					// do the renaming:
				$error = $this->deleteFile();
			
				if(!$error) {
					$this->pObj->redirect();
				}
				
			}
	
			$content.= tx_dam_div::getDAMRecordInfo($this->rec);
			$content.= '<br />';
				
	
				// output error message
			if($error) {
				$content.= $SOBE->doc->section('Error',htmlspecialchars($error),0,1,2);
				$content.= $SOBE->doc->spacer(15);
			}
					
										
				// Making the formfields for renaming:
			$code = $this->deleteForm();
				// Add the HTML as a section:
			$content.= $SOBE->doc->section('',$code);

		} else {

			$content.= $this->pObj->wrongCommandMessage();
		}
		

		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);

			// CSH:
#		$code.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_rename', $GLOBALS['BACK_PATH'],'<br/>');

		return $content;
	}



	/**
	 * Rendering the delete file form 
	 * 
	 * @return	string		HTML content
	 */
	function deleteForm()	{
		global $SOBE, $BACK_PATH, $LANG, $FILEMOUNTS;

		$id = $this->rec['uid'];
		$filepath = tx_dam_div::getAbsPath($this->rec['file_path']).$this->rec['file_name'];
				
		$content = '';

		$msg = $LANG->getLL('tx_dam_cmd_filedelete.message',1);
		
		$content.= $SOBE->doc->section($LANG->getLL('tx_dam_cmd_filedelete.warning',1),htmlspecialchars($msg),0,1,2);
		$content.= $SOBE->doc->spacer(5);
		
			// Making submit button:
		$content.= '
			<div id="c-submit">
				<input type="hidden" name="data[delete]['.$id.'][data]" value="'.htmlspecialchars($filepath).'" /><br />
				<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filedelete.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.cancel',1).'" onclick="jumpBack(); return false;" />
				<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->returnUrl).'" />
			</div>
		';		

		return $content;
	}

	
	/**
	 * Rename the file and process DB update
	 * 
	 * @return	void
	 */	
	function deleteFile() {
		$error = FALSE;
		
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();
			
		$row = $this->rec;
		
		if($id = $row['uid']) {
			$data = $this->pObj->data['delete'][$row['uid']];
			if (is_array($data)) {

				
				//
				// Processing delete
				//
				
				$file->setCmdmap($this->pObj->data);
				$log = $file->process();
				
				if ($file->errors()) {
					
					$error = $file->getLastError();
					
				} else {

				
					//
					// update DB
					//
				
					$org_filename = $row['file_name'];
					
					$newFile = $log['cmd']['upload'][$id]['target_file'];
					$new_filename = basename($newFile);			
					$new_filename = $new_filename ? $new_filename : $org_filename;
					
					$new_path = $log['cmd']['upload'][$id]['target_path'];
					
							// rename meta data fields
						$fields_values = array();
						$fields_values['deleted'] = 1;
#TODO move record to recycler?
						if ($org_filename != $new_filename) {
							$fields_values['file_name'] = $new_filename;
						}
						if ($new_path) {
							$fields_values['file_path'] = tx_dam_div::getRelPath($new_path);
						}

#TODO tcemain or tx_dam_db
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $fields_values);
						
						$this->rec = t3lib_BEfunc::getRecord('tx_dam', $row['uid']);			
					
				}
	
			}
		}
		return $error;	
	}

	
}


//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/tx_dam_cmd_filedelete.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/tx_dam_cmd_filedelete.php']);
//}


?>