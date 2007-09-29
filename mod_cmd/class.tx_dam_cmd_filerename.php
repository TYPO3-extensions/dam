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
 * Command module 'file rename'
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
 * Class for the file rename command
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_cmd_filerename extends t3lib_extobjbase {


	var $rec = array();


	/**
	 * Do some init things and set some things in HTML header
	 * 
	 * @return	void		
	 */
	function head() {
		global $SOBE, $LANG, $BACK_PATH, $TYPO3_CONF_VARS;


//
//			// Cleaning and checking target
//		if (@file_exists($this->pObj->target))	{
//			$this->pObj->target = $SOBE->basicff->cleanDirectoryName($this->pObj->target);		// Cleaning and checking target (file or dir)
//		} else {
//			$this->pObj->target = '';
//		}
//		$key = $SOBE->basicff->checkPathAgainstMounts($this->pObj->target.'/');
//		if (!$this->pObj->target || !$key)	{
//			t3lib_BEfunc::typo3PrintError ('Parameter Error','Target was not a directory!','');
//			exit;
//		}
//
//			// Finding the icon
//		switch($GLOBALS['FILEMOUNTS'][$key]['type'])	{
//			case 'user':	$this->icon = 'gfx/i/_icon_ftp_user.gif';	break;
//			case 'group':	$this->icon = 'gfx/i/_icon_ftp_group.gif';	break;
//			default:		$this->icon = 'gfx/i/_icon_ftp.gif';	break;
//		}
//
//			// Relative path to filemount, $key:
//		$this->shortPath = substr($this->pObj->target,strlen($GLOBALS['FILEMOUNTS'][$key]['path']));
//
//			// Setting title:
//		$this->title = $GLOBALS['FILEMOUNTS'][$key]['name'].': '.$this->shortPath;



		$SOBE->pageTitle = $LANG->sL('LLL:EXT:lang/locallang_core.php:file_rename.php.pagetitle');

		$id = FALSE;
		if(is_array($this->pObj->data['tx_dam_simpleforms'])) {
			$id = intval(key($this->pObj->data['tx_dam_simpleforms']));
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

			if (is_array($this->pObj->data['tx_dam_simpleforms'])) {
					// do the renaming:
				$error = $this->renameFile();

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
			$code = $this->renameForm();
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
	 * Making the formfields for renaming
	 * 
	 * @return	string		HTML content
	 */
	function renameForm()	{
		global $TCA, $BACK_PATH, $LANG, $FILEMOUNTS;

		$content='';

		$row = $this->rec;

		$filename = tx_dam_div::getAbsPath($row['file_path'].$row['file_name']);
		if($id = $row['uid']) {

			//
			// Create a edit form with tceforms
			//


			require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
			$form = t3lib_div::makeInstance('tx_dam_simpleForms');

			$form->initDefaultBEmode();
			$form->setVirtualTable('tx_dam_simpleforms', 'tx_dam');
			$form->setNewBEDesign(FALSE);
			$form->setNoneToEditable($TCA['tx_dam_simpleforms']);
			$form->removeRequired($TCA['tx_dam_simpleforms']);

			$columnsOnly = 'title,file_name,file_dl_name';
			$code = $form->getListedFields('tx_dam_simpleforms', $row, $columnsOnly);
			$content.= $form->wrapTotal($code, $row, 'tx_dam_simpleforms');

			$SOBE->doc->JScode .='
			'.$form->printNeededJSFunctions_top();
			$content.= $form->printNeededJSFunctions();

			$form->removeVirtualTable('tx_dam_simpleforms');


				// Making submit button:
			$content.= '
				<div id="c-submit">
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:file_rename.php.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.cancel',1).'" onclick="jumpBack(); return false;" />
					<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->returnUrl).'" />
				</div>
			';
		}


		return $content;

	}

	/**
	 * Rename the file and process DB update
	 * 
	 * @return	void
	 */	
	function renameFile() {
		$error = FALSE;

		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();

		$row = $this->rec;

		if($id = $row['uid']) {
			$data = $this->pObj->data['tx_dam_simpleforms'][$row['uid']];
			if (is_array($data)) {
				$filepath = tx_dam_div::getAbsPath($row['file_path'].$row['file_name']);
				$org_filename = $row['file_name'];
				$new_filename = $data['file_name'];
				if ($new_filename AND ($new_filename!=$row['file_name']) AND @file_exists($filepath)) {

						// Processing rename file
					$cmd = array();
					$cmd['rename'][$id]['target'] = $filepath;
					$cmd['rename'][$id]['data'] = $new_filename;

					$file->setCmdmap($cmd);
					$log = $file->process();

					if ($file->errors()) {
						$error = $file->getLastError();
					} else {
						$org_filename = $new_filename;
					}

				}

					// rename meta data field
				$fields_values = array(
					'file_name' => $org_filename,
					'file_dl_name' => $data['file_dl_name'] ? $data['file_dl_name'] : $row['file_dl_name'],
					'title' => $data['title'],
					);
#TODO tcemain or tx_dam_db
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $fields_values);

				$this->rec = t3lib_BEfunc::getRecord('tx_dam', $row['uid']);
			}
		}
		return $error;
	}
}


//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filerename.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filerename.php']);
//}


?>