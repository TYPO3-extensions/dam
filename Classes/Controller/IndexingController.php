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
		// this function is only for testing in backend

		$pathName = 'typo3conf/ext/dam/Tests/MimeType/test.pdf';

		echo 'File name for testing: ' . $pathName;

		$fileMimeType = $this->getFileMimeType($pathName);

		echo '<br>MIME type: ' . $fileMimeType;

		$fileAssetType = $this->getFileAssetType($fileMimeType);

		echo '<br>Asset type: ' . $fileAssetType;

		$metaData = $this->getFileMetaInfo($pathName, $fileMimeType);

		echo '<br>Additional meta data: ';
		var_dump($metaData);
		
		//$file->getMount()->getDriver()->getAbsolutePath()
	}

	/**
	 * Get the condensed meta information of a file
	 *
	 * @param t3lib_vfs_Domain_Model_File $file an Asset
	 * @return array file information
	 */
	public function getMetaData(t3lib_vfs_Domain_Model_File $file) {

		$fields = array();
		$fields['mime_type'] = $this->getFileMimeType($file);
		$fields['asset_type'] = $this->getFileAssetType($file);
		
		// @todo fix this! does not work because Zend_Pdf is not found
		// $extractedMetaData = $this->getFileMetaInfo($absolutePath, $fields['mime_type']);
		if (!empty($extractedMetaData)) {
			$fields = array_merge($fields, $extractedMetaData['fields']);
		}
		
		return $fields;
	}

	/**
	 * Get the extension of t3lib_vfs_Domain_Model_File $file
	 *
	 * @param t3lib_vfs_Domain_Model_File $file an Asset
	 * @return string the extension
	 */
	public function getExtension(t3lib_vfs_Domain_Model_File $file) {
		// @todo steal some code from the internet to get the extension name
		return 'pdf';
	}

	/**
	 * Get the MIME type of a file with PHP function finfo::file
	 *
	 * @param t3lib_vfs_Domain_Model_File $file an Asset
	 * @return array file information
	 */
	public function getFileMimeType(t3lib_vfs_Domain_Model_File $file) {
		$absolutePathName = $file->getAbsolutePath();
		$fileInfo = new finfo();
     	$fileMimeType = $fileInfo->file($absolutePathName, FILEINFO_MIME_TYPE);
		return $fileMimeType;
	}

	/**
	 * Get the uid of the Asset type by a MIME type
	 *
	 * @param t3lib_vfs_Domain_Model_File $asset an Asset
	 * @return array file information
	 */
	public function getFileAssetType(t3lib_vfs_Domain_Model_File $file) {

		// Todo: Move this query to the appropriate place.
		
		/* @var $GLOBALS['TYPO3_DB'] t3lib_DB */
		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('asset_type', 'tx_dam_domain_model_mimetype', 'mime_type_name = "' . $this->getFileMimeType($file) . '"');
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
	public function getFileMetaInfo($pathName, $mimeType, $metaData = array()) {

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

		return isset($metaData) ? $metaData : array();

	}

}
?>