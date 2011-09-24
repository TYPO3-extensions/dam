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
	 * Constructor
	 */
	public function __construct() {

			// Load preferences
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
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