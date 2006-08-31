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
 * Command module 'file delete'
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
 *   64: class tx_dam_cmd_filedelete extends t3lib_extobjbase
 *   75:     function head()
 *  101:     function main()
 *  158:     function deleteForm()
 *  196:     function deleteFile()
 *  272:     function getReferencesTable()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file delete command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage File
 */
class tx_dam_cmd_filedelete extends t3lib_extobjbase {

	var $passthroughMissingFiles = true;

	/**
	 * Additional access check
	 *
	 * @return	boolean Return true if access is granted
	 */
	function accessCheck() {
		return tx_dam::access_checkAction('deleteFile');
	}


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		$GLOBALS['SOBE']->pageTitle = $GLOBALS['LANG']->getLL('tx_dam_cmd_filedelete.title');
	}


	/**
	 * Returns a help icon for context help
	 *
	 * @return	string HTML
	 */
	function getContextHelp() {
// TODO csh
#		return t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_delete', $GLOBALS['BACK_PATH'],'');
	}


	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global  $LANG;

		$content = '';

			// Cleaning and checking target
		if ($this->pObj->file[0]) {
			$this->file = tx_dam::file_compileInfo($this->pObj->file[0], true);
			$this->meta = tx_dam::meta_getDataForFile($this->file, '*');
		} elseif ($id = intval($this->pObj->record['tx_dam'][0])) {
			$this->meta = tx_dam::meta_getDataByUid($id, '*');
			$this->file = tx_dam::file_compileInfo($this->meta, true);
		}
		if (!is_array($this->meta)) {
			$fileType = tx_dam::file_getType ($this->file);
			$this->meta = array_merge($this->file, $fileType);
			$this->meta['uid'] = 0;
		}

		if ($this->file['file_accessable']) {

			if (is_array($this->pObj->data) AND $this->pObj->data['delete']) {

				$error = tx_dam::process_deleteFile($this->file);

				if ($error) {
					$content .= $GLOBALS['SOBE']->getMessageBox ($LANG->getLL('error'), htmlspecialchars($error), $this->pObj->buttonBack(0), 2);

				} else {
					$this->pObj->redirect();
				}


			} else {
				$content.=  $this->renderForm();
			}

		} else {
				// this should have happen in index.php already
			$content.= $this->pObj->accessDeniedMessage($this->file['file_name']);
		}

		return $content;
	}


	/**
	 * Rendering the delete file form
	 *
	 * @return	string		HTML content
	 */
	function renderForm()	{
		global  $BACK_PATH, $LANG;

		$id = $this->meta['uid'];
		$filepath = tx_dam::file_absolutePath($this->file);

		$content = '';


		if ($this->meta['uid']) {
			$references = tx_dam_guiFunc::getReferencesTable($this->meta['uid']);
			if ($references) {
				$msg = $LANG->getLL('tx_dam_cmd_filedelete.messageReferences',1);
				$msg .= $GLOBALS['SOBE']->doc->spacer(5);
				$references = $GLOBALS['SOBE']->doc->section($LANG->getLL('tx_dam_cmd_filedelete.references',1), $msg.$references,0,0,0);
			}
		}


		$msg = array();

		$msg[] = tx_dam_guiFunc::getRecordInfoHeaderExtra($this->meta);

		if ($references) {
			$msg[] = '&nbsp;';
			$msg[] = '<strong><span class="typo3-red">'.$LANG->getLL('labelWarning',1).'</span> '.$LANG->getLL('tx_dam_cmd_filedelete.messageReferencesUsed',1).'</strong>';
			$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.messageReferencesDelete',1);
		}

		$msg[] = '&nbsp;';
		$msg[] = $LANG->getLL('tx_dam_cmd_filedelete.message',1);

		$buttons = '
			<input type="hidden" name="data[delete]['.$id.'][data]" value="'.htmlspecialchars($filepath).'" />
			<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filedelete.submit',1).'" />
			<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />';


		$content .= $GLOBALS['SOBE']->getMessageBox ($GLOBALS['SOBE']->pageTitle, $msg, $buttons, 1);

		$content .= $GLOBALS['SOBE']->doc->spacer(5);

		$content .= $references;

		return $content;
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php']);
}


?>