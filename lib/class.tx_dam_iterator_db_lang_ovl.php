<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   67: class tx_dam_iterator_db_lang_ovl extends tx_dam_iterator_db
 *   98:     function tx_dam_iterator_db_lang_ovl($res, $counter=NULL)
 *  110:     function initLanguageOverlay($table, $langUid)
 *
 *              SECTION: language overlay functions
 *  134:     function _fetchCurrent()
 *  146:     function _makeLanguageOverlay()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');


/**
 * Provides an iterator for a db result
 *
 * This version fetches language overlay records if possible.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_db_lang_ovl extends tx_dam_iterator_db {


	/**
	 * uid of the wanted language
	 */
	var $langUid;

	/**
	 * Field of the result rows that holds the language uid.
	 */
	var $languageField = 'sys_language_uid';

	/**
	 * Field of the result rows that holds the language uid.
	 */
	var $transOrigPointerField = 'sys_language_uid';

	/**
	 * the table name
	 */
	var $table = '';


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_db_lang_ovl($res, $counter=NULL) {
		$this->__construct($res, $counter);
	}


	/**
	 * Constructor
	 *
	 * @param	string		Table name
	 * @param	integer		uid of the wanted language
	 * @return	void
	 */
	function initLanguageOverlay($table, $langUid) {
		global $TCA;

		$this->langUid = $langUid;

		$this->table = $table;
		$this->languageField = $TCA[$table]['ctrl']['languageField'];
		$this->transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

		$this->_makeLanguageOverlay();
	}

	/***************************************
	 *
	 *	 language overlay functions
	 *
	 ***************************************/


	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _fetchCurrent() {
		$this->current = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res);
		if ($this->table AND $this->current) {
			$this->_makeLanguageOverlay();
		}
	}

	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _makeLanguageOverlay() {
		if ($this->current[$this->languageField]!=$this->langUid) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', array_keys($this->current)), $this->table, $this->transOrigPointerField.'='.$this->current['uid']);
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$this->current = $row;
			}
		}
	}
}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db_lang_ovl.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db_lang_ovl.php']);
}
?>