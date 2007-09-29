<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Module extension (addition to function menu) 'thumbs' for the 'Media>List' module.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_dam_list_thumbs extends t3lib_extobjbase
 *   69:     function modMenu()
 *   85:     function head()
 *  117:     function main()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */

require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Module extension  'Media>List>Thumbnail'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage list
 */
class tx_dam_list_thumbs extends t3lib_extobjbase {

	var $diaSize = 115;
	var $diaMargin = 10;

	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_list_thumbs_bigThumb' => '',
			'tx_dam_list_thumbs_showTitle' => '',
			'tx_dam_list_thumbs_showInfo' => '',
			'tx_dam_list_thumbs_showIcons' => '',
		);
	}

	/**
	 * Do some init things and set some styles in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global $LANG;

		//
		// Init gui items and ...
		//

		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'header');

#		$this->pObj->guiItems->registerFunc('getResultBrowser', 'footer');
		$this->pObj->guiItems->registerFunc('getSearchBox', 'footer');
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');
		$this->pObj->guiItems->registerFunc('getStoreControl', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_bigThumb', $LANG->getLL('tx_dam_list_thumbs.bigThumb'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showTitle', $LANG->getLL('tx_dam_list_thumbs.showTitle'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showInfo', $LANG->getLL('tx_dam_list_thumbs.showInfo'));
		$this->pObj->addOption('funcCheck', 'tx_dam_list_thumbs_showIcons', $LANG->getLL('tx_dam_list_thumbs.showIcons'));

		if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_bigThumb']) {
			$this->diaSize = 200;
		}

	}

	/**
	 * Main function
	 *
	 * @return	string		HTML output
	 */
	function main()    {
		global $BE_USER,$LANG,$BACK_PATH;

		$content = '';

		//
		// Use the current selection to create a query and count selected records
		//

		$this->pObj->selection->addSelectionToQuery();
		$this->pObj->selection->execSelectionQuery(TRUE);


		//
		// output header: info bar, result browser, ....
		//

		$content.= $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);


		//
		// creates thumbnail list
		//

			// any records found?
		if($this->pObj->selection->pointer->countTotal) {

				// limit query for browsing
			$this->pObj->selection->addLimitToQuery();
			$this->pObj->selection->execSelectionQuery();

			$showElements = array();
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showTitle']) {
				$showElements[] = 'title';
			}
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showInfo']) {
				$showElements[] = 'info';
			}
			if ($this->pObj->MOD_SETTINGS['tx_dam_list_thumbs_showIcons']) {
				$showElements[] = 'icons';
			}

				// extra CSS code for HTML header
			$this->pObj->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_guiFunc::getDiaStyles($this->diaSize, $this->diaMargin, 5);

			$code = '';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->pObj->selection->res)) {
				$onClick = $this->pObj->doc->wrapClickMenuOnIcon('', 'tx_dam', $row['uid'], $listFr=1,$addParams='',$enDisItems='', $returnOnClick=TRUE);
				$code.= tx_dam_guiFunc::getDia($row, $this->diaSize, $this->diaMargin, $showElements, $onClick);
			}

			$content.= $this->pObj->doc->spacer(5);
			$content.= $this->pObj->doc->section('','<div style="line-height:'.($this->diaSize +7+8).'px;">'.$code.'</div><br style="clear:left" />',0,1);

		} else {
				// no search result: showing selection box
			$content.= $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		}

		return $content;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_thumbs/class.tx_dam_list_thumbs.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_thumbs/class.tx_dam_list_thumbs.php']);
}

?>