<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 René Fritz (r.fritz@colorcube.de)
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
 * Addition of an item to the clickmenu
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 */


class tx_dam_cm1 {
	function main(&$backRef, $menuItems, $table, $uid)	{
		global $BE_USER, $TCA, $LANG;
	
			// Returns directly, because the clicked item was not from the DAM table 
		if ($table!='tx_dam')	return $menuItems;

		$this->backRef = &$backRef;
		
			// save original items
		$orgItems = $menuItems;

			// just clear the whole menu
		$menuItems = array();
		
		$root = 0;	
			

		if ($backRef->cmLevel==0)	{

				// If record found (or root), go ahead and fill the $menuItems array which will contain data for the elements to render.
			if (is_array($backRef->rec) || $root)	{
					// Get permissions
				$lCP = $BE_USER->calcPerms(t3lib_BEfunc::getRecord('pages',($table=='pages'?$backRef->rec['uid']:$backRef->rec['pid'])));
							
					// Include localllang file
				$LL = $this->includeLL();
					
					
					// Edit:
				if(isset($orgItems['edit']))	$menuItems['edit']=$orgItems['edit'];
	

				if ($backRef->editOK)	{

						// rename
					if (!in_array('tx_dam_rename_file',$backRef->disabledItems) AND !in_array('rename',$backRef->disabledItems) AND !$root AND $BE_USER->isPSet($lCP,$table,'editcontent'))	{
						$icon = $backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($backRef->PH_backPath,'gfx/rename.gif','width="12" height="12"').' alt="" />');
						$menuItems['tx_dam_rename_file']=$this->db_launch($uid, 'tx_dam_cmd_filerename', $backRef->label('rename'), $icon);
					}
					
						// replace file
					if (!in_array('tx_dam_replace_file',$backRef->disabledItems) AND !$root AND $BE_USER->isPSet($lCP,$table,'editcontent'))	{
						$icon = $backRef->excludeIcon('<img src="'.$backRef->PH_backPath.PATH_txdam_rel.'i/cm_replace_file.gif" width="15" height="12" border=0 align=top>');
						$menuItems['tx_dam_replace_file']=$this->db_launch($uid, 'tx_dam_cmd_filereplace', $LANG->getLLL('tx_dam_cm1.replaceFile',$LL), $icon);
					}
					
//					$url = PATH_txdam_rel.'mod_cmd/index.php?CMD=tx_dam_cmd_filerename&id='.$uid;
//					$menuItems['tx_dam_rename_file'] = $backRef->linkItem(
//						$backRef->label('rename'),						
//						$backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($backRef->PH_backPath,'gfx/rename.gif','width="12" height="12"').' alt="" />'),
//						$backRef->urlRefForCM($url),
//						1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
//					);
//
//					$url = PATH_txdam_rel.'mod_cmd/index.php?CMD=tx_dam_cmd_filereplace&id='.$uid;
//					$menuItems['tx_dam_replace_file'] = $backRef->linkItem(
//						$LANG->getLLL('tx_dam_cm1.replaceFile',$LL),
//						$backRef->excludeIcon('<img src="'.$backRef->PH_backPath.PATH_txdam_rel.'i/cm_replace_file.gif" width="15" height="12" border=0 align=top>'),
//						$backRef->urlRefForCM($url),
//						1
//					);
				}

					// Info:
				if(isset($orgItems['info']))	$menuItems['info']=$orgItems['info'];
	
				$menuItems['spacer1']='spacer';
				
				
					// Extra options:
				if(isset($orgItems['tx_extrapagecmoptions_spacer']))	$menuItems['tx_extrapagecmoptions_spacer']=$orgItems['tx_extrapagecmoptions_spacer'];
				if(isset($orgItems['moreoptions']))	$menuItems['moreoptions']=$orgItems['moreoptions'];
				
				
					// Hide:
				if(isset($orgItems['hide']))	$menuItems['hide']=$orgItems['hide'];
				
				
					// Edit access:
				if(isset($orgItems['edit_access']))	$menuItems['edit_access']=$orgItems['edit_access'];
				
				

				if ($backRef->editOK)	{
						// Delete:
					if(!in_array('tx_dam_delete_file',$backRef->disabledItems) AND !in_array('delete',$backRef->disabledItems) AND !$root AND $BE_USER->isPSet($lCP,$table,'delete'))	{
						$menuItems['spacer2']='spacer';
						
						#$icon = $backRef->excludeIcon('<img src="'.$backRef->PH_backPath.PATH_txdam_rel.'i/cm_replace_file.gif" width="15" height="12" border=0 align=top>');
						$icon = $backRef->excludeIcon('<img'.t3lib_iconWorks::skinImg($backRef->PH_backPath,'gfx/delete_record.gif','width="12" height="12"').' alt="" />');
						$menuItems['tx_dam_delete_file']=$this->db_launch($uid, 'tx_dam_cmd_filedelete', $backRef->label('delete'), $icon);
					}	
				}							
					
					
					
					

			}
			
		} elseif ($backRef->cmLevel==1) {
					// Extra options:
				if(isset($orgItems['history']))	$menuItems['history']=$orgItems['history'];
		}
#debug($menuItems);			
		return $menuItems;
	} 


	/**
	 * Multi-function for adding an entry to the $menuItems array
	 *
	 * @param	string		record id
	 * @param	string		Script (eg. file_edit.php) to pass &target= to
	 * @param	string		label for the element
	 * @param	string		icon image
	 * @return	array		Item array, element in $menuItems
	 * @internal
	 */
	function db_launch($id,$cmd,$label,$icon)	{
		$loc='top.content'.(!$this->backRef->alwaysContentFrame?'.list_frame':'');
		$script = PATH_txdam_rel.'mod_cmd/index.php?CMD='.$cmd;
		$editOnClick='if('.$loc.'){'.$loc.".document.location=top.TS.PATH_typo3+'".$script.'&id='.rawurlencode($id)."&returnUrl='+top.rawurlencode(".$this->backRef->frameLocation($loc.'.document').");}";

		return $this->backRef->linkItem(
			$label,
			$icon,
			$editOnClick.'return hideCM();'
		);
	}

	
	/**
	 * Includes the [extDir]/locallang.php and returns the $LOCAL_LANG array found in that file.
	 */
	function includeLL()	{
		include(PATH_txdam.'locallang_cm.php');
		return $LOCAL_LANG;
	}
} 



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dam/class.tx_dam_cm1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/dam/class.tx_dam_cm1.php"]);
}

?>