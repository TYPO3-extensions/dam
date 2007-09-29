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
 * Command module 'file rename'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   65: class tx_dam_cmd_filenew extends t3lib_extobjbase
 *   76:     function head()
 *  107:     function main()
 *  150:     function editForm()
 *  197:     function renameFile()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file rename command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filenew extends t3lib_extobjbase {


	var $rec = array();


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global  $LANG, $BACK_PATH, $TYPO3_CONF_VARS;

		$this->target = t3lib_div::_GP('target');

		$this->filename = basename($this->target);
		$this->target = dirname($this->target);

			// Init basic-file-functions object:
		$this->basicff = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->basicff->init($GLOBALS['FILEMOUNTS'],$GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);

			// Cleaning and checking target
		$this->target = tx_dam::path_makeAbsolute($this->target);
		$this->target = $this->basicff->is_directory($this->target);
		$key = $this->basicff->checkPathAgainstMounts($this->target.'/');
		if (!$this->target || !$key)	{
			$this->target = false;
		}


		$GLOBALS['SOBE']->pageTitle = $LANG->sL('LLL:EXT:lang/locallang_core.xml:file_edit.php.pagetitle');


	}

	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global  $LANG;

			// Make page header:
		$content='';

		if ($this->target) {

			$pathInfo = tx_dam::path_compileInfo($this->target);


			$content.= tx_dam_guiFunc::getFolderInfoBar($pathInfo);

			$content.= $GLOBALS['SOBE']->doc->spacer(10);


				// Making the formfields for renaming:
			$code = $this->editForm();
				// Add the HTML as a section:
			$content.= $GLOBALS['SOBE']->doc->section('',$code);

		} else {

			$content.= $this->pObj->wrongCommandMessage();
		}


		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);

			// CSH:
#		$code.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_rename', $GLOBALS['BACK_PATH'],'<br/>');

		return $content;
	}




	/**
	 * Main function, redering the actual content of the editing page
	 *
	 * @return	void
	 */
	function editForm()	{
		global $BE_USER, $LANG, $TYPO3_CONF_VARS;


		$fI = pathinfo($this->filename);
		$extList = $TYPO3_CONF_VARS['SYS']['textfile_ext'];
$fI['extension'] = 'txt';
		if ($extList && t3lib_div::inList($extList,strtolower($fI['extension'])))		{
				// Read file content to edit:
#			$fileContent = t3lib_div::getUrl($this->target);

				// making the formfields
			$hValue = 'file_edit.php?target='.rawurlencode($this->origTarget).'&returnUrl='.rawurlencode($this->returnUrl);
			$code = '';
			$code.='
				<div id="c-submit">
					<input type="hidden" name="redirect" value="'.htmlspecialchars($hValue).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_edit.php.submit',1).'" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_edit.php.saveAndClose',1).'" onclick="document.editform.redirect.value=\''.htmlspecialchars($this->returnUrl).'\';" />
					<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="backToList(); return false;" />
				</div>
				';

				// Edit textarea:
			$code.='
				<div id="c-edit">
					<textarea rows="30" name="file[editfile][0][data]" wrap="off"'.$this->pObj->doc->formWidthText(48,'width:98%;height:80%','off').'>'.
					t3lib_div::formatForTextarea($fileContent).
					'</textarea>
					<input type="hidden" name="file[editfile][0][target]" value="'.$this->target.'" />
				</div>
				<br />';

		} else {
			$code.=htmlspecialchars(sprintf($LANG->sL('LLL:EXT:lang/locallang_core.xml:file_edit.php.coundNot'), $extList));
		}

		$this->content.= $code;

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
				$filepath = tx_dam::path_makeAbsolute($row['file_path']).$row['file_name'];
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
// TODO tcemain or tx_dam_db
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $fields_values);

				$this->rec = t3lib_BEfunc::getRecord('tx_dam', $row['uid']);
			}
		}
		return $error;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filenew.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filenew.php']);
}


?>