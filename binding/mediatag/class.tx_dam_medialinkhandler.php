<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Lorenz Ulrich (lorenz.ulrich@visol.ch)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 */

/**
 * Media linkhandler enables linking to DAM items, f.e. from header_link or image_link
 *
 * @author	Lorenz Ulrich <lorenz.ulrich@visol.ch>
 * @package TYPO3
 * @subpackage tx_dam
 * @access public
 */

require_once(t3lib_extMgm::extPath('dam') . 'binding/mediatag/class.tx_dam_tsfemediatag.php');

class tx_dam_medialinkhandler {

	/**
	 * Process the link generation
	 *
	 * @param string $linktxt
	 * @param array $conf
	 * @param string $linkHandlerKeyword Define the identifier that an record is given
	 * @param string $linkHandlerValue uid of the requested DAM record
	 * @param string $linkParams Full link params like "media:77"
	 * @param tslib_cObj $pObj
	 * @return string
	 */
	function main($linktxt, $conf, $linkHandlerKeyword, $linkHandlerValue, $linkParams, &$pObj) {
		$this->pObj        = &$pObj;
		$generatedLink     = '';
		$aTagParams = str_replace('media:' . $linkHandlerValue, '', $linkParams); // extract link params like "target", "class" or "title"

			// get the record from $linkhandlerValue
		$recordRow = $GLOBALS['TSFE']->sys_page->checkRecord('tx_dam', $linkHandlerValue);

			// build the typolink when the requested record and the necessary configuration are available
		if (is_array($recordRow) && !empty($recordRow)) // record available
		{
			unset($conf['parameter']);
			unset($conf['parameter.']);
			$conf['parameter'] = $linkHandlerValue . $aTagParams;
			$mediaTag = t3lib_div::makeInstance('tx_dam_tsfemediatag');
			$typolinkHref = $mediaTag->typoLink($linktxt, $conf, $pObj);
			$generatedLink = $typolinkHref;
		} else {
			$generatedLink = $linktxt;
		}

		return $generatedLink;
	}
}

?>
