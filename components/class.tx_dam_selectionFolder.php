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
 * Contains standard selection trees/rules.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   80: class tx_dam_selectionFolder extends t3lib_folderTree
 *  121:     function tx_dam_selectionFolder()
 *  137:     function getId ($row)
 *  149:     function getJumpToParam($row, $command='SELECT')
 *  163:     function PM_ATagWrap($icon,$cmd,$bMark='')
 *  189:     function wrapTitle($title,$row,$bank=0)
 *  212:     function getControl($title,$row)
 *  240:     function printTree($treeArr='')
 *  291:     function setMounts($mountpoints)
 *  304:     function getTreeTitle()
 *  313:     function getDefaultIcon()
 *  323:     function getTreeName()
 *
 *              SECTION: DAM specific functions
 *  342:     function selection_getItemTitle($id)
 *  360:     function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)
 *
 *              SECTION: element browser specific functions
 *  388:     function eb_wrapTitle($title,$row)
 *  403:     function eb_PM_ATagWrap($icon,$cmd,$bMark='')
 *  419:     function eb_printTree($treeArr='')
 *  481:     function ext_isLinkable($v)
 *
 * TOTAL FUNCTIONS: 17
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_t3lib.'class.t3lib_foldertree.php');



/**
 * folder tree class
 *
 * This is customized to behave like a selection class.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Component
 * @subpackage  Selection
 */
class tx_dam_selectionFolder extends t3lib_folderTree  {

	/**
	 * Definies if this class can render a browse tree
	 */
	var $isTreeViewClass = true;

	/**
	 * Defines if a browsetree for TCEForms can be rendered
	 */
	var $isTCEFormsSelectClass = false;

	/**
	 * If mounts are supported (be_users)
	 */
	var $supportMounts = false;	// done automatically

	/**
	 * element browser mode
	 */
	var $mode = 'browse';

	/**
	 * enables selection icons: + = -
	 */
	var $modeSelIcons = true;

	/**
	 * Defines the deselect magic value
	 */
	var $deselectValue = 0;





	/**
	 * constructor
	 *
	 * @return	void
	 */
	function tx_dam_selectionFolder()	{

		$this->title='Folder tree';
		$this->treeName='txdamFolder';
		$this->domIdPrefix=$this->treeName;
		$this->MOUNTS = $GLOBALS['FILEMOUNTS'];
		$this->iconPath = 'gfx/i/';
		$this->iconName = '_icon_webfolders.gif';
		$this->ext_IconMode = '1'; // no context menu on icons
	}


	/**
	 * Returns the id from the record (typ. uid)
	 *
	 * @param	array		Record array
	 * @return	integer		The "uid" field value.
	 */
	function getId ($row) {
		return rawurlencode($this->treeName.$row['path']);
	}


