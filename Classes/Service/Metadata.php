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
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Media_Service_Metadata {

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

	}

	/**
	 * Get the condensed meta information of a file
	 *
	 * @param t3lib_file_Domain_Model_File $file an Media
	 * @return Tx_Media_Domain_Model_Media $media
	 */
	public function getMetadata(t3lib_file_Domain_Model_File $file) {

		$values = array();
		$values['media_type'] = $this->getFileMediaType($file);
		
		$extractedMetadata = $this->getFileMetaInfo($file->getAbsolutePath(), $values['mime_type']);
		if (!empty($extractedMetadata)) {
			$values = array_merge($fields, $extractedMetadata['fields']);
		}

		$media = t3lib_div::makeInstance('Tx_Media_Domain_Model_Media', $values);

		return $media;

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