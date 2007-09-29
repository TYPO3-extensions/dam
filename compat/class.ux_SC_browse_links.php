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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 * @ignore
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   57: class ux_SC_browse_links extends SC_browse_links
 *   64:     function main()
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'compat/class.browse_links.php');

/**
 * Provide hook to make it possible to inserts the DAM in the element browser.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3
 * @subpackage tx_dam
 * @ignore
 */
class ux_SC_browse_links extends SC_browse_links {

	/**
	 * Main function, detecting the current mode of the element browser and branching out to internal methods.
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER;

		$modData = $BE_USER->getModuleData('browse_links.php','ses');

			// render type by user func
		$browserRendered = false;
		if (is_array ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'] as $classRef) {
				$browserRenderObj = t3lib_div::getUserObj($classRef);
				if(is_object($browserRenderObj) && method_exists($browserRenderObj, 'isValid') && method_exists($browserRenderObj, 'render'))	{
					if ($browserRenderObj->isValid($this->mode, $this)) {
						$this->content .=  $browserRenderObj->render($this->mode, $this);
						$browserRendered = true;
						break;
					}
				}
			}
		}

			// if type was not rendered use default rendering functions
		if(!$browserRendered) {

				// Output the correct content according to $this->mode
			switch((string)$this->mode)	{
				case 'rte':
					$this->content=$this->main_rte();
				break;
				case 'db':
					if (isset($this->expandPage))	{
						$modData['expandPage']=$this->expandPage;
						$BE_USER->pushModuleData('browse_links.php',$modData);
					} else {
						$this->expandPage=$modData['expandPage'];
					}

					$this->content=$this->main_db();
				break;
				case 'file':
				case 'filedrag':
					if (isset($this->expandFolder))	{
						$modData['expandFolder']=$this->expandFolder;
						$BE_USER->pushModuleData('browse_links.php',$modData);
					} else {
						$this->expandFolder=$modData['expandFolder'];
					}

					$this->content=$this->main_file();
				break;
				case 'wizard':
					$this->content=$this->main_rte(1);
				break;
			}
		}
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_SC_browse_links.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_SC_browse_links.php']);
}

 ?>