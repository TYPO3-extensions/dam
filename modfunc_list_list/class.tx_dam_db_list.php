<?php
/**
 * ************************************************************
 *  Copyright notice
 *  
 *  (c) 1999-2003 Kasper Skårhøj (kasper@typo3.com)
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
 * **************************************************************/

class tx_dam_db_list {
		// Used in this class:
	var $showIcon = 1;
	var $no_noWrap = 0;
	var $oddColumnsTDParams ='';			// If set this is <td>-params for odd columns in addElement. Used with db_layout / pages section
	var $backPath='';

		// Not used in this class - but maybe extension classes...
	var $fixedL = 50;						// Max length of strings
	var $headLineCol = '#dddddd';			// Head line color
	var $subHeadLineCol = '#eeeeee';
	var $thumbScript = 'thumbs.php';
	var $thumbs = 0;						// Boolean. Thumbnails on records containing files (pictures)
	var $script = 'index.php';


	var $HTMLcode='';			// String with accumulated HTML content



		// internal
	var $table='';		// set to the tablename if single-table mode
	var $tableList='';	// Specify a list of tables which are the only ones allowed to be displayed.
	var $searchString='';
	var $returnUrl='';
	var $staticParams='';

	var $res;
	var $resultsPerPage=0;		// "LIMIT " in SQL...
	var $firstItemNum=0;
	var $resCount = '';					// This could be set to the total number of items. Used by the fwd_rew_navigation...
	var $eCounter=0;		// Counting the elements no matter what...

	var $fieldArray = Array();				// Decides the columns shown. Filled with values that refers to the keys of the data-array. $this->fieldArray[0] is the title column.
	var $element_tdParams=array();		// Keys are fieldnames and values are td-parameters to add in addElement();
	var $allFields=true;
	var $setFields=array();

	var $recPath_cache=array();
	var $perms_clause='';
	var $calcPerms=0;
	var $currentTable = array();

	var $duplicateStack=array();


 	var $noControlPanels = 0;
	var $alternateBgColors=0;
	var $showElements = array();

	var $dontShowClipControlPanels=1;


