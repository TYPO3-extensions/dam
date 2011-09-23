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
class Tx_Dam_ExtensionManager_Configurator {

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
	 * Constructor
	 */
	public function __construct() {

			// Load preferences
		if ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]) {
			$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
			
		}
		
			// Merge with Data that comes from the User
		$postData = t3lib_div::_POST();
		if (!empty($postData['data'])) {
			$this->configuration = array_merge($this->configuration, $postData['data']);
		}
	}
	
	/**
	 * Display a message to the Extension Manager whether the database needs to be updated or not.
	 *
	 * @return string the HTML message
	 */
	public function displayMessage(&$params, &$tsObj) {
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
			if ($this->createDirectory()) {
				$actions[] = 'Created new directory within "' . PATH_site . $this->configuration['dam_path'] . '"';
			}
			
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
	protected function createDirectory($statements) {
		
		$result = FALSE;
		
		$directories[] = PATH_site . $this->configuration['dam_path'];
		$directories[] = $directories[0] . '/Assets';
		$directories[] = $directories[0] . '/Thumbnails';
		$directories[] = $directories[0] . '/Deleted';
		
		foreach ($directories as $directory) {
			if (! is_dir($directory)) {

				try {
					mkdir($directory);
					$result = TRUE;
				}
				catch(Exception $e) {
					throw new Exception('Exception 1316752944: not able to create a directory at ' . $directory, 1316752944);
				}
			}
		}
		
		return $result;
	}
}

?>