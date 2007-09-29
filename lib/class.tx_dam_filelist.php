<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skårhøj (kasper@typo3.com)
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
 * Include file extending t3lib_recordList
 *
 * @author	Kasper Skårhøj <kasper@typo3.com>
 * @package TYPO3
 * @subpackage tx_dam
 */


require_once(PATH_t3lib.'class.t3lib_recordlist.php');
require_once($GLOBALS['BACK_PATH'].'class.file_list.inc');

$LANG->includeLLFile('EXT:lang/locallang_mod_file_list.php');

/**
 * Modified filelist class
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class  tx_dam_fileList extends fileList {

	var $folderParam = 'path';


	/**
	 * @param	[type]		$path: ...
	 * @param	[type]		$header: ...
	 * @return	[type]		...
	 */
	function getBrowseableFolderList($path, $header=FALSE)	{
		global $BACK_PATH;


		$pointer = t3lib_div::_GP('pointer');
		$this->start($path,$pointer,'','');

		$this->backPath = $BACK_PATH;
		$this->script = 'index.php';
		$this->clickMenus = 0;
		$this->thumbs = FALSE; //$this->MOD_SETTINGS['displayThumbs']?1:$BE_USER->uc['thumbnailsByDefault'];

		if ($header) {
			$this->writeTop($path, FALSE);
		}
		$this->counter=0; // should not be needed. Bug somewhere
		$this->generateList();
		$this->writeBottom();

		return str_replace('?id=', '?'.$this->folderParam.'=', $this->HTMLcode);
	}


	/********************************
	 *
	 * Overwrites methods from filelist
	 *
	 ********************************/

	var $widthGif = '<img src="clear.gif" width="1" height="1" hspace="130" alt="" />';

	/**
	 * Make the top of the list
	 * 
	 * @param	string		The path to list.
	 * @param	[type]		$pathOnly: ...
	 * @return	void		
	 */
	function writeTop($path, $pathOnly=FALSE)	{
			// Makes the code for the foldericon in the top
		$path = $GLOBALS['SOBE']->basicFF->is_directory($path);	// Cleaning name...

		if ($path)	{
			$out='';
			$this->counter++;
			$theFile = $GLOBALS['SOBE']->basicFF->getTotalFileInfo($path);
			$root = $GLOBALS['SOBE']->basicFF->checkPathAgainstMounts($theFile['path']);
			$titleCol='path';
			$this->fieldArray = Array($titleCol,'up');

			list($title,$icon,$path) =  $this->dirData($theFile);

				// Start compiling the HTML
			$theData = Array();
			$theData[$titleCol] = $this->widthGif;

			$title = $GLOBALS['SOBE']->basicFF->blindPath($path);
			/*
			$theData['up']='<a href="'.htmlspecialchars($this->listURL()).'">'.
					'<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/refresh_n.gif','width="14" height="14"').' title="'.htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.reload',1)).'" alt="" />'.
					'</a>';
			*/

			if ($root)	{
					// The icon with link
				$theIcon = '<img'.t3lib_iconWorks::skinImg($this->backPath,$icon,'width="18" height="16"').' title="'.htmlspecialchars($theFile['file']).'" alt="" />';
				if ($this->clickMenus) $theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($theIcon,$path);

				$theData[$titleCol].='<br />'.t3lib_div::fixed_lgd_cs($title,-($this->fixedL+20));	// No HTML specialchars here - HTML like <b> </b> is allowed
				if(!$pathOnly) {
					$theData['up'].=$this->linkWrapDir('<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/i/folder_up.gif','width="18" height="16"').' title="'.htmlspecialchars($GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.upOneLevel',1)).'" alt="" />',$theFile['path']);
				}
			} else {
					// root:0
				$theIcon='<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/i/_icon_ftp.gif','width="18" height="16"').' alt="" />';
				$theData[$titleCol].='<br />'.htmlspecialchars(t3lib_div::fixed_lgd_cs($title,$this->fixedL+20));
			}

				// Adding top element
			$out.=$this->addelement(1,'',$theData,'',$this->leftMargin,$theIcon);

			$this->HTMLcode.='

		<!--
			Page header for file list
		-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-filelist-top">
					'.$out.'
				</table>';
		}
	}


	/**
	 * Wraps the directory-titles ($code) in a link to file_list.php (id=$path) and sorting commands...
	 * 
	 * @param	string		String to be wrapped
	 * @param	string		ID (path)
	 * @param	string		Sorting column
	 * @return	string		HTML
	 */
	function linkWrapSort($code,$path,$col)	{
		return $code;
	}


	/**
	 * Returns an array with file/dir items + an array with the sorted items
	 *
	 * @param	string		Path (absolute) to read
	 * @param	string		$type is the technical type; file,dir,link. empty is all kinds of stuff.
	 * @param	string		$extList: List of fileextensions to select. If empty, all are selected.
	 * @return	array		Array('files'=>array(), 'sorting'=>array());
	 */
	function readDirectory($path,$type,$extList='')	{
		$items = Array('files'=>array(), 'sorting'=>array());
		$path = $GLOBALS['SOBE']->basicFF->is_directory($path);	// Cleaning name...

		if($path && $GLOBALS['SOBE']->basicFF->checkPathAgainstMounts($path.'/'))	{
			$d = @dir($path);
			$tempArray=Array();
			if (is_object($d))	{
				while($entry=$d->read()) {


#TODO this limit is needed sometimes
					if (count($tempArray)>=10000) break;



					if ($entry!='.' && $entry!='..')	{
						$wholePath = $path.'/'.$entry;		// Because of odd PHP-error where  <br />-tag is sometimes placed after a filename!!
						if (@file_exists($wholePath) && (!$type || t3lib_div::inList($type,filetype($wholePath))))	{
							if ($extList)	{
								$fI = t3lib_div::split_fileref($entry);
								if (t3lib_div::inList($extList,$fI['fileext']))	{
									$tempArray[] = $wholePath;
								}
							} else {
								$tempArray[] = $wholePath;
							}
						}
					}
				}
				$d->close();
			}
				// Get fileinfo
			reset($tempArray);
			while (list(,$val)=each($tempArray))	{
				$temp = $GLOBALS['SOBE']->basicFF->getTotalFileInfo($val);
				$items['files'][] = $temp;
				if ($this->sort)	{
					$items['sorting'][] = strtoupper($temp[$this->sort]);
				} else {
					$items['sorting'][] = '';
				}
			}
				// Sort if required
			if ($this->sort)	{
				if (!$this->sortRev)	{
					asort($items['sorting']);
				} else {
					arsort($items['sorting']);
				}
			}
		}
		return $items;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filelist.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_filelist.php']);
}


?>