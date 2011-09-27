<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011
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
 * ************************************************************* */

/**
 * TCE hook handling
 *
 * @package     TYPO3
 * @subpackage  speciality
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version $Id: class.tx_speciality_tcehook.php 535 2010-10-12 10:19:30Z fudriot $
 */
class Tx_Dam_Hooks_TCE {

	/**
	 * The extension key
	 *
	 * @var string
	 */
	protected $extKey = 'dam';

	/**
	 * @var t3lib_vfs_Factory
	 */
	protected $factory;

	/**
	 * @var t3lib_vfs_Domain_Repository_MountRepository
	 */
	protected $mountRepository;

	/**
	 * @var t3lib_vfs_Domain_Model_Mount
	 */
	protected $mount;

	/**
	 * Is a child of t3lib_vfs_Service_Storage_AbstractDriver
	 *
	 * @var object
	 */
	protected $driver;

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	protected function initializeAction() {

			// Load preferences
		if ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]) {
			$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		}

			// Instantiate necessary stuff for FAL
		$this->factory = t3lib_div::makeInstance('t3lib_vfs_Factory');
		$this->mountRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_MountRepository');
		$this->mount = $this->mountRepository->findByUid($this->configuration['storage']);
		$this->driver = $this->mount->getDriver();
	}

	/**
	 * delete file when record is deleted
	 */
//	function processCmdmap_preProcess($command, $table, $id, $value, $tce) {
//	}

	/**
	 * status TXDAM_status_file_changed will be reset when record was edited
	 *
	 * @param	string		action status: new/update is relevant for us
	 * @param	string		db table
	 * @param	integer		record uid
	 * @param	array		record
	 * @param	object		parent object
	 * @return	void
	 */
	public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj) {
		if ($table === 'tx_dam_domain_model_asset') {
			$uploadedFile = array();
			if (!empty($pObj->uploadedFileArray['tx_dam_domain_model_asset']['_userfuncFile']['file']['name'])) {

					// Init action
				$this->initializeAction();

					// check if file must be overwritten
					// @todo fetch this config from TypoScript or so...
				if (TRUE) {
					$assetRepository = t3lib_div::makeInstance('Tx_Dam_Domain_Repository_AssetRepository');
					$asset = $assetRepository->findByUid($id);

						// @todo: check why it does not work
					$fileRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_FileRepository');
					$fileObject = $fileRepository->findByUid($asset->getFile());

						// @temporary code fileRepository
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('identifier', 'sys_file', 'uid = ' . $asset->getFile());
					$fileData = $this->driver->getFile($rows[0]['identifier']);
					$factory = t3lib_div::makeInstance('t3lib_vfs_Factory');
					$fileObject = $factory->createFileObject($fileData);

					$pObj->uploadedFileArray['tx_dam_domain_model_asset']['_userfuncFile']['file']['name'] = $fileObject->getName();
				}

				$uploadedFile = $pObj->uploadedFileArray['tx_dam_domain_model_asset']['_userfuncFile']['file'];
				$file = $this->upload($uploadedFile);
				$this->index($file);


				// @todo extract metadata service

					// Reset the file uid in case the relation would have changed -> new file created  instead of overwriting.
				$fieldArray['file'] = $file->getUid();
			}
		}
	}

	/**
	 * Index the file into the database
	 *
	 * @param string $file the file which has been uploaded
	 */
	protected function index($file) {
		/** @var t3lib_vfs_Domain_Repository_FileRepository $fileRepository */
		$fileRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_FileRepository');
		$fileRepository->addToIndex($file);
	}

	/**
	 * Upload the file to the right directory
	 *
	 * @param array $uploadedFile uploaded data of the file
	 */
	protected function upload($uploadedFile) {
		$path = Tx_Dam_Configuration_Static::$assetDirectory;

		/** @var $uploader t3lib_vfs_Service_UploaderService */
		$uploader = t3lib_div::makeInstance('t3lib_vfs_Service_UploaderService');

		if (isset($uploadedFile['name'])) {
			if ($uploadedFile['error']['file']) {
				// TODO handle error
			}

			$tempfileName = $uploadedFile['tmp_name'];
			$origFilename = $uploadedFile['name'];
			$file = $uploader->addUploadedFile($tempfileName, $this->mount, $path, $origFilename, TRUE);
		}

		return $file;
	}

	/**
	 * Track uploads/* files
	 */
//	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, $tce) {
//
//	}
}

?>
