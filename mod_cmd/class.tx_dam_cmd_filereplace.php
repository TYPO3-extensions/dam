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
 * Command module 'file replace'
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
 * Class for the file replace command
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_cmd_filereplace extends t3lib_extobjbase {


	var $rec = array();


	/**
	 * Do some init things and set some things in HTML header
	 * 
	 * @return	void		
	 */
	function head() {
		global $SOBE, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;


		$SOBE->pageTitle = $LANG->getLL('tx_dam_cmd_filereplace.title');
		
		$id = FALSE;
		if(is_array($this->pObj->data['upload'])) {
			$id = intval(key($this->pObj->data['upload']));
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
			
			if (is_array($this->pObj->data['upload'])) {
					// do the renaming:
				$error = $this->replaceFile();
			
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
			$code = $this->uploadForm();
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
	 * Rendering the upload file form fields
	 * 
	 * @return	string		HTML content
	 */
	function uploadForm()	{
		global $BACK_PATH, $LANG, $FILEMOUNTS;

		$id = $this->rec['uid'];
		$path = tx_dam_div::getAbsPath($this->rec['file_path']);
				
		$content = '';

				// Adding 'size="50" ' for the sake of Mozilla!
		$content.='
			<div id="c-upload">
				<input type="file" name="upload_'.$id.'"'.$this->pObj->doc->formWidth(35).' size="50" onclick="changed=1;" />
				<input type="hidden" name="data[upload]['.$id.'][target]" value="'.htmlspecialchars($path).'" />
				<input type="hidden" name="data[upload]['.$id.'][data]" value="'.$id.'" /><br />
			</div>
			';

			// Making submit button:
		$content.= '
			<div id="c-submit">
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:file_upload.php.submit',1).'" />
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
	function replaceFile() {
		$error = FALSE;
		
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();
		
#
#TODO overwrite only orig file not others
#

			// allow overwrite
		$file->fileProcessor->dontCheckForUnique = TRUE;
		
		$row = $this->rec;
		
		if($id = $row['uid']) {
			$data = $this->pObj->data['upload'][$row['uid']];
			if (is_array($data)) {
				
				
				
				//
				// Processing uploads
				//
				

				$file->setCmdmap($this->pObj->data);
				$log = $file->process();
				
				if ($file->errors()) {
					
					$error = $file->getLastError();
					
				} else {
					
					$org_filename = $row['file_name'];
					
					$newFile = $log['cmd']['upload'][$id]['target_file'];
					$new_filename = basename($newFile);			
	
					if ($new_filename) {				
							// rename meta data fields
						$fields_values = array();
						$fields_values['file_name'] = $new_filename;
						if ($org_filename != $new_filename) {
							$fields_values['file_dl_name'] = $new_filename;
							if($row['file_name']) {
								unlink(tx_dam_div::getAbsPath($row['file_path']).$row['file_name']);
							}
						}

#TODO tcemain or tx_dam_db
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $fields_values);
						
						$this->rec = t3lib_BEfunc::getRecord('tx_dam', $row['uid']);	
					}				
					
				}
	
			}
		}
		return $error;	
	}

	
}


//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/tx_dam_cmd_filereplace.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/tx_dam_cmd_filereplace.php']);
//}


?>