	/**
	 * Returns jump-url parameter value.
	 *
	 * @param	array		$row The record array.
	 * @param	string		$command: SELECT, ...
	 * @return	string		The jump-url parameter.
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.rawurlencode($row['path']).']=1';
	}


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($this->mode=='elbrowser') {
			return $this->eb_PM_ATagWrap($icon,$cmd,$bMark);
		} else {
			if ($bMark)	{
				$anchor = '#'.$bMark;
				$name=' name="'.$bMark.'"';
			}
			$aUrl = $this->thisScript.'?PM='.$cmd;
			if (t3lib_div::_GP('folderOnly')) {
				$aUrl .= '&folderOnly=1';
			}
			$aUrl .= $this->PM_addParam;
			return '<a href="'.htmlspecialchars($aUrl.$anchor).'"'.$name.'>'.$icon.'</a>';
		}
	}


	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function wrapTitle($title,$row,$bank=0)	{

		if ($this->mode=='elbrowser') {
			return $this->eb_wrapTitle($title,$row);

		} elseif ($this->mode=='tceformsSelect') {
			return $this->tceformsSelect_wrapTitle($title,$row);

		} else {
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row).'\',this,\''.$this->domIdPrefix.$this->getId($row).'_'.$bank.'\');';
			return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a> ';
		}
	}


	/**
	 * Return a control (eg. selection icons) for the element
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function getControl($title,$row) {
		global $BACK_PATH;
		$control = '';
	// TODO skinning
		if (!t3lib_div::_GP('folderOnly') AND $this->modeSelIcons) {
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
			$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/plus.gif"  width="8" height="11" border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
			$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals.gif"  width="8" height="11" border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';

			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
			$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/minus.gif"  width="8" height="11" border="0" alt="" />';
			$control .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
		}

		return $control;
	}


	/**
	 * Compiles the HTML code for displaying the structure found inside the ->tree array
	 *
	 * @param	array		"tree-array" - if blank string, the internal ->tree array is used.
	 * @return	string		The HTML code for the tree
	 */
	function printTree($treeArr='')	{
		if($this->mode=='elbrowser') {
			return $this->eb_printTree($treeArr);
		} else {
			$titleLen = intval($this->BE_USER->uc['titleLen']);

			$out='';

				// put a table around it with IDs to access the rows from JS
				// not a problem if you don't need it
				// In XHTML there is no "name" attribute of <td> elements - but Mozilla will not be able to highlight rows if the name attribute is NOT there.
			$out .= '

				<!--
				  TYPO3 tree structure.
				-->
				<table cellpadding="0" cellspacing="0" border="0" class="typo3-browsetree">';


			$this->colorTRHover = $GLOBALS['SOBE']->doc->hoverColorTR ? $GLOBALS['SOBE']->doc->hoverColorTR : t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-20,-20,-20);
			$trHover = $this->colorTRHover ? (' onmouseover="this.style.backgroundColor = \''.$this->colorTRHover.'\';" onmouseout="this.style.backgroundColor = \'\'"') : '';

			foreach($treeArr as $k => $v)	{
				$idAttr = htmlspecialchars($this->domIdPrefix.$this->getId($v['row']).'_'.$v['bank']);
				$title = $this->getTitleStr($v['row'], $titleLen);
				$control = $this->getControl($title, $v['row'], $v['bank']);
				$out.='
					<tr'.$trHover.'>
						<td id="'.$idAttr.'">'.
							$v['HTML'].
							$this->wrapTitle($title, $v['row'], $v['bank']).
						'</td>
						<td width="1%" id="'.$idAttr.'Control" class="typo3-browsetree-control">'.
							($control ? $control : '<span></span>').
						'</td>
					</tr>
				';
			}
			$out .= '
				</table>';
			return $out;
		}
	}


	/**
	 * Set mointpoints for the tree
	 *
	 * @param	array		$mountpoints: ...
	 * @return	void
	 */
	function setMounts($mountpoints) {
// set automatically
//		if (is_array($mountpoints)) {
//			$this->MOUNTS = $mountpoints;
//		}
	}


	/**
	 * Returns the title for the tree
	 *
	 * @return	string
	 */
	function getTreeTitle()	{
		return $this->title;
	}

	/**
	 * Returns the defailt icon file
	 *
	 * @return	string
	 */
	function getDefaultIcon()	{
		return $this->iconPath.$this->iconName;
	}


	/**
	 * Returns the treename (used for storage of expanded levels)
	 *
	 * @return	string
	 */
	function getTreeName()	{
		return $this->treeName;
	}



	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/


	/**
	 * Returns the title of an item
	 *
	 * @param	string		$id
	 * @return	string
	 */
	function selection_getItemTitle($id)	{
		return tx_dam::path_makeRelative ($id);
	}


	/**
	 * Returns the icon of an item
	 *
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string
	 */
	function selection_getItemIcon($id, $value)	{
		if($icon = $this->getDefaultIcon()) {
			$icon =	'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],$icon, 'width="18" height="16"').' class="typo3-icon" alt="" />';
		}
		return $icon;
	}


	/**
	 * Function, processing the query part for selecting/filtering records in DAM
	 * Called from DAM
	 *
	 * @param	string		Query type: AND, OR, ...
	 * @param	string		Operator, eg. '!=' - see DAM Documentation
	 * @param	string		Category - corresponds to the "treename" used for the category tree in the nav. frame
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @param	object		Reference to the parent DAM object.
	 * @return	string
	 * @see tx_dam_selection::getWhereClausePart()
	 */
	function selection_getQueryPart($queryType, $operator, $cat, $id, $value, &$damObj)      {
		$query= $damObj->sl->getFieldMapping('tx_dam', 'file_path');
		if($queryType=='NOT') {
			$query.= ' NOT';
		}
		$likeStr = $GLOBALS['TYPO3_DB']->escapeStrForLike(tx_dam::path_makeRelative($id), 'tx_dam');
		$query.= ' LIKE BINARY '.$GLOBALS['TYPO3_DB']->fullQuoteStr($likeStr.'%', 'tx_dam');

		return array($queryType,$query);
	}




	/********************************
	 *
	 * element browser specific functions
	 *
	 ********************************/


	/**
	 * Wrapping $title in a-tags.
	 *
	 * @param	string		Title string
	 * @param	string		Item record
	 * @param	integer		Bank pointer (which mount point number)
	 * @return	string
	 */
	function eb_wrapTitle($title,$row)	{
		$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($row).'\');';
		return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
	}


	/**
	 * Wrap the plus/minus icon in a link
	 *
	 * @param	string		HTML string to wrap, probably an image tag.
	 * @param	string		Command for 'PM' get var
	 * @param	boolean		If set, the link will have a anchor point (=$bMark) and a name attribute (=$bMark)
	 * @return	string		Link-wrapped input string
	 * @access private
	 */
	function eb_PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?PM='.$cmd.'&act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.'\',\''.$anchor.'\');';
		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}


	/**
	 * Create the folder navigation tree in HTML
	 *
	 * @param	mixed		Input tree array. If not array, then $this->tree is used.
	 * @return	string		HTML output of the tree.
	 */
	function eb_printTree($treeArr='')	{
		global  $BE_USER;

		$titleLen=intval($BE_USER->uc['titleLen']);

		if (!is_array($treeArr))	$treeArr=$this->tree;

		$out='';
		$c=0;

// TODO			// Preparing the current-path string (if found in the listing we will see a red blinking arrow).
		if (!$GLOBALS['SOBE']->curUrlInfo['value'])	{
			$cmpPath='';
		} else if (substr(trim($GLOBALS['SOBE']->curUrlInfo['info']),-1)!='/')	{
			$cmpPath=PATH_site.dirname($GLOBALS['SOBE']->curUrlInfo['info']).'/';
		} else {
			$cmpPath=PATH_site.$GLOBALS['SOBE']->curUrlInfo['info'];
		}

			// Traverse rows for the tree and print them into table rows:
		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass=($c+1)%2 ? 'bgColor' : 'bgColor-10';

				// Creating blinking arrow, if applicable:
			if ($GLOBALS['SOBE']->curUrlInfo['act']=='file' && $cmpPath==$v['row']['path'])	{
				$arrCol='<td><img'.t3lib_iconWorks::skinImg('','gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" /></td>';
				$bgColorClass='bgColor4';
			} else {
				$arrCol='<td></td>';
			}
				// Create arrow-bullet for file listing (if folder path is linkable):
			$aOnClick = 'return jumpToUrl(\''.$this->thisScript.'?act='.$GLOBALS['SOBE']->act.'&mode='.$GLOBALS['SOBE']->mode.'&bparams='.$GLOBALS['SOBE']->bparams.$this->getJumpToParam($v['row']).'\');';
			$cEbullet = $this->ext_isLinkable($v['row']) ? '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg('','gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' : '';

				// Put table row with folder together:
			$out.='
				<tr class="'.$bgColorClass.'">
					<td nowrap="nowrap">'.$v['HTML'].$this->wrapTitle(t3lib_div::fixed_lgd_cs($v['row']['title'], $titleLen), $v['row']).'</td>
					'.$arrCol.'
					<td width="1%">'.$cEbullet.'</td>
				</tr>';
		}

		$out='

			<!--
				Folder tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" class="typo3-browsetree" style="width:100%">
				'.$out.'
			</table>';
		return $out;
	}


	/**
	 * Returns true if the input "record" contains a folder which can be linked.
	 *
	 * @param	array		Array with information about the folder element. Contains keys like title, uid, path, _title
	 * @return	boolean		True is returned if the path is found in the web-part of the the server and is NOT a recycler or temp folder
	 */
	function ext_isLinkable($v)	{
		$webpath=t3lib_BEfunc::getPathType_web_nonweb($v['path']);	// Checking, if the input path is a web-path.
		if (strstr($v['path'],'_recycler_') || strstr($v['path'],'_temp_') || $webpath!='web')	{
			return 0;
		}
		return 1;
	}

}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionFolder.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/components/class.tx_dam_selectionFolder.php']);
}
?>