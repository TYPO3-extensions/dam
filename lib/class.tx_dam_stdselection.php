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
 * Contains standard selection trees/rules.
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
 *  111: class tx_dam_stdselectionFolder extends t3lib_folderTree  
 *  117:     function tx_dam_stdselectionFolder()	
 *  134:     function getId ($row) 
 *  145:     function getJumpToParam($row, $command='SELECT') 
 *  157:     function PM_ATagWrap($icon,$cmd,$bMark='')	
 *  180:     function wrapTitle($title,$row)	
 *  212:     function printTree($treeArr='')	
 *
 *              SECTION: DAM specific functions
 *  231:     function dam_defaultIcon()	
 *  241:     function dam_treeTitle()	
 *  250:     function dam_treeName()	
 *  260:     function dam_itemTitle($id)	
 *  277:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *
 *              SECTION: element browser specific functions
 *  303:     function eb_wrapTitle($title,$row)	
 *  319:     function eb_PM_ATagWrap($icon,$cmd,$bMark='')	
 *  334:     function eb_printTree($treeArr='')	
 *  395:     function ext_isLinkable($v)	
 *
 *
 *  417: class tx_dam_stdselectionCategory extends tx_dam_browseTree 
 *  419:     function tx_dam_stdselectionCategory()	
 *
 *              SECTION: DAM specific functions
 *  456:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *
 *
 *  485: class tx_dam_stdselectionMeTypes extends tx_dam_browseTree 
 *  487:     function tx_dam_stdselectionMeTypes()	
 *  513:     function getJumpToParam($row, $command='SELECT') 
 *  528:     function getTitleStr ($row) 
 *  540:     function getRootIcon($rec) 
 *
 *              SECTION: DAM specific functions
 *  565:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *
 *
 *  580: class tx_dam_stringSearch extends tx_dam_selProcBase 
 *  582:     function tx_dam_stringSearch()	
 *  607:     function dam_itemTitle($id, $value)	
 *  624:     function dam_selectProc($queryType, $operator, $cat, $id, $value, &$damObj)      
 *  636:     function getSearchWhereClause($searchString)	
 *
 *
 *  675: class tx_dam_miTypesTree extends txdamBrowseTree 
 *  677:     function txdamMiTypesTree()	
 *
 *
 *  707: class tx_dam_statusTree extends txdamBrowseTree 
 *  709:     function txdamStatusTree()	
 *
 * TOTAL FUNCTIONS: 28
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



require_once(PATH_t3lib.'class.t3lib_foldertree.php');;

require_once(PATH_txdam.'lib/class.tx_dam_div.php');
require_once(PATH_txdam.'lib/class.tx_dam_selprocbase.php');
 
 
 
