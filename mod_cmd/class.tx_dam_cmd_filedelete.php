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


	var $rec = array();


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global  $LANG, $BACK_PATH, $TYPO3_CONF_VARS;


		$GLOBALS['SOBE']->pageTitle = $LANG->getLL('tx_dam_cmd_filedelete.title');

		$id = FALSE;
		if(is_array($this->pObj->data['delete'])) {
			$id = intval(key($this->pObj->data['delete']));
		}
		$id = t3lib_div::_GP('id');
		if (t3lib_div::testInt($id)) {
			$row = t3lib_BEfunc::getRecord('tx_dam', $id);
			$this->rec = $row;
		} else {
			$this->rec = tx_dam::meta_getDataForFile($id);
		}


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

		if (is_array($this->rec)) {

			$error = '';

			if (is_array($this->pObj->data['delete'])) {
					// do the renaming:
				$error = $this->deleteFile();

				if(!$error) {
					$this->pObj->redirect();
				}

			}

			$content.= tx_dam_guiFunc::getRecordInfoHeader($this->rec);
			$content.= '<br />';


				// output error message
			if($error) {
				$content.= $GLOBALS['SOBE']->doc->section('Error',htmlspecialchars($error),0,1,2);
				$content.= $GLOBALS['SOBE']->doc->spacer(15);
			}


				// Making the formfields for renaming:
			$code = $this->deleteForm();
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
	 * Rendering the delete file form
	 *
	 * @return	string		HTML content
	 */
	function deleteForm()	{
		global  $BACK_PATH, $LANG, $FILEMOUNTS;

		$id = $this->rec['uid'];
		$filepath = tx_dam::path_makeAbsolute($this->rec['file_path']).$this->rec['file_name'];

		$content = '';

		$msg = $LANG->getLL('tx_dam_cmd_filedelete.message',1);

		$content.= $GLOBALS['SOBE']->doc->section($LANG->getLL('tx_dam_cmd_filedelete.warning',1),htmlspecialchars($msg),0,1,2);

			// Making submit button:
		$content.= '
			<div id="c-submit">
				<input type="hidden" name="data[delete]['.$id.'][data]" value="'.htmlspecialchars($filepath).'" /><br />
				<input type="submit" value="'.$LANG->getLL('tx_dam_cmd_filedelete.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />
				<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->returnUrl).'" />
			</div>
		';
		$content.= $GLOBALS['SOBE']->doc->spacer(10);

		$references = $this->getReferencesTable();
		if ($references) {
			$msg = $LANG->getLL('tx_dam_cmd_filedelete.messageReferences',1);
			$content.= $GLOBALS['SOBE']->doc->section($LANG->getLL('tx_dam_cmd_filedelete.references',1), $msg.$references,0,0,0);
		}

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

				if(@is_file($data['data'])){
					$file->setCmdmap($this->pObj->data);
					$log = $file->process();
					if ($file->errors()) {
						$error = $file->getLastError();
					}
				}

				if(!$error) {


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
// TODO move record to recycler?
						if ($org_filename != $new_filename) {
							$fields_values['file_name'] = $new_filename;
						}
						if ($new_path) {
							$fields_values['file_path'] = tx_dam::path_makeRelative($new_path);
						}

// TODO tcemain or tx_dam_db
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam', 'uid='.$row['uid'], $fields_values);

						$this->rec = t3lib_BEfunc::getRecord('tx_dam', $row['uid']);

						//
						// delete MM relation
						//

						$GLOBALS['TYPO3_DB']->exec_DELETEquery( 'tx_dam_mm_ref', 'tx_dam_mm_ref.uid_local='.$row['uid']) ;


				}

			}
		}
		return $error;
	}

	/**
	 * Render the table with referenced records
	 *
	 * @return	string		Rendered Table
	 */
	function getReferencesTable()   {
		global $BACK_PATH, $BE_USER, $LANG, $TCA;

			// init table layout
		$refTableLayout = array(
			'table' => Array('<table cellpadding="2" cellspacing="1" border="0" width="100%">','</table>'),
			'0' => array(
				'defCol' => Array('<th nowrap="nowrap" class="bgColor5">','</th>')
			),
			'defRow' => array(
				'defCol' => Array('<td nowrap="nowrap" class="bgColor4">','</td>'),
			),
		);
		$cTable=array();
		$tr = 0;
		$td = 0;
		$cTable[$tr][$td++] = 'Page';
		$cTable[$tr][$td++] = 'Content Element';
		$cTable[$tr][$td++] = 'Content Age';
//		$cTable[$tr][$td++] = 'Media Element';
//		$cTable[$tr][$td++] = 'Media Element Age';
		$tr++;


;
		$resMM = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'tx_dam_mm_ref.*',
						'tx_dam_mm_ref,tx_dam',
						'tx_dam_mm_ref.uid_local='.$this->rec['uid'].
							' AND tx_dam.uid='.$this->rec['uid'].
							t3lib_BEfunc::deleteClause('tx_dam'),
						'',
						'tstamp DESC',
						40
					);


		while($damRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resMM)) {

			$refTable = $damRow['tablenames'];

			if ($refTable) {

					// get main fields from TCA
				$selectFields = tx_dam_db::getTCAFieldListArray($refTable, TRUE);
				$selectFields = tx_dam_db::compileFieldList($refTable, $selectFields, FALSE);
				$selectFields = $selectFields ? $selectFields : ($refTable.'.uid,'.$refTable.'.pid');

					// Query for non-deleted tables only
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								$selectFields,
								$refTable,
								$refTable.'.uid='.$damRow['uid_foreign'].
									t3lib_BEfunc::deleteClause($refTable),
								'',
								'tstamp DESC',
								40
							);


				while($refRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {


					$pageRow = t3lib_BEfunc::getRecord('pages', $refRow['pid']);

					if (is_array($pageRow)) {

							// Create output item for pages record
						$contentPageLink = tx_dam_SCbase::getRecordInfoEditLink('pages', $pageRow, true);

							// Create output item for reference record
						$contentElementLink = tx_dam_SCbase::getRecordInfoEditLink($refTable, $refRow);

							// Create output text describing the age
						$contentAge = t3lib_BEfunc::dateTimeAge($refRow['tstamp'], 1);

//							// Create output item for tx_dam record
//						$damElementLink = tx_dam_SCbase::getRecordInfoEditLink('tx_dam', $damRow);
//
//							// Create output text describing the tx_dam record age
//						$damElementAge = t3lib_BEfunc::dateTimeAge($damRow['tstamp'], 1);


							// Add row to table
						$td=0;
						$cTable[$tr][$td++] = $contentPageLink;
						$cTable[$tr][$td++] = $contentElementLink;
						$cTable[$tr][$td++] = $contentAge;
//						$cTable[$tr][$td++] = $damElementLink;
//						$cTable[$tr][$td++] = $damElementAge;
						$tr++;
					}
				}
			}
		}

			// Return rendered table
		if(count($cTable) > 1){
			return $this->pObj->doc->table($cTable, $refTableLayout);
		}

		return false;
	}
}


//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_filedelete.php']);
//}


?>