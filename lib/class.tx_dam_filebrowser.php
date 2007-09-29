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
 * DAM file listing class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   62: class  tx_dam_filebrowser extends tx_dam_listfiles
 *   75:     function getBrowseableFolderList($pathInfo, $renderNavHeader=true)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once (PATH_txdam.'lib/class.tx_dam_iterator_dir.php');
require_once(PATH_txdam.'lib/class.tx_dam_listfiles.php');
require_once(PATH_txdam.'lib/class.tx_dam_listpointer.php');


/**
 * Simple file browser
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class  tx_dam_filebrowser extends tx_dam_listfiles {




	/**
	 * Creates a file/folder browser.
	 * The list does not include any actions (delete,rename,...) but can be used to show the files of a folder and select a folder as a starting point for some processing like indexing.
	 *
	 * @param	array		$pathInfo Path info array from tx_dam::path_compileInfo()
	 * @param	boolean		$renderNavHeader If set a header with the path will be rendered
	 * @return	string		HTML output
	 */
	function getBrowseableFolderList($pathInfo, $renderNavHeader=true)	{
		$content = '';

		$pathInfo = is_array($pathInfo) ? $pathInfo : tx_dam::path_compileInfo($pathInfo);


		//
		// fetches files and folder
		//

		$dirListFolder = t3lib_div::makeInstance('tx_dam_iterator_dir');
		$dirListFolder->read($pathInfo['dir_path_absolute'], 'dir,link');


		//
		// folder listing
		//

		$dirListFiles = t3lib_div::makeInstance('tx_dam_iterator_dir');
		$dirListFiles->read($pathInfo['dir_path_absolute'], 'file');


		//
		// initializes the pointer object for lists
		//

		$this->pointer = t3lib_div::makeInstance('tx_dam_listPointer');
		$this->pointer->init(0, 100);
		$this->pointer->setTotalCount($dirListFolder->count()+$dirListFiles->count());

		//
		// Create filelisting
		//

		$this->removeColumn('_CONTROL_');
		$this->clickMenus = false;
		$this->clipBoard = false;

			// disable sorting links
		$this->enableSorting = false;

			// Enable/disable display of thumbnails
		$this->showThumbs = false;
			// Enable/disable display poups
		$this->enableFilePopup = false;
			// Enable/disable display of long titles
		$this->showfullTitle = false;
			// Enable/disable display of AlternateBgColors
		#$this->showAlternateBgColors = true;
// TODO do not exist: $this->pObj->modTSconfig
		$this->showAlternateBgColors = $this->pObj->modTSconfig['properties']['alternateBgColors']?1:0;
			// Enable/disable display of unix like permission string
		$this->showUnixPerms = false;
			// Display file sizes in bytes or formatted
		$this->showDetailedSize = false;


		$this->setPathInfo($pathInfo);
		$this->addData($dirListFolder, 'dir');
		$this->addData($dirListFiles, 'files');
		$this->setCurrentSorting($this->SOBE->MOD_SETTINGS['tx_dam_file_list_sortField'], $this->SOBE->MOD_SETTINGS['tx_dam_file_list_sortRev']);
		$this->setParameterNames('SET[tx_dam_file_list_sortField]', 'SET[tx_dam_file_list_sortRev]');
		$this->setPointer($this->pointer);


		if ($renderNavHeader AND is_object($this->SOBE)) {
			$content.= '<div class="typo3-foldernavbar">'.$this->SOBE->getFolderNavBar($pathInfo).'</div>';
		}
		$content.= $this->getListTable();

		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filebrowser.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filebrowser.php']);
}


?>