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
 *   61: class tx_dam_simpleForms extends t3lib_TCEforms 
 *   68:     function initDefaultBEmode()	
 *   88:     function setNewBEDesign()	
 *  141:     function addUserTemplateMarkers($marker,$table,$field,$row,&$PA)	
 *  163:     function wrapItem ($content) 
 *  173:     function removeRequired(&$tca) 
 *  185:     function setNonEditable(&$tca) 
 *  198:     function removeMM(&$tca) 
 *  206:     function setTSconfig($table,$row,$field='')	
 *  228:     function getTSCpid($table,$uid,$pid)	
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once (PATH_t3lib.'class.t3lib_tceforms.php');

/**
 * Modified TCEforms for usage in simple forms for data input and NOT record editing.
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_simpleForms extends t3lib_TCEforms {

	var $tx_dam_fixedFields = array();

	var $savedGroupData = '';

	function setVirtualTable($virtual, $existant) {
		global $BE_USER, $TCA;

			// fake table - to be safe
		t3lib_div::loadTCA($existant);
		$TCA[$virtual] = $TCA[$existant];

		$this->savedGroupData = $BE_USER->groupData;

		$checkFields = array('explicit_allowdeny', 'tables_select', 'tables_modify', 'non_exclude_fields');
		foreach ($checkFields as $key) {
			$addList = '';
			$checkList = t3lib_div::trimExplode(',', $BE_USER->groupData[$key], 1);
			foreach ($checkList as $val) {
				list($table,$field) = explode(':', $val, 2);

				if($val==$existant) {
					$addList.= ','.$virtual;
				} elseif($table==$existant AND $field) {
					$addList.= ','.$virtual.':'.$field;
				}
			}
			$BE_USER->groupData[$key] .= $addList;
		}

	}


	function removeVirtualTable($virtual) {
		global $BE_USER, $TCA;

		$BE_USER->groupData = $this->savedGroupData;
		unset($TCA[$virtual]);
	}


	/**
	 * Initialize various internal variables.
	 * 
	 * @return	void		
	 */
	function initDefaultBEmode()	{
		global $BACK_PATH;

		parent::initDefaultBEmode();

		$this->backPath = $BACK_PATH;
		#$this->doSaveFieldName='update';
		$this->palettesCollapsed = 0;
		$this->disableRTE = 1;
		$this->edit_showFieldHelp='text';
		$this->globalShowHelp = 1;
		$this->hiddenFieldList = '';
		$this->edit_docModuleUpload = FALSE;
	}

