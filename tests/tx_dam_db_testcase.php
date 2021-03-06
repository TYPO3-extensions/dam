<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * @author	Rene Fritz <r.fritz@colorcube.de>
 */
 
require_once (PATH_txdam.'tests/class.tx_dam_testlib.php');

class tx_dam_db_testcase extends tx_dam_testlib {




	/**
	 * tx_dam_db::getPid()
	 */
	public function test_getPID () {

		$pid = tx_dam_db::getPid();
		self::assertTrue ($pid>0, 'No DAM sysfolder detected/created');

		$pid = tx_dam_db::getPidList();
		self::assertTrue (!($pid=='0'), 'No DAM sysfolder detected/created');

	}



	/**
	 * tx_dam_db::getMetaForUploads()
	 */
	public function test_getMetaForUploads () {

//		$files = t3lib_div::getFilesInDir(PATH_site.'uploads/pics/',$extList='',1,1);
//		$rows = tx_dam_db::getMetaForUploads($files);
		#self::assertTrue ($pid>0, 'No DAM sysfolder detected/created');


	}

}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_db_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_db_testcase.php']);
//}
?>