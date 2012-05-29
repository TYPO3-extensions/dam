<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Lorenz Ulrich (lorenz.ulrich@phz.ch)
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
 * Contains an implementation of the mediaWizardProvider supporting DAM references
 *
 * @author	Lorenz Ulrich (lorenz.ulrich@phz.ch)
 */


class tx_dam_mediawizarddamprovider implements tslib_mediaWizardProvider {

	/**
	 * @var array List of providers we can handle in this class
	 */
	protected $providers = array(
		'dam'
	);

	/**
	 * Checks if we have a reference to a media item
	 *
	 * This is done by checking that die passed location starts with "media:"
	 *
	 * @param  $url
	 * @return string
	 */
	protected function getMethod($url) {

		if (strpos($url, 'media:') === 0) {
			return 'process_dam';
		} else {
			return NULL;
		}

	}

	/***********************************************
	 *
	 * Implementation of tslib_mediaWizardProvider
	 *
	 ***********************************************/

	/**
	 * @param  $url
	 * @return bool
	 * @see tslib_mediaWizardProvider::canHandle
	 */
	public function canHandle($url) {
		return ($this->getMethod($url) !== NULL);
	}

	/**
	 * @param  $url	URL to rewrite
	 * @return string The rewritten URL
	 * @see tslib_mediaWizardProvider::rewriteUrl
	 */
	public function rewriteUrl($url) {
		$method = $this->getMethod($url);
		return call_user_func(array($this, $method), $url);
	}

	/***********************************************
	 *
	 * Providers URL rewriting:
	 *
	 ***********************************************/

	/**
	 * Get a relative path from a DAM item
	 *
	 * @param string $url
	 * @return string processed url
	 */
	protected function process_dam($url) {

		$damRecordUid = (int)substr($url, 6);
		$meta = tx_dam::meta_getDataByUid($damRecordUid, 'file_path,file_name');
			// Return a full URL to avoid prefixing issues in the TYPO3 Core
		$url = t3lib_div::locationHeaderUrl($meta['file_path'] . $meta['file_name']);

		return $url;

	}

}

if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_mediawizarddamprovider.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/dam/binding/mediatag/class.tx_dam_mediawizarddamprovider.php']);
}

?>