#TODO $setOrigDesign used???
	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function setNewBEDesign($enableCheckboxes=true)	{
		global $BACK_PATH;

		parent::setNewBEDesign();

		$this->totalWrap='<table border="0" cellpadding="0" cellspacing="0" width="50%">|</table>';


		if ($enableCheckboxes) {
			$_FIELD_SETFIXED = '###FIELD_SETFIXED###';
			$_BGCOLOR_HEAD = ' ###BGCOLOR_HEAD###';
		}

		$this->fieldTemplate = '
			<tr ###CLASSATTR_2###>
				<td>###FIELD_HELP_ICON###</td>
				<td width="99%"><span style="color:###FONTCOLOR_HEAD###;"###CLASSATTR_4###><b>###FIELD_NAME###</b></span>###FIELD_HELP_TEXT###</td>
			</tr>
			<tr ###CLASSATTR_1###>
				<td nowrap="nowrap" valign="middle"'.$_BGCOLOR_HEAD.'>'.
#				'<img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="'.$BACK_PATH.'clear.gif" width="10" height="10" alt="" />'.
				$_FIELD_SETFIXED.
				'<img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="'.$BACK_PATH.'clear.gif" width="7" height="10" alt="" /></td>
				<td valign="top">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td>
			</tr>
			<tr>
				<td colspan="2"><img src="'.$BACK_PATH.'clear.gif" width="1" height="4" alt="" /></td>
			</tr>';


		$this->palFieldTemplate = '
			<tr ###CLASSATTR_1###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top">###FIELD_PALETTE###</td>
			</tr>';

		$this->palFieldTemplateHeader = '
			<tr ###CLASSATTR_2###>
				<td>&nbsp;</td>
				<td nowrap="nowrap" valign="top"><strong>###FIELD_HEADER###</strong></td>
			</tr>';

		$this->sectionWrap = '
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="###SPACE_BEFORE###" alt="" /></td>
			</tr>
			<tr>
				<td colspan="2"><table ###TABLE_ATTRIBS###>###CONTENT###</table></td>
			</tr>
			';


	}


	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function setNewBEDesignOrig()	{
		parent::setNewBEDesign();
	}


	/**
	 * add own markers for output
	 * 
	 * @param	array		Array with key/value pairs to insert in the template.
	 * @param	[type]		$table: ...
	 * @param	[type]		$field: ...
	 * @param	[type]		$row: ...
	 * @param	[type]		$PA: ...
	 * @return	[type]		...
	 * @see function intoTemplate()
	 */
	function addUserTemplateMarkers($marker,$table,$field,$row,&$PA)	{
		global $BACK_PATH;

		if($PA['fieldConf']['config']['type']=='none') {
			if(in_array($field,$this->tx_dam_fixedFields)) {
				$marker['SETFIXED']='<img src="'.$BACK_PATH.'gfx/pil2right.gif" width="7" height="12" vspace="2" alt="" />';
			}
		} else {
			$itemFormElName=$this->prependFormFieldNames.'_fixedFields['.$table.']['.$row['uid'].']['.$field.']';		// Form field name

			$marker['SETFIXED']='<input type="hidden" name="'.$itemFormElName.'" value="0" />'.
								'<input type="checkbox" name="'.$itemFormElName.'"'.(in_array($field,$this->tx_dam_fixedFields)?' checked':'').' value="1" />';
		}
		return $marker;
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$content: ...
	 * @return	[type]		...
	 */
	function wrapItem ($content) {
		return str_replace('###FIELD_PALETTE###',$content,$this->palFieldTemplate);
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$$tca: ...
	 * @return	[type]		...
	 */
	function removeRequired(&$tca) {
		foreach($tca['columns'] as $field => $config) {
			$tca['columns'][$field]['config']['eval'] = str_replace('required','',$tca['columns'][$field]['config']['eval']);
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$$tca: ...
	 * @return	[type]		...
	 */
	function setNonEditable(&$tca, $columnsExclude='') {

		$columnsExclude = t3lib_div::trimExplode(',', $columnsExclude, 1);
		foreach($tca['columns'] as $field => $config) {
			if(!in_array($field, $columnsExclude)) {
				$tca['columns'][$field]['config']['type'] = 'none';
				$tca['columns'][$field]['config']['size'] = max(5,$tca['columns'][$field]['config']['size']);
			}
		}
	}
	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$$tca: ...
	 * @return	[type]		...
	 */
	function setNoneToEditable(&$tca) {
		foreach($tca['columns'] as $field => $config) {
			if($tca['columns'][$field]['config']['type'] == 'none') {
				$tca['columns'][$field]['config']['type'] = 'input';
			}
		}
	}
	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$$tca: ...
	 * @return	[type]		...
	 */
	function removeMM(&$tca) {
		foreach($tca['columns'] as $field => $config) {
			unset($tca['columns'][$field]['config']['MM']);
		}
	}

	// ------------------------------------------------------------------

	function setTSconfig($table,$row,$field='')	{

		$mainKey = $table.':'.$row['uid'];
		if (!isset($this->cachedTSconfig[$mainKey]))	{
# this tries to read the record again, which fails when using pseudo records
#			$this->cachedTSconfig[$mainKey]=t3lib_BEfunc::getTCEFORM_TSconfig($table,$row);
		}
		if ($field)	{
			return $this->cachedTSconfig[$mainKey][$field];
		} else {
			return $this->cachedTSconfig[$mainKey];
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$table: ...
	 * @param	[type]		$uid: ...
	 * @param	[type]		$pid: ...
	 * @return	[type]		...
	 */
	function getTSCpid($table,$uid,$pid)	{
		$key = $table.':'.$uid.':'.$pid;
		if (!isset($this->cache_getTSCpid[$key]))	{
# this tries to read the record again, which fails when using pseudo records
#			$this->cache_getTSCpid[$key] = t3lib_BEfunc::getTSCpid($table,$uid,$pid);
		}
		return $this->cache_getTSCpid[$key];
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_simpleforms.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_simpleforms.php']);
}
?>