<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  (c) 2005-2006 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
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
 * Displays the page/file tree for browsing database records or files.
 * Used from TCEFORMS an other elements
 * In other words: This is the ELEMENT BROWSER!
 *
 * Adapted for htmlArea RTE by Stanislas Rolland
 *
 * $Id: class.tx_rtehtmlarea_browse_links.php,v 1.1 2006/05/05 20:35:09 stanrolland Exp $
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @author	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 */


require_once(t3lib_extMgm::extPath('rtehtmlarea').'mod3/class.tx_rtehtmlarea_browse_links.php');



/**
 * Script class for the Element Browser window.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 */
class ux_tx_rtehtmlarea_browse_links extends tx_rtehtmlarea_browse_links {


	/******************************************************************
	 *
	 * Main functions
	 *
	 ******************************************************************/
	/**
	 * Rich Text Editor (RTE) link selector (MAIN function)
	 * Generates the link selector for the Rich Text Editor.
	 * Can also be used to select links for the TCEforms (see $wiz)
	 *
	 * @param	boolean		If set, the "remove link" is not shown in the menu: Used for the "Select link" wizard which is used by the TCEforms
	 * @return	string		Modified content variable.
	 */
	function main_rte($wiz=0)	{
		global $LANG, $BE_USER, $BACK_PATH;

			// Starting content:
		$content=$this->doc->startPage($LANG->getLL('Insert/Modify Link',1));

			// Initializing the action value, possibly removing blinded values etc:
		$allowedItems = explode(',','page,file,url,mail,spec');
		if (is_array($this->buttonConfig['options.']) && $this->buttonConfig['options.']['removeItems']) {
			$allowedItems = array_diff($allowedItems,t3lib_div::trimExplode(',',$this->buttonConfig['options.']['removeItems'],1));
		} else {
			$allowedItems = array_diff($allowedItems,t3lib_div::trimExplode(',',$this->thisConfig['blindLinkOptions'],1));
		}
		reset($allowedItems);
		if (!in_array($this->act,$allowedItems)) {
			$this->act = current($allowedItems);
		}
		
			// Making menu in top:
		$menuDef = array();
		if (!$wiz)	{
			$menuDef['removeLink']['isActive'] = $this->act=='removeLink';
			$menuDef['removeLink']['label'] = $LANG->getLL('removeLink',1);
			$menuDef['removeLink']['url'] = '#';
			$menuDef['removeLink']['addParams'] = 'onclick="editor.renderPopup_unLink();return false;"';
		}
		if (in_array('page',$allowedItems)) {
			$menuDef['page']['isActive'] = $this->act=='page';
			$menuDef['page']['label'] = $LANG->getLL('page',1);
			$menuDef['page']['url'] = '#';
			$menuDef['page']['addParams'] = 'onclick="jumpToUrl(\'?act=page&editorNo='.$this->editorNo.'&contentTypo3Language='.$this->contentTypo3Language.'&contentTypo3Charset='.$this->contentTypo3Charset.'\');return false;"';
		}
		if (in_array('file',$allowedItems)){
			$menuDef['file']['isActive'] = $this->act=='file';
			$menuDef['file']['label'] = $LANG->getLL('file',1);
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onclick="jumpToUrl(\'?act=file&editorNo='.$this->editorNo.'&contentTypo3Language='.$this->contentTypo3Language.'&contentTypo3Charset='.$this->contentTypo3Charset.'\');return false;"';
		}
		if (in_array('url',$allowedItems)) {
			$menuDef['url']['isActive'] = $this->act=='url';
			$menuDef['url']['label'] = $LANG->getLL('extUrl',1);
			$menuDef['url']['url'] = '#';
			$menuDef['url']['addParams'] = 'onclick="jumpToUrl(\'?act=url&editorNo='.$this->editorNo.'&contentTypo3Language='.$this->contentTypo3Language.'&contentTypo3Charset='.$this->contentTypo3Charset.'\');return false;"';
		}
		if (in_array('mail',$allowedItems)) {
			$menuDef['mail']['isActive'] = $this->act=='mail';
			$menuDef['mail']['label'] = $LANG->getLL('email',1);
			$menuDef['mail']['url'] = '#';
			$menuDef['mail']['addParams'] = 'onclick="jumpToUrl(\'?act=mail&editorNo='.$this->editorNo.'&contentTypo3Language='.$this->contentTypo3Language.'&contentTypo3Charset='.$this->contentTypo3Charset.'\');return false;"';
		}
		if (is_array($this->thisConfig['userLinks.']) && in_array('spec',$allowedItems)) {
			$menuDef['spec']['isActive'] = $this->act=='spec';
			$menuDef['spec']['label'] = $LANG->getLL('special',1);
			$menuDef['spec']['url'] = '#';
			$menuDef['spec']['addParams'] = 'onclick="jumpToUrl(\'?act=spec&editorNo='.$this->editorNo.'&contentTypo3Language='.$this->contentTypo3Language.'&contentTypo3Charset='.$this->contentTypo3Charset.'\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);

			// Adding the menu and header to the top of page:
		$content.=$this->printCurrentUrl($this->curUrlInfo['info']).'<br />';
		
			// Depending on the current action we will create the actual module content for selecting a link:
		switch($this->act)	{
			case 'mail':
				$extUrl='
			<!--
				Enter mail address:
			-->
					<form action="" name="lurlform" id="lurlform">
						<table border="0" cellpadding="2" cellspacing="1" id="typo3-linkMail">
							<tr>
								<td>'.$LANG->getLL('emailAddress',1).':</td>
								<td><input type="text" name="lemail"'.$this->doc->formWidth(20).' value="'.htmlspecialchars($this->curUrlInfo['act']=='mail'?$this->curUrlInfo['info']:'').'" /> '.
									'<input type="submit" value="'.$LANG->getLL('setLink',1).'" onclick="setTarget(\'\');setValue(\'mailto:\'+document.lurlform.lemail.value); return link_current();" /></td>
							</tr>
						</table>
					</form>';
				$content.=$extUrl;
				$content.=$this->addAttributesForm();
			break;
			case 'url':
				$extUrl='
			<!--
				Enter External URL:
			-->
					<form action="" name="lurlform" id="lurlform">
						<table border="0" cellpadding="2" cellspacing="1" id="typo3-linkURL">
							<tr>
								<td>URL:</td>
								<td><input type="text" name="lurl"'.$this->doc->formWidth(20).' value="'.htmlspecialchars($this->curUrlInfo['act']=='url'?$this->curUrlInfo['info']:'http://').'" /> '.
									'<input type="submit" value="'.$LANG->getLL('setLink',1).'" onclick="setValue(document.lurlform.lurl.value); return link_current();" /></td>
							</tr>
						</table>
					</form>';
				$content.=$extUrl;
				$content.=$this->addAttributesForm();
			break;
			case 'file':
				$content.=$this->addAttributesForm();
				
				
				$browserRendered = false;
				if (t3lib_extMgm::isLoaded('dam')) {
		
					require_once(t3lib_extMgm::extPath('dam').'class.tx_dam_browse_media.php');
					$browserRenderObj = t3lib_div::makeInstance('tx_dam_browse_media');
					
					if ($browserRenderObj->isValid('part_rte_linkfile', $this)) {
						$content.=  $browserRenderObj->renderPart('rte_linkfile', $this);
						$browserRendered = true;
					}
					
				} 
				
					// if type was not rendered use default rendering functions
				if(!$browserRendered) {
					$foldertree = t3lib_div::makeInstance('tx_rtehtmlarea_folderTree');
					$tree=$foldertree->getBrowsableTree();
	
					if (!$this->curUrlInfo['value'] || $this->curUrlInfo['act']!='file')	{
						$cmpPath='';
					} elseif (substr(trim($this->curUrlInfo['info']),-1)!='/')	{
						$cmpPath=PATH_site.dirname($this->curUrlInfo['info']).'/';
						if (!isset($this->expandFolder)) $this->expandFolder = $cmpPath;
					} else {
						$cmpPath=PATH_site.$this->curUrlInfo['info'];
					}
	
					list(,,$specUid) = explode('_',$this->PM);
					$files = $this->expandFolder($foldertree->specUIDmap[$specUid]);

						// Create upload/create folder forms, if a path is given:
					if ($BE_USER->getTSConfigVal('options.uploadFieldsInTopOfEB')) {
						$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
						$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
						$path=$this->expandFolder;
						if (!$path || !@is_dir($path))	{
							$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
						}
						if ($path!='/' && @is_dir($path))	{
							$uploadForm=$this->uploadForm($path);
							$createFolder=$this->createFolder($path);
						} else {
							$createFolder='';
							$uploadForm='';
						}
						$content.=$uploadForm;
						if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB')) {
							$content.=$createFolder;
						}
					}
	
					$content.= '
			<!--
			Wrapper table for folder tree / file list:
			-->
					<table border="0" cellpadding="0" cellspacing="0" id="typo3-linkFiles">
						<tr>
							<td class="c-wCell" valign="top">'.$this->barheader($LANG->getLL('folderTree').':').$tree.'</td>
							<td class="c-wCell" valign="top">'.$files.'</td>
						</tr>
					</table>
					';
				}
			break;
			case 'spec':
				if (is_array($this->thisConfig['userLinks.']))	{
					$subcats=array();
					$v=$this->thisConfig['userLinks.'];
					reset($v);
					while(list($k2)=each($v))	{
						$k2i = intval($k2);
						if (substr($k2,-1)=='.' && is_array($v[$k2i.'.']))	{

								// Title:
							$title = trim($v[$k2i]);
							if (!$title)	{
								$title=$v[$k2i.'.']['url'];
							} else {
								$title=$LANG->sL($title);
							}
								// Description:
							$description=$v[$k2i.'.']['description'] ? $LANG->sL($v[$k2i.'.']['description'],1).'<br />' : '';

								// URL + onclick event:
							$onClickEvent='';
							if (isset($v[$k2i.'.']['target']))	$onClickEvent.="setTarget('".$v[$k2i.'.']['target']."');";
							$v[$k2i.'.']['url'] = str_replace('###_URL###',$this->siteURL,$v[$k2i.'.']['url']);
							if (substr($v[$k2i.'.']['url'],0,7)=="http://" || substr($v[$k2i.'.']['url'],0,7)=='mailto:')	{
								$onClickEvent.="cur_href=unescape('".rawurlencode($v[$k2i.'.']['url'])."');link_current();";
							} else {
								$onClickEvent.="link_spec(unescape('".$this->siteURL.rawurlencode($v[$k2i.'.']['url'])."'));";
							}

								// Link:
							$A=array('<a href="#" onclick="'.htmlspecialchars($onClickEvent).'return false;">','</a>');

								// Adding link to menu of user defined links:
							$subcats[$k2i]='
								<tr>
									<td class="bgColor4">'.$A[0].'<strong>'.htmlspecialchars($title).($this->curUrlInfo['info']==$v[$k2i.'.']['url']?'<img'.t3lib_iconWorks::skinImg($BACK_PATH,'gfx/blinkarrow_right.gif','width="5" height="9"').' class="c-blinkArrowR" alt="" />':'').'</strong><br />'.$description.$A[1].'</td>
								</tr>';
						}
					}

						// Sort by keys:
					ksort($subcats);

						// Add menu to content:
					$content.= '
			<!--
				Special userdefined menu:
			-->
						<table border="0" cellpadding="1" cellspacing="1" id="typo3-linkSpecial">
							<tr>
								<td class="bgColor5" class="c-wCell" valign="top"><strong>'.$LANG->getLL('special',1).'</strong></td>
							</tr>
							'.implode('',$subcats).'
						</table>
						';
				}
			break;
			case 'page':
			default:
				$content.=$this->addAttributesForm();
				
				$pagetree = t3lib_div::makeInstance('tx_rtehtmlarea_pageTree');
				$tree=$pagetree->getBrowsableTree();
				$cElements = $this->expandPage();
				$content.= '
			<!--
				Wrapper table for page tree / record list:
			-->
					<table border="0" cellpadding="0" cellspacing="0" id="typo3-linkPages">
						<tr>
							<td class="c-wCell" valign="top">'.$this->barheader($LANG->getLL('pageTree').':').$tree.'</td>
							<td class="c-wCell" valign="top">'.$cElements.'</td>
						</tr>
					</table>
					';
			break;
		}

			// End page, return content:
		$content.= $this->doc->endPage();
		$content = $this->doc->insertStylesAndJS($content);
		return $content;
	}
	

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_tx_rtehtmlarea_browse_links.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_tx_rtehtmlarea_browse_links.php']);
}

?>