	function init()	{
		global $TCA,$BE_USER,$SOBE;

		if (!count($this->showElements)) {
			$this->showElements = array('viewPage','editPage','newPage','unHidePage','movePage','pasteIntoPage','clearPageCache',
							'refresh',
							'permsRec','revertRec','editRec','infoRec','newRec','sortRec','unHideRec','delRec');
		}

		$this->HTMLcode='';

		$this->table='tx_dam';
		$this->thumbs = $BE_USER->uc['thumbnailsByDefault'];
		$this->returnUrl=t3lib_div::_GP('returnUrl');
		$this->showElements = array('cvsExp','refresh','editRec','sortRec','unHideRec','delRec','revertRec');


		if (!$GLOBALS['TYPO3_CONF_VARS']['GFX']['thumbnails'])	{
			$this->thumbScript='gfx/notfound_thumb.gif';
		}
	}




	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function setDispFields()	{
			// Display fields:
		$dispFields = $GLOBALS['BE_USER']->getModuleData('tx_dam_db_list.php/displayFields');
		$dispFields_in = t3lib_div::_GP('displayFields');
		if (is_array($dispFields_in))	{
			reset($dispFields_in);
			$tKey = key($dispFields_in);
			$dispFields[$tKey]=$dispFields_in[$tKey];
			$GLOBALS['BE_USER']->pushModuleData('tx_dam_db_list.php/displayFields',$dispFields);
		}
		$this->setFields=$dispFields;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$rowlist: ...
	 * @return	[type]		...
	 */
	function getSelFieldList($table,$rowlist)	{
		global $TCA, $BACK_PATH, $SOBE;
		t3lib_div::loadTCA($table);

			// Init
		$titleCol = $TCA[$table]['ctrl']['label'];
		$thumbsCol = $TCA[$table]['ctrl']['thumbnail'];


			// Cleaning rowlist for duplicates and place the $titleCol as the first column always!
		$this->fieldArray=array();
		$this->fieldArray[] = $titleCol;
		$this->fieldArray[] = '_CONTROL_';


		$this->fieldArray=array_unique(array_merge($this->fieldArray,t3lib_div::trimExplode(',',$rowlist,1)));

			// Select fields:
		$selectFields = $this->fieldArray;
		$selectFields[] = 'uid';
		$selectFields[] = 'pid';
		if ($thumbsCol)	$selectFields[] = $thumbsCol;	// adding column for thumbnails

		if (is_array($TCA[$table]['ctrl']['enablecolumns']))	{
			$selectFields = array_merge($selectFields,$TCA[$table]['ctrl']['enablecolumns']);
		}
		if ($TCA[$table]['ctrl']['type'])	{
			$selectFields[] = $TCA[$table]['ctrl']['type'];
		}
		if ($TCA[$table]['ctrl']['typeicon_column'])	{
			$selectFields[] = $TCA[$table]['ctrl']['typeicon_column'];
		}
		if ($TCA[$table]['ctrl']['label_alt'])	{
			$selectFields = array_merge($selectFields,t3lib_div::trimExplode(',',$TCA[$table]['ctrl']['label_alt'],1));
		}
		$selectFields = array_unique($selectFields);		// Unique list!
		$selectFields = array_intersect($selectFields,$this->makeFieldList($table,1));		// Making sure that the fields in the field-list ARE in the field-list from TCA!
		$selFieldList = implode(',',$selectFields);		// implode it into a list of fields for the SQL-statement.

		return $selFieldList;
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function generateList()	{
		global $TCA, $LANG;

		$table = $this->table;
		t3lib_div::loadTCA($table);
		$val=$TCA[$table];

		$fields = $this->makeFieldList($table);
		if (is_array($this->setFields[$table]))	{
			$fields = array_intersect($fields,$this->setFields[$table]);
		} else {
			$fields = array();
		}

		$this->HTMLcode.=$this->getTable($table,implode(',',$fields));
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$rowlist: ...
	 * @return	[type]		...
	 */
	function getTable($table,$rowlist)	{
		global $TCA, $BACK_PATH, $SOBE, $LANG;
		
		t3lib_div::loadTCA($table);

			// Init
		$titleCol = $TCA[$table]['ctrl']['label'];
		$thumbsCol = $TCA[$table]['ctrl']['thumbnail'];

		$selFieldList = $this->getSelFieldList($table,$rowlist);

		$dbCount=0;
		if ($this->resCountAll AND $this->res)	{
			$dbCount = $GLOBALS['TYPO3_DB']->sql_num_rows($this->res);
		}
#debug($dbCount,'$dbCount');
		$shEl = $this->showElements;
		$out='';
		if ($dbCount)	{
				// Start table:
			$out.='<table border="0" cellpadding="0" cellspacing="0">';

				// half line is drawn
			$theData = Array();
			if (!$this->table && !$rowlist)	{
				$theData[$titleCol] = '<img src=clear.gif width="'.($GLOBALS['SOBE']->MOD_SETTINGS['bigControlPanel']?"230":"350").'" height=1>';
				if (in_array('_CONTROL_',$this->fieldArray))	$theData['_CONTROL_']='';
			}
			$out.=$this->addelement('',$theData);

				// header line is drawn
			$theData = Array();
#			$theData[$titleCol] = '<b>'.$GLOBALS['LANG']->sL($TCA[$table]['ctrl']['title']).'</b> ('.$this->resCount.')';
			$theUpIcon = '';

			$out.=$this->addelement($theUpIcon,$theData,' bgcolor="'.$this->headLineCol.'"');

				// Fixing a order table for sortby tables
			$this->currentTable=array();
			$currentIdList=array();
			$doSort = ($TCA[$table]['ctrl']['sortby'] && !$this->sortField);
#			if ($this->table || $doSort)	{
				$prevUid=0;
				$prevPrevUid=0;
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))	{
					$currentIdList[] = $row['uid'];
					if ($doSort)	{
						if ($prevUid)	{
							$this->currentTable['prev'][$row['uid']]=$prevPrevUid;
							$this->currentTable['next'][$prevUid]='-'.$row['uid'];
							$this->currentTable['prevUid'][$row['uid']]=$prevUid;
						}
						$prevPrevUid = isset($this->currentTable['prev'][$row['uid']]) ? -$prevUid : $row['pid'];
						$prevUid=$row['uid'];
					}
				}
				$GLOBALS['TYPO3_DB']->sql_data_seek($this->res,0);
#			}
//			debug($this->currentTable);


				// items
			$iOut='';
			$this->duplicateStack=array();
			$this->eCounter=$this->firstItemNum;
			$cc=0;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->res))	{
				if($this->eCounter==$this->firstItemNum) {
					$iOut.= $this->fwd_rwd_nav('rwd');
				}

				$cc++;
				$row_bgColor=
					$this->alternateBgColors ?
					(($cc%2)?'' :' bgColor="'.t3lib_div::modifyHTMLColor($GLOBALS['SOBE']->doc->bgColor4,+10,+10,+10).'"') :
					'';

					// Initialization
				$iconfile = t3lib_iconWorks::getIcon($table,$row);
				$alttext = t3lib_BEfunc::getRecordIconAltText($row,$table);
				$recTitle = t3lib_BEfunc::getRecordTitle($table,$row,1);



					// The icon with link
				$theIcon = '<img src="'.$this->backPath.$iconfile.'" width="18" height="16" border="0" title="'.$alttext.'" />';
				$theIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($theIcon,$table,$row['uid']);

					// 	Preparing and getting the data-array
				$theData = Array();
				reset($this->fieldArray);
				while(list(,$fCol)=each($this->fieldArray))	{
					if ($fCol==$titleCol)	{
						$theData[$fCol] = $this->linkWrapItems($table,$row['uid'],$recTitle,$row).'&nbsp;';
					} elseif ($fCol=='pid') {
						$theData[$fCol]=$row[$fCol];
					} elseif ($fCol=='_CONTROL_') {
						$theData[$fCol]=$this->makeControl($table,$row);
					} else {
						$theData[$fCol]='&nbsp;'.t3lib_BEfunc::getProcessedValueExtra($table,$fCol,$row[$fCol],100);
					}
				}

				if($table=='tx_dam') {
					$params='SLCMD[DESELECT_ID][tx_dam]['.$row['uid'].']=1';
					$actionIcon='<a href="index.php?'.$params.'"><img src="'.$BACK_PATH.PATH_txdam_rel.'i/button_deselect.gif" width="11" height="10" border="0" title="'.$LANG->getLL('deselect').'" align="top" /></a>';
				}
				
				$iOut.=$this->addElement($theIcon,$theData,$row_bgColor,$actionIcon);
				
					// Thumbsnails?
				if ($this->thumbs && trim($row[$thumbsCol]))	{
					$iOut.=$this->addelement('', Array($titleCol=>$this->thumbCode($row,$table,$thumbsCol)),$row_bgColor);
				}
				$this->eCounter++;
			}
			if($this->eCounter>$this->firstItemNum) {
				$iOut.= $this->fwd_rwd_nav('fwd');
			}

				// field header line is drawn:
			$theData = Array();
			reset($this->fieldArray);
			while(list(,$fCol)=each($this->fieldArray))	{
				$permsEdit = $this->calcPerms&($table=="pages"?2:16);
				if ($fCol=='_CONTROL_') {

					if (!$TCA[$table]['ctrl']['readOnly'])	{


						if ($permsEdit && $this->table && is_array($currentIdList) && in_array('editRec',$shEl))	{
							$editIdList = implode(',',$currentIdList);
							$params='&edit['.$table.']['.$editIdList.']=edit&columnsOnly='.implode(',',$this->fieldArray).'&disHelp=1';
							$theData[$fCol].='<a href="#" onClick="'.t3lib_BEfunc::editOnClick($params,$BACK_PATH,-1,1).'"><img src="'.$this->backPath.'gfx/edit2.gif" width="11" height="12" vspace="2" border="0" align="top" title="'.$GLOBALS['LANG']->getLL('editShownColumns').'" /></a>';
						}
					}
				} else {
					$theData[$fCol]='';
#					$theData[$fCol].='&nbsp;';
					if ($this->table && is_array($currentIdList) && in_array('editRec',$shEl))	{
						if (!$TCA[$table]['ctrl']['readOnly'] && $permsEdit && $TCA[$table]['columns'][$fCol])	{
							$editIdList = implode(',',$currentIdList);
							$params='&edit['.$table.']['.$editIdList.']=edit&columnsOnly='.$fCol.'&disHelp=1';
							$theData[$fCol].='<a href="#" onClick="'.t3lib_BEfunc::editOnClick($params,$BACK_PATH,-1,1).'"><img src="'.$this->backPath.'gfx/edit2.gif" width="11" height="12" vspace="2" border="0" align="top" title="'.sprintf($GLOBALS['LANG']->getLL('editThisColumn'),ereg_replace(":$",'',trim($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($table,$fCol))))).'" /></a>';
						}
					} else {
//						$theData[$fCol].='&nbsp;';
					}
					$theData[$fCol].=$this->addSortLink($GLOBALS['LANG']->sL(t3lib_BEfunc::getItemLabel($table,$fCol,'&nbsp;<i>[|]</i>&nbsp;')),$fCol,$table);;
				}
			}
			$out.=$this->addelement('',$theData,' bgcolor="'.$this->subHeadLineCol.'"');
				// finish
			$out.=$iOut;
			$out.='</table>';

		}
		return $out;
	}



	/********************************
	 *
	 * output
	 *
	 ********************************/

	/**
	 * 
	 * @param	[type]		$code: ...
	 * @param	[type]		$field: ...
	 * @param	[type]		$table: ...
	 * @return	[type]		...
	 */
	function addSortLink($code,$field,$table)	{
		if ($field=='_CONTROL_')	return $code;
#		if ($this->disableSingleTableView)	return $code;
		return '<a href="'.$this->listURL(-1,'sortField,sortRev,table').'&table='.$table.'&sortField='.$field.'&sortRev='.($this->sortRev || ($this->sortField!=$field)?0:1).'">'.$code.
		($this->sortField==$field?'<img src="'.$this->backPath.'gfx/red'.($this->sortRev?"up":"down").'.gif" hspace="2" width="7" height="4" border="0">':'').
		'</a>';
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function makeControl($table,$row)	{
		global $TCA, $LANG, $BACK_PATH;
#		if ($this->dontShowClipControlPanels)	return '';

		$shEl = $this->showElements;
		t3lib_div::loadTCA($table);
		$cells=array();

		if ($table=='pages')	{
			$localCalcPerms = $GLOBALS['BE_USER']->calcPerms(t3lib_BEfunc::getRecord('pages',$row['uid']));
		}
		$permsEdit = ($table=="pages" && ($localCalcPerms&2)) || ($table!="pages" && ($this->calcPerms&16));
		
		

		
			// Edit: ( Only if permissions to edit the page-record of the content of the parent page ($this->id)
		if ($permsEdit && in_array('editRec',$shEl))	{
			$params='&edit['.$table.']['.$row['uid'].']=edit';
			$cells[]='<a href="#" onClick="'.t3lib_BEfunc::editOnClick($params,$BACK_PATH,-1).'"><img src="'.$this->backPath.'gfx/edit2'.(!$TCA[$table]['ctrl']['readOnly']?"":"_d").'.gif" width="11" height="12" border="0" align="top" title="'.$LANG->getLL('edit').'" /></a>';
		}
		

		
		if ($GLOBALS['SOBE']->MOD_SETTINGS['bigControlPanel'] || $this->table)	{

					// Info: (All records)
			if (in_array('infoRec',$shEl)) {
				$cells[]='<a href="#" onClick="top.launchView(\''.$table.'\', \''.$row['uid'].'\'); return false;"><img src="'.$this->backPath.'gfx/zoom2.gif" width="12" height="12" border="0" align="top" title="'.$LANG->getLL('showInfo').'" /></a>';
			}

			if (!$TCA[$table]['ctrl']['readOnly'])	{

					// Revert
				if (in_array('revertRec',$shEl))	{
					$cells[]='<a href="#" onClick="return jumpExt(\''.$this->backPath.'show_rechis.php?element='.rawurlencode($table.':'.$row['uid']).'\',\'#latest\');"><img src="'.$this->backPath.'gfx/history2.gif"  width="13" height="12" border="0" title="'.$GLOBALS['LANG']->getLL('history').'" align="top" /></a>';
				}
					// Perms
				if ($table=="pages" && in_array('permsRec',$shEl) && $GLOBALS['BE_USER']->check('modules','web_perm'))	{
					$cells[]='<a href="mod/web/perm/index.php?id='.$row['uid'].'&return_id='.$row['uid'].'&edit=1"><img src="'.$this->backPath.'gfx/perm.gif" width="7" hspace="2" height="12" border="0" title="'.$GLOBALS['LANG']->getLL('permissions').'" align="top" /></a>';
				}
	


					// Up/Down
				if ($permsEdit && $TCA[$table]['ctrl']['sortby']  && !$this->sortField  && in_array('sortRec',$shEl))	{	//
					if (isset($this->currentTable['prev'][$row['uid']]))	{	// Up
						$params='&cmd['.$table.']['.$row['uid'].'][move]='.$this->currentTable['prev'][$row['uid']];
						$cells[]='<a href="#" onClick="return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');"><img src="'.$this->backPath.'gfx/button_up.gif" width="11" height="10" border="0" title="'.$GLOBALS['LANG']->getLL('moveUp').'" align="top" /></a>';
					} else {
						$cells[]='<img src="clear.gif" width="11" height="10" align="top">';
					}
					if ($this->currentTable['next'][$row['uid']])	{	// Down
						$params='&cmd['.$table.']['.$row['uid'].'][move]='.$this->currentTable['next'][$row['uid']];
						$cells[]='<a href="#" onClick="return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');"><img src="'.$this->backPath.'gfx/button_down.gif" width="11" height="10" border="0" title="'.$GLOBALS['LANG']->getLL('moveDown').'" align="top" /></a>';
					} else {
						$cells[]='<img src="clear.gif" width="11" height="10" align="top">';
					}
				}
		
					// Hide
				$hiddenField = $TCA[$table]['ctrl']['enablecolumns']['disabled'];
				if ($permsEdit && $hiddenField && $TCA[$table]['columns'][$hiddenField] && in_array('unHideRec',$shEl) && (!$TCA[$table]['columns'][$hiddenField]['exclude'] || $GLOBALS['BE_USER']->check('non_exclude_fields',$table.':'.$hiddenField)))	{
					if ($row[$hiddenField])	{
						$params='&data['.$table.']['.$row['uid'].']['.$hiddenField.']=0';
						$cells[]='<a href="#" onClick="return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');"><img src="'.$this->backPath.'gfx/button_unhide.gif" width="11" height="10" border="0" title="'.$GLOBALS['LANG']->getLL('unHide'.($table=="pages"?"Page":"")).'" align="top" /></a>';
					} else {
						$params='&data['.$table.']['.$row['uid'].']['.$hiddenField.']=1';
						$cells[]='<a href="#" onClick="return jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');"><img src="'.$this->backPath.'gfx/button_hide.gif" width="11" height="10" border="0" title="'.$GLOBALS['LANG']->getLL('hide'.($table=="pages"?"Page":"")).'" align="top" /></a>';
					}
				}
		
					// Delete
