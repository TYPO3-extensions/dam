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
 *   71: class tx_dam_iterator_db
 *  101:     function tx_dam_iterator_db($res, $counter=NULL)
 *  115:     function __construct($res, $counter=NULL)
 *  126:     function __destruct()
 *
 *              SECTION: Iterator functions
 *  145:     function rewind()
 *  155:     function valid()
 *  165:     function next()
 *  177:     function seek($offset)
 *  189:     function key()
 *  199:     function current()
 *  209:     function count ()
 *  220:     function _fetchCurrent()
 *
 * TOTAL FUNCTIONS: 11
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Provides an iterator for a db result
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Iterator
 */
class tx_dam_iterator_db {


	/**
	 * result pointer
	 */
	var $res;

	/**
	 * Used to define the current entry.
	 */
	var $currentPointer = 0;

	/**
	 * total count of rows
	 * Can be set to avoud using sql_num_rows() when amount of rows is already known.
	 */
	 var $countTotal = 0;



	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	integer		$counter Total item count
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_iterator_db($res, $counter=NULL) {
		$this->__construct($res, $counter);
	}


	/**
	 * Initialize the object
	 * PHP5 constructor
	 *
	 * @param	mixed		$res DB result pointer
	 * @param	integer		$counter Total item count
	 * @return	void
	 * @see __construct()
	 */
	function __construct($res, $counter=NULL) {
		$this->res = $res;
		$this->countTotal = $counter;
		$this->_fetchCurrent();
	}

	/**
	 * Destructor
	 *
	 * @return	void
	 */
	function __destruct() {
		$GLOBALS['TYPO3_DB']->sql_free_result($this->res);
	}




	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/


	/**
	 * Set the internal pointer to its first element.
	 *
	 * @return	void
	 */
	function rewind() {
		$this->seek(0);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		return is_array($this->current);
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		$this->_fetchCurrent();
		$this->currentPointer ++;
	}


	/**
	 * Set the internal pointer to the offset
	 *
	 * @param	integer		$offset
	 * @return	void
	 */
	function seek($offset) {
		$this->currentPointer = $offset;
		$GLOBALS['TYPO3_DB']->sql_data_seek($this->res, $offset);
		$this->_fetchCurrent();
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return $this->currentPointer;
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function current() {
		return $this->current;
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return $this->countTotal ? $this->countTotal : $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);
	}



	/**
	 * Fetches the current element
	 *
	 * @return	void
	 */
	function _fetchCurrent() {
		$this->current = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res);
	}
}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_iterator_db.php']);
}
?>