<?php
/***************************************************************
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
 ***************************************************************/

/**
 * TCEform custom field for DAM
 *
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @package dam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Dam_TCEforms_UserField {

	/**
	 * The extension key
	 *
	 * @var string
	 */
	protected $extKey = 'dam';

	/**
	 * The Template Engine
	 *
	 * @var Tx_Fluid_View_StandaloneView
	 */
	protected $view = 'view';

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
	 * Constructor
	 */
	public function __construct() {

			// Load preferences
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

			// Instantiate Template Engine
		$this->view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');

			// Instantiate necessary stuff for FAL
		$this->mountRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_MountRepository');
		$this->mount = $this->mountRepository->findByUid($this->configuration['storage']);
		$this->driver = $this->mount->getDriver();

			// Load StyleSheet in the Page Renderer
		$this->pageRenderer = $GLOBALS['SOBE']->doc->getPageRenderer();
		$cssFile = t3lib_extMgm::extRelPath('dam') . 'Resources/Public/StyleSheets/Dam.css';
		$this->pageRenderer->addCssFile($cssFile);
	}

	/**
	 * This method renders the user-defined thumbnails for DAM purpose
	 *
	 * @param	array			$PA: information related to the field
	 * @param	t3lib_tceforms	$fobj: reference to calling TCEforms object
	 *
	 * @return	string	The HTML for the form field
	 */
	public function renderFile ($PA, t3lib_TCEforms $fobj) {
		
			// Instantiate a Fluid stand-alone view and load the template file
		$filePath = t3lib_extMgm::extPath('dam') . 'Resources/Private/TCEforms/File.html';
		$this->view->setTemplatePathAndFilename($filePath);

		$record = $PA['row'];
		
		if ($record['file'] > 0) {
			$fileRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_FileRepository');
			$file = $fileRepository->findByUid($record['file']);

				// Fetches the absolute file path
			$fileAbsolutePath = $this->driver->getAbsolutePath($file);

				// Generates HTML for Thumbnail generation
			$thumbnail = t3lib_BEfunc::getThumbNail('thumbs.php', $fileAbsolutePath,' align="middle" style="border:solid 1px #ccc;" class="tx-dam-thumbnail" ',160);
		}
		
			// Assignes values for the View
		$fileName = $file ? $file->getName() : '';
		$publicUrl = $file ? $this->driver->getPublicUrl($file) : '';

		$this->view->assign('fileName', $fileName);
		$this->view->assign('publicUrl', $publicUrl);
		$this->view->assign('uploadMaxFilesize', ini_get('upload_max_filesize'));
		$this->view->assign('mimeTypeAllowed', $this->configuration['mime_type_allowed']);
		$this->view->assign('thumbnail', $thumbnail);

		return $this->view->render();
	}

	/**
	 * This method renders the user-defined thumbnails for DAM purpose
	 *
	 * @param	array			$PA: information related to the field
	 * @param	t3lib_tceforms	$fobj: reference to calling TCEforms object
	 *
	 * @return	string	The HTML for the form field
	 */
	public function renderThumbnail($PA, t3lib_TCEforms $fobj) {

		return 'todo';
			// Instantiate a Fluid stand-alone view and load the template file
		$filePath = t3lib_extMgm::extPath('dam') . 'Resources/Private/TCEforms/Thumbnail.html';
		$view = t3lib_div::makeInstance('Tx_Fluid_View_StandaloneView');
		$view->setTemplatePathAndFilename($filePath);

		$view->assign('uploadMaxFilesize', ini_get('upload_max_filesize'));
		$view->assign('mimeTypeAllowed', $this->configuration['mime_type_allowed']);

		return $view->render();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/Resources/Private/PHP/TCEforms/class.tx_dam_tceforms.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/Resources/Private/PHP/TCEforms/class.tx_dam_tceforms.php']);
}

?>