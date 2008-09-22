<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2008 Rene Fritz (r.fritz@colorcube.de)
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
 * Module extension (addition to function menu) 'References' for the 'Media>Info' module.
 * Part of the DAM (digital asset management) extension.
 * 
 * This module lists all references to a file in media>info
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @author	David Steeb <david@b13.de>
 * @package TYPO3
 * @subpackage tx_dam
 */

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Module 'Media>Info>References'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @author	David Steeb <david@b13.de>
 */
class tx_dam_info_reference extends t3lib_extobjbase {

	
	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu() {
		return array(
			'tx_dam_info_reference_showRootline' => 1,
		);
	}


	/**
	 * Do some init things and aet some styles in HTML header
	 *
	 * @return	void
	 */
	function head() {
			// Init gui items
		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems->registerFunc('getOptions', 'footer');

			// add some options
		$this->pObj->addOption('funcCheck', 'tx_dam_info_reference_showRootline', $GLOBALS['LANG']->getLL('showRootline'));

	}

	
	/**
	 * Main function to render the reference for DAM records
	 *
	 * @return	string		HTML output
	 */
	function main() {
			// Use the current selection to create a query and count selected records
		$this->pObj->selection->addSelectionToQuery();

		$this->pObj->selection->qg->queryAddMM($mm_table='tx_dam_mm_ref', $foreign_table='', $local_table='tx_dam');
		$this->pObj->selection->execSelectionQuery(TRUE);


			// output header: info bar, result browser
		$content = $this->pObj->guiItems->getOutput('header');
		$content.= $this->pObj->doc->spacer(10);


			// any records found?
		if ($this->pObj->selection->pointer->countTotal) {
			$this->pObj->selection->qg->query['FROM']['tx_dam'] = tx_dam_db::getMetaInfoFieldList();
			$this->pObj->selection->qg->query['FROM']['tx_dam_mm_ref'] = 'tx_dam_mm_ref.uid_foreign,tx_dam_mm_ref.tablenames,tx_dam_mm_ref.ident';
			$this->pObj->selection->qg->query['ORDERBY']['tx_dam_mm_ref'] = 'tablenames';
			$this->pObj->selection->addLimitToQuery();
			$this->pObj->res = $this->pObj->selection->execSelectionQuery();

			$content .= $this->pObj->doc->section('',$this->getReferencesTable(),0,1);
		} else {
				// no search result: showing selection box
			$content .= $this->pObj->doc->section('',$this->pObj->getCurrentSelectionBox(),0,1);
		}

		return $content;
	}


	/**
	 * Render the table with referenced records
	 *
	 * @return	string		Rendered Table
	 * @todo see tx_dam_cmd_filedelete for duplicate
	 */
	function getReferencesTable() {

			// init table layout
		$refTableLayout = array(
			'table' => array('<table cellpadding="2" cellspacing="1" border="0" width="100%">','</table>'),
			'0' => array(
				'defCol' => array('<th nowrap="nowrap" class="bgColor5">','</th>')
			),
			'defRow' => array(
				'defCol' => array('<td nowrap="nowrap" class="bgColor4">','</td>'),
			),
		);

		$cTable = array();
		$tr = 0;
		$td = 0;

		$cTable[$tr][$td++] = $GLOBALS['LANG']->getLL('page', 1).':';
		$cTable[$tr][$td++] = $GLOBALS['LANG']->getLL('content_element', 1).':';
		$cTable[$tr][$td++] = $GLOBALS['LANG']->getLL('content_age', 1).':';
		$cTable[$tr][$td++] = $GLOBALS['LANG']->getLL('media_element', 1).':';
		$cTable[$tr][$td++] = $GLOBALS['LANG']->getLL('media_element_age', 1).':';
		$tr++;

		while ($damRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->pObj->selection->res)) {

			$refTable = $damRow['tablenames'];
			if (!$refTable) continue;


				// get main fields from TCA
			$selectFields = tx_dam_db::getTCAFieldListArray($refTable, TRUE);
			$selectFields = tx_dam_db::compileFieldList($refTable, $selectFields, FALSE);
			$selectFields = ($selectFields ? $selectFields : ($refTable.'.uid,'.$refTable.'.pid'));

				// Query for non-deleted tables only
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$selectFields,
					$refTable,
					$refTable.'.uid='.$damRow['uid_foreign'].
						t3lib_BEfunc::BEenableFields($refTable).t3lib_BEfunc::deleteClause($refTable),
					'',
					'tstamp DESC',
					40
				);

			while ($refRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

				$pageRow = t3lib_BEfunc::getRecord('pages', $refRow['pid']);
				if (is_array($pageRow)) {

						// Create output item for pages record
					$contentPageLink = tx_dam_SCbase::getRecordInfoEditLink('pages', $pageRow, $this->pObj->MOD_SETTINGS['tx_dam_info_reference_showRootline']);

						// Create output item for reference record
					$contentElementLink = tx_dam_SCbase::getRecordInfoEditLink($refTable, $refRow);

						// Create output text describing the age
					$contentAge = t3lib_BEfunc::dateTimeAge($refRow['tstamp'], 1);

						// Create output item for tx_dam record
					$damElementLink = tx_dam_SCbase::getRecordInfoEditLink('tx_dam', $damRow);

						// Create output text describing the tx_dam record age
					$damElementAge = t3lib_BEfunc::dateTimeAge($damRow['tstamp'], 1);

						// Add row to table
					$td=0;
					$cTable[$tr][$td++] = $contentPageLink;
					$cTable[$tr][$td++] = $contentElementLink;
					$cTable[$tr][$td++] = $contentAge;
					$cTable[$tr][$td++] = $damElementLink;
					$cTable[$tr][$td++] = $damElementAge;
					$tr++;
				}
			}
		}

			// Return rendered table
		if (count($cTable) > 1) {
			return $this->pObj->doc->table($cTable, $refTableLayout);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_info_reference/class.tx_dam_info_reference.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_info_reference/class.tx_dam_info_reference.php']);
}

?>