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
 * Command module 'new folder'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   64: class tx_dam_cmd_foldernew extends t3lib_extobjbase
 *   75:     function head()
 *  118:     function main()
 *  172:     function folderForm()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'lib/class.tx_dam_guifunc.php');


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');



/**
 * Class for the file rename command
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
class tx_dam_cmd_foldernew extends t3lib_extobjbase {


	var $folderNumber=10;


	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global  $LANG, $BACK_PATH;


			// Initialize GPvars:
		$this->number = t3lib_div::_GP('number');
		$this->target = t3lib_div::_GP('target');

			// Init basic-file-functions object:
		$this->basicff = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->basicff->init($GLOBALS['FILEMOUNTS'],$GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);

			// Cleaning and checking target
		$this->target = tx_dam::path_makeAbsolute($this->target);
		$this->target = $this->basicff->is_directory($this->target);
		$key = $this->basicff->checkPathAgainstMounts($this->target.'/');
		if (!$this->target || !$key)	{
			$this->target = false;
		}

			// Finding the icon
		switch($GLOBALS['FILEMOUNTS'][$key]['type'])	{
			case 'user':	$this->icon = 'gfx/i/_icon_ftp_user.gif';	break;
			case 'group':	$this->icon = 'gfx/i/_icon_ftp_group.gif';	break;
			default:		$this->icon = 'gfx/i/_icon_ftp.gif';	break;
		}

			// Relative path to filemount, $key:
		$this->shortPath = substr($this->target,strlen($GLOBALS['FILEMOUNTS'][$key]['path']));

			// Setting title:
		$this->title = $GLOBALS['FILEMOUNTS'][$key]['name'].': '.$this->shortPath;

#		$GLOBALS['SOBE']->pageTitle = 'Create new folder';
		$GLOBALS['SOBE']->pageTitle = $LANG->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.pagetitle');

	}

	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global  $LANG;

			// Make page header:
		$content='';

		if ($this->target) {

			$pathInfo = tx_dam::path_compileInfo($this->target);
//			$error = '';
//
//				// make the new folder:
//			$error = $this->makeFolder();
//
//			if(!$error) {
//				$this->pObj->redirect();
//			}
//
//				// output error message
//			if($error) {
//				$content.= $GLOBALS['SOBE']->doc->section('Error',htmlspecialchars($error),0,1,2);
//				$content.= $GLOBALS['SOBE']->doc->spacer(15);
//			}


			$content.= tx_dam_guiFunc::getFolderInfoBar($pathInfo);

			$content.= $GLOBALS['SOBE']->doc->spacer(10);


				// Making the formfields for renaming:
			$code = $this->folderForm();
				// Add the HTML as a section:
			$content.= $GLOBALS['SOBE']->doc->section('',$code);

		} else {

			$content.= $this->pObj->wrongCommandMessage();
		}


		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);

			// CSH:
#		$code.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_rename', $GLOBALS['BACK_PATH'],'<br/>');

		return $content;
	}

	/**
	 * Making the formfields for renaming
	 *
	 * @return	string		HTML content
	 */
	function folderForm()	{
		global  $TCA, $BACK_PATH, $LANG, $FILEMOUNTS;


		$content='';

		$GLOBALS['SOBE']->doc->JScode=$GLOBALS['SOBE']->doc->wrapScriptTags('
			var path = "'.$this->target.'";

			function reload(a)	{	//
				if (!changed || (changed && confirm('.$LANG->JScharCode($LANG->sL('LLL:EXT:lang/locallang_core.xml:mess.redraw')).')))	{
					var params = "&target="+escape(path)+"&number="+a;
					document.location = "index.php?CMD=tx_dam_cmd_foldernew&redirect='.htmlspecialchars($this->pObj->redirect).'&"+params;
				}
			}

			var changed = 0;
		');


		$code.='</form><form action="'.$BACK_PATH.'tce_file.php" method="post" name="editform">';


			// Making the selector box for the number of concurrent folder-creations
		$this->number = t3lib_div::intInRange($this->number,1,10);
		$code.='
			<div id="c-select">
				<select name="number" onchange="reload(this.options[this.selectedIndex].value);">';
		for ($a=1;$a<=$this->folderNumber;$a++)	{
			$code.='
					<option value="'.$a.'"'.($this->number==$a?' selected="selected"':'').'>'.$a.' '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.folders',1).'</option>';
		}
		$code.='
				</select>
			</div>
			';

			// Making the number of new-folder boxes needed:
		$code.='
			<div id="c-createFolders">
		';
		for ($a=0;$a<$this->number;$a++)	{
			$code.='
					<input'.$GLOBALS['SOBE']->doc->formWidth(20).' type="text" name="file[newfolder]['.$a.'][data]" onchange="changed=true;" />
					<input type="hidden" name="file[newfolder]['.$a.'][target]" value="'.htmlspecialchars($this->target).'" /><br />
				';
		}
		$code.='
			</div>
		';

		$code.= '<br />';

			// Making submit button for folder creation:
		$code.='
			<div id="c-submitFolders">
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_newfolder.php.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />
				<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->redirect).'" />
			</div>
			';

			// CSH:
#		$code.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'file_newfolder', $GLOBALS['BACK_PATH'],'<br/>');

		$content.= $GLOBALS['SOBE']->doc->section('',$code);



			// Add spacer:
		$content.= $GLOBALS['SOBE']->doc->spacer(10);

			// Switching form tags:
		$content.= $GLOBALS['SOBE']->doc->sectionEnd();


		return $content;

	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_foldernew.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_foldernew.php']);
}


?>