<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2005 René Fritz (r.fritz@colorcube.de)
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
 * DAM nav frame.
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
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



unset($MCONF);
include ('conf.php');
include ($BACK_PATH.'init.php');
include ($BACK_PATH.'template.php');



define('PATH_txdam', t3lib_extMgm::extPath('dam'));

require_once(PATH_txdam.'lib/class.tx_dam_navframe.php');


/**
 * Main script class for the tree navigation frame
 * 
 * @author	@author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_mainnavframe extends tx_dam_navframe {


		// Constructor:
	function init()	{
		global $MCONF;

		list($this->mainModule) = explode('_', $MCONF['name']);

		parent::init();
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_tools/tx_dam_navframe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_tools/tx_dam_navframe.php']);
}




// Make instance:

$SOBE = t3lib_div::makeInstance('tx_dam_mainnavframe');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();


?>