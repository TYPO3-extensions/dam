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
 * Module extension (addition to function menu) 'File Relation Check' for the 'Media>Info' module..
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage tools
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_dam_tools_filerelcheck extends t3lib_extobjbase
 *   68:     function main()
 *  125:     function moduleContent()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module 'Media>Tools>File check'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage tools
 */
class tx_dam_tools_filerelcheck extends t3lib_extobjbase {



	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		$content = '';

		//
		// Use the current selection to create a query and count selected records
		//

			// db query
//		$this->pObj->selection->addSelectionToQuery();
//
//
//		$this->pObj->selection->qg->queryAddMM($mm_table='tx_dam_mm_ref',$foreign_table='',$local_table='tx_dam');
//		$this->pObj->selection->execSelectionQuery(TRUE);
//
//
//		//
//		// output header: info bar, result browser, ....
//		//
//
//		$content.= $this->pObj->guiItems->getOutput('header');
//		$content.= $this->pObj->doc->spacer(10);
//
//
//			// any records found?
//		if($this->pObj->selection->pointer->countTotal) {
//			$this->pObj->selection->qg->query['FROM']['tx_dam'] = tx_dam_db::getMetaInfoFieldList();
//			$this->pObj->selection->qg->query['FROM']['tx_dam_mm_ref'] = 'tx_dam_mm_ref.uid_foreign,tx_dam_mm_ref.tablenames,tx_dam_mm_ref.ident';
//
//			$this->pObj->selection->qg->query['ORDERBY']['tx_dam_mm_ref'] = 'tablenames';
//
//			$this->pObj->selection->addLimitToQuery();
//			$this->pObj->selection->res = $this->pObj->selection->execSelectionQuery();
//
//			$content.= $this->moduleContent();
//		}


		$msg = '<span style="color:red"> This module is experimental and might not give the expected results.</span>';
		$content.= $this->pObj->doc->section('Warning', $msg, 0 , 0, 1);



		$content.= $this->moduleContent();

		return $content;
	}






	/**
	 * Generates the module content
	 */
	function moduleContent()	{
		$path = t3lib_div::getFileAbsFileName($this->pObj->path);
		$path = $path ? $path : t3lib_div::getFileAbsFileName('fileadmin/');
		$content = '';
		if ($path)	{

				// init table layout
			$refTableLayout = array(
				'table' => array('<table cellpadding="2" cellspacing="1" border="0" width="100%">','</table>'),
				'0' => array(
					'defCol' => array('<th nowrap="nowrap" class="bgColor5">','</th>')
				),
				'defRow' => array(
					'defCol' => array('<td nowrap="nowrap" class="bgColor4">','</td>'),
				),
			);
			$cTable=array();
			$tr=0;
			$td=0;
			$cTable[$tr][$td++] = 'uid';
			$cTable[$tr][$td++] = 'File';
			$cTable[$tr][$td++] = 'Message';
			$tr++;


			$content.=$this->pObj->doc->section('File / DAM relation overview:',tx_dam::path_makeRelative($path),0,1);

				// Select all files and register them in an array where the relative path is the key:
			$fileArr = array();
			$allFiles = array_flip(t3lib_div::removePrefixPathFromList(t3lib_div::getAllFilesAndFoldersInPath($fileArr,$path),PATH_site));
			$foundFiles = array();
			$removeUids = $outsidePathUids = array();

				// Select all DAM rows:
			$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike(tx_dam::path_makeRelative($path), 'tx_dam');
			$where = 'tx_dam.file_path LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');
			$damRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,file_name,file_path,file_mtime','tx_dam',$where,'','file_path,file_name,tstamp DESC',1000);

				// Traverse all DAM rows and check status
			$tRows = array();
			foreach($damRows as $row)	{
				$filePath = $row['file_path'].$row['file_name'];

				if (t3lib_div::isFirstPartOfStr(PATH_site.$row['file_path'],$path) || t3lib_div::isAbsPath($row['file_path']))	{
					if (isset($allFiles[$filePath]))	{	// Is found:
						unset($allFiles[$filePath]);
						$foundFiles[$filePath] = 1;

						$fMtime = filemtime(PATH_site.$filePath);
						if ($row['file_mtime']==$fMtime)	{
							$msg = 'OK';
						} else {
							$msg = $this->pObj->doc->icons(1).'NOTICE: file has an mtime '.t3lib_BEfunc::calcAge($fMtime-$row['file_mtime']).' seconds larger than the indexed record has. Update indexing.';
						}
					} elseif (isset($foundFiles[$filePath]))	{	// Is found twice or more!
						$msg = $this->pObj->doc->icons(3).'ERROR: Duplicate <br/> file is found AGAIN in the database (REMOVE record "tx_dam:'.$row['uid'].'")';
						$removeUids[] = $row['uid'];
					} else {	// Was not a file...
						$msg = $this->pObj->doc->icons(3).'ERROR: Not found <br/>file is not found in the filesystem! (REMOVE record "tx_dam:'.$row['uid'].'")';
						$removeUids[] = $row['uid'];
					}
				} else {
					$msg = '<em>Outside path. not tested</em>';
					$outsidePathUids[] = $row['uid'];
				}

					// Add row to table
				$td=0;
				$cTable[$tr][$td++] = htmlspecialchars($row['uid']);
				$cTable[$tr][$td++] = htmlspecialchars(preg_replace('#^'.$this->pObj->path.'#','',$filePath));
				$cTable[$tr][$td++] = $msg;
				$tr++;

			}

			$out = '';

				// Return rendered table
			if(count($cTable) > 1){
				$out.= $this->pObj->doc->table($cTable, $refTableLayout);
			}
			$out.='<hr/>Remove UIDs: <br/><textarea cols="80" rows="3">DELETE FROM tx_dam WHERE uid IN('.implode(',',$removeUids).')</textarea>';
			$out.='<hr/>Outside paths UIDs: <br/><textarea cols="80" rows="3">'.implode(',',$outsidePathUids).'</textarea>';

			$content.= $this->pObj->doc->section('Indexed files and their status:', $out, 0 , 1);

			$content.= $this->pObj->doc->section('Non-indexed files:', t3lib_div::view_array($allFiles), 0 , 1);
		}
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_filerelcheck/class.tx_dam_tools_filerelcheck.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_tools_filerelcheck/class.tx_dam_tools_filerelcheck.php']);
}

?>