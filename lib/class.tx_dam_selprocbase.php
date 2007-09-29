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
 *   86: class tx_dam_browseTree extends t3lib_treeView 
 *  102:     function getJumpToParam($row, $command='SELECT') 
 *  113:     function wrapTitle($title,$row)	
 *  143:     function PM_ATagWrap($icon,$cmd,$bMark='')	
 *  157:     function getRootIcon($rec) 
 *  168:     function printTree($treeArr='')	
 *
 *              SECTION: DAM specific functions
 *  189:     function dam_defaultIcon()	
 *  199:     function dam_treeTitle()	
 *  208:     function dam_treeName()	
 *  220:     function dam_itemTitle($id)	
 *  243:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *
 *              SECTION: element browser specific functions
 *  262:     function eb_wrapTitle($title,$row)	
 *  277:     function eb_PM_ATagWrap($icon,$cmd,$bMark='')	
 *  292:     function eb_printTree($treeArr='')	
 *  352:     function ext_isLinkable() 
 *
 *
 *  364: class tx_dam_selProcBase 
 *  370:     function tx_dam_selProcBase()	
 *  389:     function init()	
 *
 *              SECTION: DAM specific functions
 *  405:     function dam_defaultIcon()	
 *  415:     function dam_treeTitle()	
 *  424:     function dam_treeName()	
 *  437:     function dam_itemTitle($id, $value)	
 *  454:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *
 * TOTAL FUNCTIONS: 21
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_t3lib.'class.t3lib_treeview.php');

