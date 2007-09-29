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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-FeLib
 * @subpackage
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_dam_tsfe
 *
 *              SECTION: Misc functions
 *   81:     function fetchFileList ($content, $conf)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Provide TSFE functions for usage in own extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-FeLib
 * @subpackage
 */
class tx_dam_tsfe {




	/**********************************************************
	 *
	 * TypoScript functions
	 *
	 **********************************************************/


	/**
	 * Used to fetch a file list for TypoScript cObjects
	 *
	 *	tt_content.textpic.20.imgList >
	 *	tt_content.textpic.20.imgList.cObject = USER
	 *	tt_content.textpic.20.imgList.cObject {
	 *		userFunc = tx_dam_divFe->fetchFileList
	 *
	 * @param	mixed		$content: ...
	 * @param	array		$conf: ...
	 * @return	string		comma list of files with path
	 * @see dam_ttcontent extension
	 */
	function fetchFileList ($content, $conf) {
		$files = array();

		$filePath = $this->cObj->stdWrap($conf['additional.']['filePath'],$conf['additional.']['filePath.']);
		$fileList = trim($this->cObj->stdWrap($conf['additional.']['fileList'],$conf['additional.']['fileList.']));
		$refField = trim($this->cObj->stdWrap($conf['refField'],$conf['refField.']));
		$fileList = t3lib_div::trimExplode(',',$fileList);
		foreach ($fileList as $file) {
			if($file) {
				$files[] = $filePath.$file;
			}
		}

		$uid = $this->cObj->data['_LOCALIZED_UID'] ? $this->cObj->data['_LOCALIZED_UID'] : $this->cObj->data['uid'];
		$refTable = ($conf['refTable'] && is_array($GLOBALS['TCA'][$conf['refTable']])) ? $conf['refTable'] : 'tt_content';
		$damFiles = tx_dam_db::getReferencedFiles($refTable, $uid, $refField);

		$files = array_merge($files, $damFiles['files']);

		return implode(',',$files);
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tsfe.php']);
}

?>