#				if (
#					($table=="pages" && ($localCalcPerms&4)) || ($table!="pages" && ($this->calcPerms&16)) && in_array('delRec',$shEl)
#					)	{
#					$params='&cmd['.$table.']['.$row['uid'].'][delete]=1';
#					$cells[]='<a href="#" onClick="if (confirm(unescape(\''.rawurlencode($LANG->getLL('deleteWarning')).'\'))) {jumpToUrl(\''.$GLOBALS['SOBE']->doc->issueCommand($params,-1).'\');} return false;"><img src="'.$this->backPath.'gfx/garbage.gif" width="11" height="12" border="0" align="top" title="'.$LANG->getLL('delete').'" /></a>';
#				}
		

			}
		}
		
		if ($lockInfo=t3lib_BEfunc::isRecordLocked($table,$row['uid']))	{
//			debug($lockInfo);
			$cells[]='<a href="#" onClick="alert(unescape(\''.rawurlencode($lockInfo['msg']).'\'));return false;"><img src="gfx/recordlock_warning3.gif" width="17" height="12" border="0" title="'.$lockInfo['msg'].'" /></a>';
		}
		
		return '<table border="0" cellpadding=1 cellspacing="0" bgColor="'.$GLOBALS['SOBE']->doc->bgColor4.'"><tr><td>'.implode('</td><td>',$cells).'</td></tr></table>';
	}



	/**
	 * Returns a table-row with the content from the fields in the input data array.
	 * OBS: $this->fieldArray MUST be set! (represents the list of fields to display)
	 * 
	 * @param	integer		$h is an integer >=0 and denotes how tall a element is. Set to '0' makes a halv line, -1 = full line, set to 1 makes a 'join' and above makes 'line'
	 * @param	string		$icon is the <img>+<a> of the record. If not supplied the first 'join'-icon will be a 'line' instead
	 * @param	array		$data is the dataarray, record with the fields. Notice: These fields are (currently) NOT htmlspecialchar'ed before being wrapped in <td>-tags
	 * @param	string		$tdParams is insert in the <td>-tags. Must carry a ' ' as first character
	 * @param	integer		OBSOLETE - NOT USED ANYMORE. $lMargin is the leftMargin (integer)
	 * @param	string		$altLine is the HTML <img>-tag for an alternative 'gfx/ol/line.gif'-icon (used in the top)
	 * @return	string		HTML content for the table row
	 */
	function addElement($icon,$data,$tdParams='',$action='')	{
		$noWrap = ($this->no_noWrap) ? '' : ' nowrap="nowrap"';

			// Start up:		
		$out='
		<tr>';

		
			// Show icon and lines
		if ($this->showAction=true)	{
			$out.='
			<td valign="middle" align="left" nowrap="nowrap"'.$tdParams.' style="padding-left:5px;">';
			if ($action)	$out.= $action;
			$out.='</td>
			';
		}		
		
			// Show icon and lines
		if ($this->showIcon)	{
			$out.='
			<td valign="top" align="left" nowrap="nowrap"'.$tdParams.' style="padding-left:5px;">';
			if ($icon)	$out.= $icon;
			$out.='</td>
			';
		}

			// Init rendering.		
		$colsp='';
		$lastKey='';
		$c=0;
		$ccount=0;
		$tdP[0]= $this->oddColumnsTDParams ? $this->oddColumnsTDParams : $tdParams;
		$tdP[1]=$tdParams;

			// Traverse field array which contains the data to present:
		reset($this->fieldArray);
		while(list(,$vKey)=each($this->fieldArray))	{
			if (isset($data[$vKey]))	{
				if ($lastKey)	{	
					$out.='
						<td valign="top"'.
						$noWrap.
						$tdP[($ccount%2)].
						$colsp.
						$this->element_tdParams[$lastKey].
						'>'.$data[$lastKey].'</td>';	
				}
				$lastKey=$vKey;
				$c=1;
				$ccount++;
			} else {
				if (!$lastKey) {$lastKey=$vKey;}
				$c++;
			}
			if ($c>1)	{$colsp=' colspan="'.$c.'"';} else {$colsp='';}
		}
		if ($lastKey)	{	$out.='<td valign="top"'.$noWrap.$tdP[($ccount%2)].$colsp.$this->element_tdParams[$lastKey].'>'.$data[$lastKey].'</td>';	}

			// End row
		$out.='
		</tr>';

			// Return row.
		return $out;
	}
	

	
	/**
	 * Creates a forward/reverse button based on the status of ->eCounter, ->firstItemNum, ->resultsPerPage
	 * 
	 * @param	string		Table name
	 * @return	array		array([boolean], [HTML]) where [boolean] is 1 for reverse element, [HTML] is the table-row code for the element
	 */
	function fwd_rwd_nav($type)	{

		$code='';
		$theData = Array();
		$titleCol=$this->fieldArray[0];

		if ($type=='fwd')	{
			if($this->lastItemNum<$this->resCountAll) {
				$theData[$titleCol] = $this->fwd_rwd_HTML('fwd');
			}
		} elseif($this->pointer) {
			$theData[$titleCol] = $this->fwd_rwd_HTML('rwd');
		}
		$code=$this->addElement('',$theData);
		return $code;
	}

	/********************************
	 *
	 * GUI
	 *
	 ********************************/

	/**
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$formFields: ...
	 * @return	[type]		...
	 */
	function fieldSelectBox($table='',$formFields=1)	{
		global $TCA;
		
		$table = $table ? $table : $this->table;
		t3lib_div::loadTCA($table);
		$formElements=array('','');
		if ($formFields)	{
			$formElements=array('<form action="'.htmlspecialchars($this->listURL()).'" method="post">','</form>');
		}

		$setFields=is_array($this->setFields[$table]) ? $this->setFields[$table] : array();
			// Make level selector:
		$fields = $this->makeFieldList($table);
		$opt=array();
		reset($fields);
		$opt[] = '<option value=""></option>';
		while(list(,$fN)=each($fields))	{
			$fL = is_array($TCA[$table]['columns'][$fN]) ? ereg_replace(":$",'',$GLOBALS['LANG']->sL($TCA[$table]['columns'][$fN]['label'])) : "[".$fN.']';
			$opt[] = '<option value="'.$fN.'"'.(in_array($fN,$setFields)?" selected":"").'>'.htmlspecialchars($fL).'</option>';
		}
		$lMenu = '<select size='.t3lib_div::intInRange(count($fields)+1,3,7).' multiple name="displayFields['.$table.'][]">'.implode('',$opt).'</select>';
		
			// Table with the search box:
		$content.= '<br />
		<table border="0" cellpadding="1" cellspacing="0"">
		'.$formElements[0].'
			<tr>
				<td bgcolor="#9BA1A8">
					<table border="0" cellpadding="0" cellspacing="0" bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor4.'">
					<tr>
						<td>'.$lMenu.'</td>
						<td><input type="Submit" name="search" value="&gt;&gt;"></td>
					</tr>
					</table>			
				</td>
			</tr>'.$formElements[1].'
		</table>
		';
		return $content;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @return	[type]		...
	 */
	function makeSearchString($table)	{
		global $TCA;
		if ($TCA[$table] && $this->searchString)	{
			t3lib_div::loadTCA($table);
			$columns = $TCA[$table]['columns'];
			reset($columns);
			while(list($fieldName,$info)=each($columns))	{
				$type = $info['config']['type'];
				if ($type=="text" || ($type=="input" && !ereg('date|time|int',$info['config']['eval'])))	{
					$sfields[]=$fieldName;
				}
			}
			$like=" LIKE '%".$GLOBALS['TYPO3_DB']->quoteStr($this->searchString,$table)."%'";
			if (count($sfields))	{
				$queryPart = ' AND ('.implode($like." OR ",$sfields).$like.')';
				return $queryPart;
			}
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$formFields: ...
	 * @return	[type]		...
	 */
	function getSearchBox($formFields=1)	{
		if ($GLOBALS['CLIENT']['BROWSER']=='net')	{
//			$content.= '<img src=clear.gif width=1 height=100><br />';
		}
		$formElements=array('','');
		if ($formFields)	{
			$formElements=array('<form action="'.$this->listURL().'" method="POST">','</form>');
		}


			// Table with the search box:
		$content.= '
		<table border="0" cellpadding=1 cellspacing="0">
		'.$formElements[0].'
			<tr>
				<td><img src=clear.gif width='.$this->spaceSearchBoxFromLeft.' height=1></td>
				<td bgcolor="#9BA1A8">
					<table border="0" cellpadding="0" cellspacing="0" bgcolor="'.$GLOBALS['TBE_TEMPLATE']->bgColor4.'">
					<tr>
						<td nowrap>&nbsp;'.$GLOBALS['LANG']->php3Lang['labels']['enterSearchString'].'&nbsp;&nbsp;'.'<input type="Text" name="search_field" value="'.htmlspecialchars($this->searchString).'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(10).'></td>
						<td>'.$lMenu.'</td>
						<td><input type="Submit" name="search" value="'.$GLOBALS['LANG']->php3Lang['labels']['search'].'"></td>
					</tr>
					</table>			
				</td>
			</tr>'.$formElements[1].'
		</table>
		';
		return $content;
	}

	/**
	 * Creates the button with link to either forward or reverse
	 * 
	 * @param	string		Type: "fwd" or "rwd"
	 * @param	string		Table name
	 * @return	string		
	 * @access private
	 */
	function fwd_rwd_HTML($type)	{
		$tParam = $this->table ? '&table='.rawurlencode($this->table) : '';
		switch($type)	{
			case 'rwd':
				$pointer=max(0,$this->pointer-1);
				$href = $this->listURL().'&SET[tx_dam_resultPointer]='.$pointer.$tParam;
				return '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						'<img src="'.$this->backPath.'gfx/pilup.gif" width="14" height="14" valign="top" border="0" alt="" />'.
						'</a> <i>['.max(1,$this->firstItemNum-$this->resultsPerPage).' - '.($this->firstItemNum-1).']</i>';
			break;
			case 'fwd':
				$pointer=max(0,$this->pointer+1);
				$href = $this->listURL().'&SET[tx_dam_resultPointer]='.$pointer.$tParam;
				return '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						'<img src="'.$this->backPath.'gfx/pildown.gif" width="14" height="14" valign="top" border="0" alt="" />'.
						'</a> <i>['.($this->lastItemNum+1).' - '.min($this->lastItemNum+1+$this->resultsPerPage,$this->resCountAll).']</i>';
			break;
		}
	}




	/********************************
	 *
	 * internal
	 *
	 ********************************/

	/**
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$dontCheckUser: ...
	 * @return	[type]		...
	 */
	function makeFieldList($table,$dontCheckUser=0)	{
		global $TCA,$BE_USER;
		$fieldListArr = array();
		if (is_array($TCA[$table]))	{
			t3lib_div::loadTCA($table);
			reset($TCA[$table]['columns']);
			while(list($fN,$fieldValue)=each($TCA[$table]['columns']))	{
				if ($dontCheckUser || 
					((!$fieldValue['exclude'] || $BE_USER->check('non_exclude_fields',$table.':'.$fN)) && $fieldValue['config']['type']!='passthrough'))	{
					$fieldListArr[]=$fN;
				}
			}
			if ($dontCheckUser || $BE_USER->isAdmin())	{
				$fieldListArr[]='uid';
				$fieldListArr[]='pid';
				if ($TCA[$table]['ctrl']['tstamp'])	$fieldListArr[]=$TCA[$table]['ctrl']['tstamp'];
				if ($TCA[$table]['ctrl']['crdate'])	$fieldListArr[]=$TCA[$table]['ctrl']['crdate'];
				if ($TCA[$table]['ctrl']['cruser_id'])	$fieldListArr[]=$TCA[$table]['ctrl']['cruser_id'];
				if ($TCA[$table]['ctrl']['sortby'])	$fieldListArr[]=$TCA[$table]['ctrl']['sortby'];
			}
		}
		return $fieldListArr;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$pid: ...
	 * @return	[type]		...
	 */
	function recPath($pid)	{
		if (!isset($this->recPath_cache[$pid]))	{
			$this->recPath_cache[$pid] = t3lib_BEfunc::getRecordPath ($pid,$this->perms_clause,20);
		}
		return $this->recPath_cache[$pid];
	}


	/********************************
	 *
	 * tools
	 *
	 ********************************/
	 
	 
	/**
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$code: ...
	 * @return	[type]		...
	 */
	function linkWrapTable($table,$code)	{
			// Returns the title (based on $code) of a table ($table) with the proper link around
		if ($this->table!=$table)	{
			return '<a href="'.$this->listURL($table).'">'.$code.'</a>';
		} else {
			return '<a href="'.$this->listURL('','sortField,sortRev,table').'">'.$code.'</a>';
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$code: ...
	 * @param	[type]		$row: ...
	 * @return	[type]		...
	 */
	function linkWrapItems($table,$uid,$code,$row)	{
			// Returns the title (based on $code) of a record (from table $table) with the proper link around (that is for "pages"-records a link to the level of that record...)
		if (!strcmp($code,'')) {$code='<i>['.$GLOBALS['LANG']->php3Lang['labels']['no_title'].']</i> - '.t3lib_BEfunc::getRecordTitle($table,$row);}
		$code=t3lib_div::fixed_lgd_cs('&nbsp;'.$code,$this->fixedL);
		if ($table=='pages')	{
			return '<a href="'.$this->listURL($uid).'">'.$code.'</a>';
		} else {
			return $code;
//			return '<a href="javascript:top.launchView(\''.$table.'\','.$uid.');">'.$code.'</a>';	// This launches the show_item-windows
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$exclList: ...
	 * @return	[type]		...
	 */
	function listURL($table=-1,$exclList='')	{
		return $this->script.'?'.
			($this->staticParams?$this->staticParams:'').
			($this->returnUrl?"&returnUrl=".rawurlencode($this->returnUrl):"").
			($this->searchString?"&search_field=".rawurlencode($this->searchString):'').
			((!$exclList || !t3lib_div::inList($exclList,'sortField')) && $this->sortField?"&sortField=".rawurlencode($this->sortField):"").
			((!$exclList || !t3lib_div::inList($exclList,'sortRev')) && $this->sortRev?"&sortRev=".rawurlencode($this->sortRev):"")
			;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$row: ...
	 * @param	[type]		$table: ...
	 * @param	[type]		$field: ...
	 * @return	[type]		...
	 */
	function thumbCode($row,$table,$field)	{
		return t3lib_BEfunc::thumbCode($row,$table,$field,$this->backPath,$this->thumbScript);
	}


}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_db_list.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_list_list/class.tx_dam_db_list.php']);
}


?>