require_once(PATH_txdam.'lib/class.tx_dam_div.php');
 
 
/**
 * Base class for selection tree classes
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_browseTree extends t3lib_treeView {
	
	var $isTreeViewClass = TRUE;
	
	/**
	 * element browser mode
	 */
	var $modeEB = false;
	
	/**
	 * enables selection icons: + = -
	 */	
	var $modeSelIcons = true;
	
	var $deselectValue = 0;

	var $clickMenuScript='';

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @param	[type]		$command: ...
	 * @return	[type]		...
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.$row['uid'].']=1';
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$title: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function wrapTitle($title,$row)	{
		global $BACK_PATH;
		
		if ($this->modeEB) {
			return $this->eb_wrapTitle($title,$row);
		} else {
			$extra = '';
			if($row['uid'] AND $this->modeSelIcons){			
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/plus.gif"   style="margin-left:2px;" width="8" height="11" border="0" alt="" /></a>';
	
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals.gif" style="margin-left:6px;" width="8" height="11" border="0" alt="" /></a>';
	
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/minus.gif"  style="margin-left:6px;margin-right:2px;" width="8" height="11" border="0" alt="" /></a>';
	
				$extra = ' &nbsp;<span class="txdam-editbar">'.$extra.'</span>';
	
			}
			return parent::wrapTitle($title,$row).$extra;
		}
	}



	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$icon: ...
	 * @param	[type]		$cmd: ...
	 * @param	[type]		$bMark: ...
	 * @return	[type]		...
	 */
	function PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($this->modeEB) {
			return $this->eb_PM_ATagWrap($icon,$cmd,$bMark);
		} else {
			return parent::PM_ATagWrap($icon,$cmd,$bMark);
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$rec: ...
	 * @return	[type]		...
	 */
	function getRootIcon($rec) {
		global $BACK_PATH;
		return $this->wrapIcon('<img src="'.$BACK_PATH.PATH_txdam_rel.'i/catfolder.gif" width="18" height="16" align="top" alt="" />',$rec);
	}
	
	
	/**
	 * Wrapping the image tag, $icon, for the row, $row (except for mount points)
	 *
	 * @param	string		The image tag for the icon
	 * @param	array		The row for the current element
	 * @return	string		The processed icon input value.
	 * @access private
	 */
	function wrapIcon($icon,$row)	{
		global $SOBE;
		
			// Add title attribute to input icon tag
		$theIcon = $this->addTagAttributes($icon,($this->titleAttrib ? $this->titleAttrib.'="'.$this->getTitleAttrib($row).'"' : ''));

			// Wrap icon in click-menu link.
		if (!$this->ext_IconMode)	{
			#$theIcon = $SOBE->doc->wrapClickMenuOnIcon($theIcon,$this->table,$this->getId($row),0);
#TODO	
			if (t3lib_extmgm::isLoaded('dam_catedit')) {
				require_once(t3lib_extmgm::extPath('dam_catedit').'lib/class.tx_damcatedit_div.php');
				$theIcon = tx_damcatedit_div::clickMenuWrap($theIcon, $this->table, $this->getId($row), 0, $addParams='', $enDisItems='', '', $this->clickMenuScript);
			}
			
		} elseif (!strcmp($this->ext_IconMode,'titlelink'))	{
// unused for now
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row).'\',this,\''.$this->domIdPrefix.$this->getId($row).'_'.$this->bank.'\');';
			$theIcon='<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$theIcon.'</a>';
		}
		return $theIcon;
	}	
	

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$treeArr: ...
	 * @return	[type]		...
	 */
	function printTree($treeArr='')	{
		if($this->modeEB) {
			return $this->eb_printTree($treeArr);
		} else {
			return parent::printTree($treeArr);
		}	
	}
	
	
	
	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/
	 
	 
	 	
	/**
	 * @return	[type]		...
	 */
	function dam_defaultIcon()	{
		return $this->iconPath.$this->iconName;
	}
	
	/**
	 * Returns the title for the tree
	 * 
	 * @return	string		
	 */
	function dam_treeTitle()	{
		return $this->title;
	}

	/**
	 * Returns the treename (used for storage of expanded levels)
	 * 
	 * @return	string		
	 */
	function dam_treeName()	{
		return $this->treeName;
	}
	
	

	/**
	 * Returns the title of an item
	 * 
	 * @param	[type]		$id: ...
	 * @return	string		
	 */
	function dam_itemTitle($id)	{	
		$itemTitle=$id;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',',$this->fieldArray), $this->table, 'uid='.intval($id));
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$itemTitle = $this->getTitleStr($row);
		}	
		return $itemTitle;
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
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      {
#		return array($queryType,$query);
	}	
	
	
	
	/********************************
	 *
	 * element browser specific functions
	 *
	 ********************************/
	 
	 
	 
	/**
	 * @param	[type]		$title: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function eb_wrapTitle($title,$row)	{
		global $SOBE;
		
		$aOnClick = 'return jumpToUrl(\''.$this->script.'?act='.$SOBE->act.'&mode='.$SOBE->mode.$this->getJumpToParam($row).'\');';
		return '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$icon: ...
	 * @param	[type]		$cmd: ...
	 * @param	[type]		$bMark: ...
	 * @return	[type]		...
	 */
	function eb_PM_ATagWrap($icon,$cmd,$bMark='')	{
		if ($bMark)	{
			$anchor = '#'.$bMark;
			$name=' name="'.$bMark.'"';
		}
		$aOnClick = 'return jumpToUrl(\''.$this->script.'?PM='.$cmd.'\',\''.$anchor.'\');';
		return '<a href="#"'.$name.' onclick="'.htmlspecialchars($aOnClick).'">'.$icon.'</a>';
	}
				
	/**
	 * Create the folder navigation tree in HTML
	 * 
	 * @param	mixed		Input tree array. If not array, then $this->tree is used.
	 * @return	string		HTML output of the tree.
	 */
	function eb_printTree($treeArr='')	{
		global $SOBE, $BE_USER;
		
		$titleLen=intval($BE_USER->uc['titleLen']);	
		
		if (!is_array($treeArr))	$treeArr=$this->tree;
		
		$out='';
		$c=0;
		
			// Preparing the current-path string (if found in the listing we will see a red blinking arrow).
		if (!$SOBE->curUrlInfo['value'])	{
			$cmpPath='';
		} else if (substr(trim($SOBE->curUrlInfo['info']),-1)!='/')	{
			$cmpPath=PATH_site.dirname($SOBE->curUrlInfo['info']).'/';
		} else {
			$cmpPath=PATH_site.$SOBE->curUrlInfo['info'];
		}
		
			// Traverse rows for the tree and print them into table rows:
		foreach($treeArr as $k => $v)	{
			$c++;
			$bgColorClass=($c+1)%2 ? 'bgColor' : 'bgColor-10';
			
				// Creating blinking arrow, if applicable:
			if ($SOBE->curUrlInfo['act']=='file' && $cmpPath==$v['row']['path'])	{
				$arrCol='<td><img'.t3lib_iconWorks::skinImg('','gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" /></td>';
				$bgColorClass='bgColor4';
			} else {
				$arrCol='<td></td>';
			}
				// Create arrow-bullet for file listing (if folder path is linkable):
			$aOnClick = 'return jumpToUrl(\''.$this->script.'?act='.$SOBE->act.'&mode='.$SOBE->mode.$this->getJumpToParam($row).'\');';
			$cEbullet = $this->ext_isLinkable($v['row']) ? '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img'.t3lib_iconWorks::skinImg('','gfx/ol/arrowbullet.gif','width="18" height="16"').' alt="" /></a>' : '';
			
				// Put table row with folder together:
			$out.='
				<tr class="'.$bgColorClass.'">
					<td nowrap="nowrap">'.$v['HTML'].$this->wrapTitle(t3lib_div::fixed_lgd_cs($v['row']['title'],$titleLen),$v['row']).'</td>
					'.$arrCol.'
					<td width="1%">'.$cEbullet.'</td>
				</tr>';
		}
		
		$out='
		
			<!--
				Folder tree:
			-->
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-tree" style="width:100%">
				'.$out.'
			</table>';
		return $out;
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function ext_isLinkable() {
		return true;
	}
} 

/**
 * Base class for selection classes
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_selProcBase {

	var $isPureSelectionClass = TRUE;
	
	var $deselectValue = 0;
	
	function tx_dam_selProcBase()	{
#		global $LANG, $BACK_PATH;

#		$this->isTreeViewClass = FALSE;
#		$this->isPureSelectionClass = TRUE;
		
#		$this->title=$LANG->sL('LLL:EXT:dam/lib/locallang.php:mediaTypes',1);
#		$this->treeName='txdamStrSearch';

#		$this->iconName = 'mediatype.gif';
#		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';

	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function init()	{
	}



	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/
	 
	 
	 
	/**
	 * @return	[type]		...
	 */
	function dam_defaultIcon()	{
		return $this->iconPath.$this->iconName;
	}
	
	/**
	 * Returns the title for the tree
	 * 
	 * @return	string		
	 */
	function dam_treeTitle()	{
		return $this->title;
	}

	/**
	 * Returns the treename (used for storage of expanded levels)
	 * 
	 * @return	string		
	 */
	function dam_treeName()	{
		return $this->treeName;
	}
	
	

	/**
	 * Returns the title of an item
	 * 
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string		
	 */
	function dam_itemTitle($id, $value)	{	
		return $id;
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
	 * @see tx_dam_SCbase::getWhereClausePart()
	 */
	function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      {
#		$query= 'tx_dam.tx_damdemo_customcategory';
#		if($operator=='!=') {
#			$query.= ' NOT';
#		}
#		$query.= " LIKE BINARY '".$id."'";
#		
#		return array($queryType,$query);
	}		
}


// No XCLASS inclusion code: this is a base class
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selprocbase.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_selprocbase.php']);
//}

?>