/**
 * folder tree class
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_stdselectionFolder extends t3lib_folderTree  {

	/**
	 * element browser mode
	 */
	var $modeEB = false;
	
	/**
	 * enables selection icons: + = -
	 */	
	var $modeSelIcons = true;
	
	var $deselectValue = 0;
	
	function tx_dam_stdselectionFolder()	{
		
		$this->isTreeViewClass = TRUE;
		
		$this->title='Folder tree';
		$this->treeName='txdamFolder';
		$this->domIdPrefix=$this->treeName;
		$this->MOUNTS = $GLOBALS['FILEMOUNTS'];
		$this->ext_IconMode = '1'; // no context menu on icons
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function getId ($row) {
		return rawurlencode($this->treeName.$row['path']);
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @param	[type]		$command: ...
	 * @return	[type]		...
	 */
	function getJumpToParam($row, $command='SELECT') {
		return '&SLCMD['.$command.']['.$this->treeName.']['.rawurlencode($row['path']).']=1';
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
			if ($bMark)	{
				$anchor = '#'.$bMark;
				$name=' name="'.$bMark.'"';
			}
			$aUrl = $this->thisScript.'?PM='.$cmd;
			if (t3lib_div::_GP('folderOnly')) {
				$aUrl .= '&folderOnly=1';
			}
			return '<a href="'.htmlspecialchars($aUrl.$anchor).'"'.$name.'>'.$icon.'</a>';
		}
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
			$out='';
			$extra = '';
	
			$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row).'\',this);';
			$out .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a> ';
	
			if (!t3lib_div::_GP('folderOnly') AND $this->modeSelIcons) {
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'OR').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/plus.gif"  style="margin-left:2px;" width="8" height="11" border="0" alt="" /></a>';
	
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'AND').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/equals.gif" style="margin-left:6px;" width="8" height="11" border="0" alt="" /></a>';
	
				$aOnClick = 'return jumpTo(\''.$this->getJumpToParam($row,'NOT').'\',this,\''.$this->treeName.'\');';
				$extra .= '<a href="#" onclick="'.htmlspecialchars($aOnClick).'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/minus.gif" style="margin-left:6px;margin-right:2px;" width="8" height="11" border="0" alt="" /></a>';
	
				$extra = ' &nbsp;<span class="txdam-editbar">'.$extra.'</span>';
			}
			return $out.$extra;
		}
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
	 * [Describe function...]
	 * 
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
		return tx_dam_div::getRelPath ($id);
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
		$query= 'tx_dam.file_path';
		if($operator=='!=') {
			$query.= ' NOT';
		}
		$query.= " LIKE BINARY '".tx_dam_div::getRelPath ($id)."%'";
		
		return array($queryType,$query);
	}
	
	

	/********************************
	 *
	 * element browser specific functions
	 *
	 ********************************/
	 
	
	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$title: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function eb_wrapTitle($title,$row)	{
		global $SOBE;
		
#		$aOnClick = 'return jumpToUrl(\''.$this->script.'?act='.$SOBE->act.'&mode='.$SOBE->mode.'&expandFolder='.rawurlencode($row['path']).'\');';
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
			$aOnClick = 'return jumpToUrl(\''.$this->script.'?act='.$SOBE->act.'&mode='.$SOBE->mode.'&expandFolder='.rawurlencode($v['row']['path']).'\');';
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
			<table border="0" cellpadding="0" cellspacing="0" id="typo3-tree" style="width:100%">
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






/**
 * category tree class
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_stdselectionCategory extends tx_dam_browseTree {
	
	function tx_dam_stdselectionCategory()	{
		global $LANG, $BACK_PATH;

		$this->isTreeViewClass = TRUE;
		
		$this->title=$LANG->sL('LLL:EXT:dam/lib/locallang.php:categories',1);
		$this->treeName='txdamCat';
		$this->domIdPrefix=$this->treeName;
		$this->parentField='parent_id';
		$this->table='tx_dam_cat';
		$this->iconName = 'cat.gif';
		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';
		$this->clause=' AND NOT deleted ORDER BY sorting,title';
		$this->fieldArray = Array('uid','title');
		$this->defaultList = 'uid,pid,tstamp,sorting';
		$this->ext_IconMode = '1'; // no context menu on icons
	}
	
	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/

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
#		$query= 'tx_dam_mm_cat.uid_foreign'.$operator.intval($id);
#		$damObj->qg->queryAddCategoryJoin();
		
		$catUidList = tx_dam_db::uniqueList(intval($id), tx_dam_db::getSubRecordsIdList(intval($id),99,'tx_dam_cat'));
		if ($operator=='!=')	{
			$query= 'tx_dam_mm_cat.uid_foreign NOT IN ('.$catUidList.')';
		} else {
			$query= 'tx_dam_mm_cat.uid_foreign IN ('.$catUidList.')';
		}

		$damObj->qg->queryAddCategoryJoin();
		
		return array($queryType,$query);
	}
}






/**
 * media type tree class
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_stdselectionMeTypes extends tx_dam_browseTree {

	function tx_dam_stdselectionMeTypes()	{
		global $LANG, $BACK_PATH, $currentTypes;

		$this->isTreeViewClass = TRUE;
		
		$this->title=$LANG->sL('LLL:EXT:dam/lib/locallang.php:mediaTypes',1);
		$this->treeName='txdamMedia';
		$this->domIdPrefix=$this->treeName;
		$this->iconName = 'mediatype.gif';
		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';
		$this->ext_IconMode = '1'; // no context menu on icons

		$this->table='tx_dam_metypes_avail';
		$this->parentField='parent_id';
		$this->clause=' ORDER BY sorting,title';
		$this->fieldArray = Array('uid','parent_id','title','type','sorting');
		$this->defaultList = 'uid,pid,tstamp,sorting';
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @param	[type]		$command: ...
	 * @return	[type]		...
	 */
	function getJumpToParam($row, $command='SELECT') {
		if($row['parent_id']){
			$id = $row['title'];
		} else {
			$id = $row['type'];
		}
		return '&SLCMD['.$command.']['.$this->treeName.']['.$id.']=1';
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function getTitleStr ($row) {
		global $LANG;
		$title = $LANG->getLL($row['title']);
		return $title ? $title : $row['title'];
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$rec: ...
	 * @return	[type]		...
	 */
	function getRootIcon($rec) {
		global $BACK_PATH;
		return $this->wrapIcon('<img src="'.$BACK_PATH.PATH_txdam_rel.'i/mediafolder.gif" width="18" height="16" align="top" alt="" />',$rec);
	}

	
	/********************************
	 *
	 * DAM specific functions
	 *
	 ********************************/

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
		if (t3lib_div::testInt($id)) {
			$query= 'tx_dam.media_type'.$operator.intval($id);
		} else {
			$query= 'tx_dam.file_type'.$operator."'".$id."'";
		}
		
		return array($queryType,$query);
	}
}

	/**
	 * [Describe function...]
	 * 
	 */
class tx_dam_stringSearch extends tx_dam_selProcBase {
	
	function tx_dam_stringSearch()	{
		global $LANG, $BACK_PATH;

		$this->isTreeViewClass = FALSE;
		$this->isPureSelectionClass = TRUE;
		
		$this->deselectValue = '';
		
		$this->title = $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.enterSearchString',1);
		$this->treeName = 'txdamStrSearch';
		
		$this->table = 'tx_dam';

		$this->iconName = 'mediatype.gif';
		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';

	}	

	/**
	 * Returns the title of an item
	 * 
	 * @param	string		The select value/id
	 * @param	string		The select value (true/false,...)
	 * @return	string		
	 */
	function dam_itemTitle($id, $value)	{	
		return $value;
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
		$query = $this->getSearchWhereClause($value);
		
		return array('WHERE',$query);
	}

	/**
	 * Creates part of query for searching after a word ($this->searchString) fields in input table
	 * 
	 * @param	string		Table, in which the fields are being searched.
	 * @return	string		Returns part of WHERE-clause for searching, if applicable.
	 */
	function getSearchWhereClause($searchString)	{
		global $TCA;

		$table = $this->table;
		
			// Make query, only if table is valid and a search string is actually defined:
		if ($TCA[$table] && $searchString)	{

				// Loading full table description - we need to traverse fields:
			t3lib_div::loadTCA($table);

				// Initialize field array:
			$sfields=array();
			$sfields[]='uid';	// Adding "uid" by default.

				// Traverse the configured columns and add all columns that can be searched:
			foreach($TCA[$table]['columns'] as $fieldName => $info)	{
				if ($info['config']['type']=='text' || ($info['config']['type']=='input' && !ereg('date|time|int',$info['config']['eval'])))	{
					$sfields[]=$fieldName;
				}
			}

				// If search-fields were defined (and there always are) we create the query:
			if (count($sfields))	{
				$like=' LIKE "%'.$GLOBALS['TYPO3_DB']->quoteStr($searchString, $table).'%"';		// Free-text searching...
				$queryPart = ' AND ('.implode($like.' OR ',$sfields).$like.')';

					// Return query:
				return $queryPart;
			}
		}
	}

}

/* old - not needed - maybe wrong


// browsing by mime types
class tx_dam_miTypesTree extends txdamBrowseTree {

	function txdamMiTypesTree()	{
		global $currentTypes, $BACK_PATH;

		$this->title='Mime Types';
		$this->treeName='txdamMime';
		$this->iconName = 'mimetype.gif';
		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';
		$this->BE_USER = $GLOBALS['BE_USER'];
		$this->parentField='parent_id';
		$this->ext_IconMode = '1'; // no context menu on icons
if (1) {
		$this->table='tx_dam_mitypes_avail';
		$this->clause=' ORDER BY title';
		$this->fieldArray = Array('uid','title');
		$this->defaultList = 'uid,pid,tstamp,sorting';

} else {
		$this->table='';
		$this->data = array();
		$this->setDBFromArray($currentTypes['mime'],true);
#		$this->setDataFromArray($currentTypes['mime'],true);
}
	}

	f_unction getRootIcon($rec) {
		global $BACK_PATH;
		return $this->wrapIcon('<img src="'.$BACK_PATH.PATH_txdam_rel.'i/mimefolder.gif" width="18" height="16" align="top" alt="" />',$rec);
	}
}

// browsing by status of the media record
class tx_dam_statusTree extends txdamBrowseTree {

	function txdamStatusTree()	{
		global $BACK_PATH;
		
		$this->title='Status';
		$this->table='';
		$this->treeName='txdamStatus';
		$this->iconName = 'statustype.gif';
		$this->iconPath = $BACK_PATH.PATH_txdam_rel.'i/';
		$this->BE_USER = $GLOBALS['BE_USER'];
		$this->ext_IconMode = '1'; // no context menu on icons
		$data = array (
			array (	'available' => 'available',	),
			array (	'not available' => 'not available',	),
			array (	'review suggested' => 'review suggested',	),
			array (	'review needed' => 'review needed',	),
		);
		$this->setDataFromArray($data,true);
	}

	f_unction getRootIcon($rec) {
		global $BACK_PATH;
		return $this->wrapIcon('<img src="'.$BACK_PATH.PATH_txdam_rel.'i/statusfolder.gif" width="18" height="16" align="top" alt="" />',$rec);
	}
}
*/


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_stdselection.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_stdselection.php']);
}
?>