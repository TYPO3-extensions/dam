<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2004 René Fritz (r.fritz@colorcube.de)
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
 * Base class for index rule plugins for the DAM.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class tx_dam_indexRuleBase 
 *   85:     function getTitle()	
 *   94:     function getDescription()	
 *  103:     function getOptionsForm()	
 *  112:     function processOptionsForm()	
 *  121:     function getOptionsInfo()	
 *  130:     function preIndexing()	
 *  138:     function postIndexing()	
 *  144:     function processMeta($meta, $absFile)	
 *  155:     function postProcessMeta($meta, $absFile)	
 *  160:     function getEnabledIcon() 
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Base class for index rule plugins for the DAM
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_indexRuleBase {

	/**
	 * all data from the form
	 * [enabled] is reserved
	 * [shy] is reserved
	 *
	 * This set a index rule not to be shown but always enabled:
	 *
	 *	var $setup = array(
	 *		'enabled' => true,
	 *		'shy' => true,
	 *		);	 
	 */
	var $setup = array();



	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function getTitle()	{
		return 'No title';
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function getDescription()	{
		return '';
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function getOptionsForm()	{
		return '';
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function processOptionsForm()	{
		return '';
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function getOptionsInfo()	{
		return '';
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function preIndexing()	{
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function postIndexing()	{
	}

	
/* 	will be called if exists

	function processMeta($meta, $absFile)	{
		return $meta;
	}

	/ **
	 * [Describe function...]
	 * 
	 * @param	[type]		$meta: ...
	 * @param	[type]		$absFile: ...
	 * @return	[type]		...
	 * /
	function postProcessMeta($meta, $absFile)	{
		return $meta;
	}
*/

	function getEnabledIcon() {
		return '&bull;&nbsp;';
	}

}

// No XCLASS inclusion code: this is a base class
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexrulebase.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexrulebase.php']);
//}

?>