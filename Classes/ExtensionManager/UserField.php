<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@ecodev.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Tx_Dam_ExtensionManager_UserField {

	/**
	 * The extension key
	 * 
	 * @var string
	 */
	protected $extKey = 'dam';

	/**
	 * The Configuration Array
	 * 
	 * @var array
	 */
	protected $configuration = array();
	
	/**
	 * @var t3lib_vfs_Domain_Repository_MountRepository
	 */
	protected $mountRepository;

	/**
	 * Constructor
	 */
	public function __construct() {

			// Load configuration
		if ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]) {
			$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		}
		
			// Merge with Data that comes from the User
		$postData = t3lib_div::_POST();
		if (!empty($postData['data'])) {
			$this->configuration = array_merge($this->configuration, $postData['data']);
		}
		
		/** @var $mount t3lib_vfs_Domain_Model_Mount */
		if ($this->configuration['storage'] > 0) {
			$this->mountRepository = t3lib_div::makeInstance('t3lib_vfs_Domain_Repository_MountRepository');
			$this->mount = $this->mountRepository->findByUid($this->configuration['storage']);
		}
	}
	
	/**
	 * Display a message to the Extension Manager whether the configuration is OK or KO.
	 *
	 * @param array $params
	 * @param object $tsObj t3lib_tsStyleConfig
	 * @return string the HTML message
	 */
	public function renderMessage(&$params, &$tsObj) {
		$out = '';


		if ($this->needsUpdate()) {
			$out .= '
			<div style="">
				<div class="typo3-message message-warning">
					<div class="message-header">'
						. $GLOBALS['LANG']->sL('LLL:EXT:dam/Resources/Private/Language/locallang_dam.xml:updater_header') .
					'</div>
					<div class="message-body">
						' . $GLOBALS['LANG']->sL('LLL:EXT:dam/Resources/Private/Language/locallang_dam.xml:updater_message') . '
					</div>
				</div>
			</div>
			';

		}
		else {
			
			$actionOut = '';
			$actions = array();
			
				// Create default directory for DAM
			if ($this->createDefaultDirectory()) {
				$absoluteBasePath = $this->mount->getDriver()->getAbsoluteBasePath();
				$actions[] = 'Created new default directory within "' . $absoluteBasePath . '"';
			}
			
				// Report to the BE User
			if (!empty($actions)) {
				$actionOut = '<span style="text-decoratoin: underline; font-weight: bold;">Action(s) executed:</span>';
				$actionOut .= '<ul><li>' . implode('<li></li>', $actions) . ' </li></ul>';
			}
			
			$out .= '
			<div style="">
				<div class="typo3-message message-ok">
					<div class="message-header">'
						. $GLOBALS['LANG']->sL('LLL:EXT:dam/Resources/Private/Language/locallang_dam.xml:ok_header') .
					'</div>
					<div class="message-body">
						' . $GLOBALS['LANG']->sL('LLL:EXT:dam/Resources/Private/Language/locallang_dam.xml:ok_message') . '
					</div>
					<div class="message-body">
						' . $actionOut . '
					</div>
				</div>
			</div>
			';
		}

		return $out;
	}
	
	/**
	 * Check whether configuration is available
	 *
	 * @return boolean
	 */
	protected function needsUpdate() {
		return empty($this->configuration);
	}
	
	/**
	 * Check if the directory exists and creates one if not the case.
	 *
	 * @return boolean
	 */
	protected function createDefaultDirectory() {
		
		$result = FALSE;
		
		$directories[] = Tx_Dam_Configuration_Static::$assetDirectory;
		$directories[] = Tx_Dam_Configuration_Static::$thumbnailDirectory;
		$directories[] = Tx_Dam_Configuration_Static::$deletedDirectory;
		
		$absoluteBasePath = $this->mount->getDriver()->getAbsoluteBasePath();
		foreach ($directories as $directory) {
			if (! is_dir($absoluteBasePath . $directory)) {
				try {
					$this->mount->getDriver()->createCollection($directory);
					$result = TRUE;
								}
				catch(Exception $e) {
					throw new Exception('Exception 1316752944: not able to create a directory at ' . $directory, 1316752944);
				}
			}
		}
		
		return $result;
	}
	
	
	/**
	 * Render the storage list Field
	 *
	 * @return string
	 */
	public function renderStorage() {
		
		/* @var t3lib_DB */
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_file_storage', 'deleted = 0');
		
		if (empty($records)) {
			$output = $GLOBALS['LANG']->sL('LLL:EXT:dam/Resources/Private/Language/locallang_dam.xml:em_error_missing_storage');
		}
		else {
			$options = '';
			foreach ($records as $record) {
				$selected = '';
				
				if ($this->configuration['storage'] == $record['uid']) {
					$selected = 'selected="selected"';
				}
				$options .= '<option value="' . $record['uid'] . '" ' . $selected .'>' . $record['name'] . '</option>';
			}

			$output = <<<EOF
				<div class="typo3-tstemplate-ceditor-row" id="userTS-storage">
					<select id="data[storage]" type="text" name="data[storage]">
						$options
					</select>
				</div>
EOF;
		}
		return $output;
	}
}

?>