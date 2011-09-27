<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@typo3.org>
 *  Lorenz Ulrich <lorenz.ulrich@visol.ch>
 *
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
 *
 *
 * @package dam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Dam_Controller_IndexingController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

	}

	public function indexAction() {
		$pathName = 'typo3conf/ext/dam/Tests/MimeType/test.pdf';

		echo 'File name for testing: ' . $pathName;

		$fileMimeType = $this->getFileMimeType($pathName);

		echo '<br>MIME type: ' . $fileMimeType;

		$fileAssetType = $this->getFileAssetType($fileMimeType);

		echo '<br>Asset type: ' . $fileAssetType;

		$metaData = $this->getFileMetaInfo($pathName, $fileMimeType, array());

		echo '<br>Additional meta data: ';
		var_dump($metaData);



	}

	/**
	 * Get the MIME type of a file with PHP function finfo::file
	 *
	 * @param	string		$pathname absolute path to file
	 * @return	array		file information
	 */
	private function getFileMimeType($pathName) {

		$absolutePathName = t3lib_div::getFileAbsFileName($pathName);
		$fileInfo = new finfo;
     	$fileMimeType = $fileInfo->file($absolutePathName, FILEINFO_MIME_TYPE);

		return $fileMimeType;

	}

	/**
	 * Get the uid of the Asset type by a MIME type
	 *
	 * @param	string		$mimeType of a file
	 * @return	array		uid of asset type
	 */
	private function getFileAssetType($mimeType) {

		// Todo: Move this query to the appropriate place.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('asset_type', 'tx_dam_domain_model_mimetype', 'mime_type="' . $mimeType . '"');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		return $row['asset_type'];

	}

	/**
	 * Get meta information from a file using a metaExtract service
	 *
	 * @param	string		file with absolute path
	 * @param	string		file MIME type
	 * @param	array		current file meta information which should be extended
	 * @return	array		file meta information
	 * @todo what about using services in a chain?
	 */
	private function getFileMetaInfo($pathName, $mimeType, $metaData) {

		$absolutePathName = t3lib_div::getFileAbsFileName($pathName);

		// find a service for that file type
		$serviceObject = t3lib_div::makeInstanceService('metaExtract', $mimeType);

		if (is_object($serviceObject)) {
			$serviceObject->setInputFile($absolutePathName, $mimeType);
			$conf['meta'] = $metaData;
			if ($serviceObject->process() > 0 && (is_array($svmeta = $serviceObject->getOutput()))) {
				$metaData = t3lib_div::array_merge_recursive_overrule($metaData, $svmeta);
			}
			$serviceObject->process();
			$serviceObject->__destruct();
			unset($serviceObject);
		}

		return $metaData;

	}

}
?>