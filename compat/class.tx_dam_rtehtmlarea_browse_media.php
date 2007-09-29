<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
*  (c) 2004-2006 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
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
 * Displays image selector for the RTE
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
require_once(PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once(PATH_txdam.'class.tx_dam_browse_media.php');

/**
 * Script Class
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @see SC_browse_links
 * @package TYPO3-DAM
 * @subpackage tx_dam
 */
class tx_dam_rtehtmlarea_browse_media extends tx_dam_browse_media {
	var $extKey = 'rtehtmlarea';
	var $content;
	var $act;
	var $allowedItems;
	var $plainMaxWidth;
	var $plainMaxHeight;
	var $magicMaxWidth;
	var $magicMaxHeight;
	var $imgPath;
	var $imgTitleDAMColumn = '';
	var $classesImageJSOptions;
	var $editorNo;
	var $sys_language_content;
	var $buttonConfig = array();
	
	/**
	 * Check if this object should be rendered.
	 *
	 * @param	string		$type Type: "file", ...
	 * @param	object		$pObj Parent object.
	 * @return	boolean
	 * @see SC_browse_links::main()
	 */
	function isValid($type, &$pObj)	{
		$isValid = false;

		$pArr = explode('|', t3lib_div::_GP('bparams'));

		if ($type === 'rte' AND $pObj->button == 'image') {
			$isValid = true;
		}
		else {
			$valid = parent::isValid($type, $pObj);
		}
		
		return $isValid;
	}
	
	
	/**
	 * Initialisation
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$BACK_PATH,$TYPO3_CONF_VARS;
		
			// Main GPvars:
		$this->siteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		$this->act = t3lib_div::_GP('act');
		$this->expandPage = t3lib_div::_GP('expandPage');
		$this->expandFolder = t3lib_div::_GP('expandFolder');
		
			// Find RTE parameters
		$this->bparams = t3lib_div::_GP('bparams');
		$this->editorNo = t3lib_div::_GP('editorNo');
		$this->sys_language_content = t3lib_div::_GP('sys_language_content');
		$this->RTEtsConfigParams = t3lib_div::_GP('RTEtsConfigParams');
		if (!$this->editorNo) {
			$pArr = explode('|', $this->bparams);
			$pRteArr = explode(':', $pArr[1]);
			$this->editorNo = $pRteArr[0];
			$this->sys_language_content = $pRteArr[1];
			$this->RTEtsConfigParams = $pArr[2];
		}
		
			// Find "mode"
		$this->mode = t3lib_div::_GP('mode');
		if (!$this->mode)	{
			$this->mode='rte';
		}
		
			// Site URL
		$this->siteURL = t3lib_div::getIndpEnv('TYPO3_SITE_URL');	// Current site url
		
			// the script to link to
		$this->thisScript = t3lib_div::getIndpEnv('SCRIPT_NAME');
		
		if (!$this->act)	{
			$this->act='magic';
		}
		
		$RTEtsConfigParts = explode(':', $this->RTEtsConfigParams);
		$RTEsetup = $BE_USER->getTSConfig('RTE',t3lib_BEfunc::getPagesTSconfig($RTEtsConfigParts[5]));
		$this->thisConfig = t3lib_BEfunc::RTEsetup($RTEsetup['properties'],$RTEtsConfigParts[0],$RTEtsConfigParts[2],$RTEtsConfigParts[4]);
		$this->imgPath = $RTEtsConfigParts[6];
		
		if (is_array($this->thisConfig['buttons.']) && is_array($this->thisConfig['buttons.']['image.'])) {
			$this->buttonConfig = $this->thisConfig['buttons.']['image.'];
		}
		
		$this->allowedItems = explode(',','magic,plain,dragdrop,image,upload');
		if (is_array($this->buttonConfig['options.']) && $this->buttonConfig['options.']['removeItems']) {
			$this->allowedItems = array_diff($this->allowedItems,t3lib_div::trimExplode(',',$this->buttonConfig['options.']['removeItems'],1));
		} else {
			$this->allowedItems = array_diff($this->allowedItems,t3lib_div::trimExplode(',',$this->thisConfig['blindImageOptions'],1));
		}
		
		reset($this->allowedItems);
		if (!in_array($this->act,$this->allowedItems))	{
			$this->act = current($this->allowedItems);
		}

		if ($this->act == 'magic') {
			if (is_array($this->buttonConfig['options.']) && is_array($this->buttonConfig['options.']['magic.'])) {
				if ($this->buttonConfig['options.']['magic.']['maxWidth']) $this->magicMaxWidth = $this->buttonConfig['options.']['magic.']['maxWidth'];
				if ($this->buttonConfig['options.']['magic.']['maxHeight']) $this->magicMaxHeight = $this->buttonConfig['options.']['magic.']['maxHeight'];
			}
				// These defaults allow images to be based on their width - to a certain degree - by setting a high height. Then we're almost certain the image will be based on the width
			if (!$this->magicMaxWidth) $this->magicMaxWidth = 300;
			if (!$this->magicMaxHeight) $this->magicMaxHeight = 1000;
		} elseif ($this->act == 'plain') {
			if ($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxWidth']) $this->plainMaxWidth = $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxWidth'];
			if ($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxHeight']) $this->plainMaxHeight = $TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['plainImageMaxHeight'];
			if (is_array($this->buttonConfig['options.']) && is_array($this->buttonConfig['options.']['plain.'])) {
				if ($this->buttonConfig['options.']['plain.']['maxWidth']) $this->plainMaxWidth = $this->buttonConfig['options.']['plain.']['maxWidth'];
				if ($this->buttonConfig['options.']['plain.']['maxHeight']) $this->plainMaxHeight = $this->buttonConfig['options.']['plain.']['maxHeight'];
			}
			if (!$this->plainMaxWidth) $this->plainMaxWidth = 640;
			if (!$this->plainMaxHeight) $this->plainMaxHeight = 680;
		}
		
		if ($this->thisConfig['classesImage']) {
			$classesImageArray = t3lib_div::trimExplode(',',$this->thisConfig['classesImage'],1);
			$this->classesImageJSOptions = '<option value=""></option>';
			reset($classesImageArray);
			while(list(,$class)=each($classesImageArray)) {
				$this->classesImageJSOptions .= '<option value="' .$class . '">' . $class . '</option>';
			}
		}
		
		
			// Insert the image if we are done
		$this->imageInsert();
		
			// Creating backend template object:
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->docType= 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;
		
		$this->getJSCode();
	}

	/**
	 * Return the RTE_imageStorageDir
	 *
	 * @return	string 
	 */
	function rteImageStorageDir()	{
		$dir = $this->imgPath ? $this->imgPath : $GLOBALS['TYPO3_CONF_VARS']['BE']['RTE_imageStorageDir'];
		return $dir;
	}

	/**
	 * Insert image and perform conversion
	 *
	 * @return	void
	 */
	function imageInsert()	{
		global $TCA,$TYPO3_CONF_VARS;
		
		if (t3lib_div::_GP('insertImage')) {
			$filepath = t3lib_div::_GP('insertImage');
			
			$imgObj = t3lib_div::makeInstance('t3lib_stdGraphic');
			$imgObj->init();
			$imgObj->mayScaleUp=0;
			$imgObj->tempPath=PATH_site.$imgObj->tempPath;
			$imgInfo = $imgObj->getImageDimensions($filepath);
			
			t3lib_div::loadTCA('tx_dam');
			if (is_array($this->buttonConfig['title.']) && is_array($TCA['tx_dam']['columns'][$this->buttonConfig['title.']['useDAMColumn']])) {
				$this->imgTitleDAMColumn = $this->buttonConfig['title.']['useDAMColumn'];
			}
			if (!$this->imgTitleDAMColumn) $this->imgTitleDAMColumn = 'caption';
			$imgMetaData = tx_dam::meta_getDataForFile($filepath,'uid,pid,alt_text,hpixels,vpixels,'.$this->imgTitleDAMColumn.','.$TCA['tx_dam']['ctrl']['languageField']);
			$imgMetaData = $this->getRecordOverlay('tx_dam',$imgMetaData,$this->sys_language_content);
			
			switch ($this->act) {
				case 'magic':
					if (is_array($imgInfo) && count($imgInfo)==4 && $this->rteImageStorageDir() && is_array($imgMetaData))	{
						$fI=pathinfo($imgInfo[3]);
						$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
						$basename = $fileFunc->cleanFileName('RTEmagicP_'.$fI['basename']);
						$destPath =PATH_site.$this->rteImageStorageDir();
						if (@is_dir($destPath))	{
							$destName = $fileFunc->getUniqueName($basename,$destPath);
							@copy($imgInfo[3],$destName);
							
							$cWidth = t3lib_div::intInRange(t3lib_div::_GP('cWidth'),0,$this->magicMaxWidth);
							$cHeight = t3lib_div::intInRange(t3lib_div::_GP('cHeight'),0,$this->magicMaxHeight);
							if (!$cWidth)	$cWidth = $this->magicMaxWidth;
							if (!$cHeight)	$cHeight = $this->magicMaxHeight;
							
							$imgI = $imgObj->imageMagickConvert($filepath,'WEB',$cWidth.'m',$cHeight.'m');	// ($imagefile,$newExt,$w,$h,$params,$frame,$options,$mustCreate=0)
							if ($imgI[3])	{
								$fI=pathinfo($imgI[3]);
								$mainBase='RTEmagicC_'.substr(basename($destName),10).'.'.$fI['extension'];
								$destName = $fileFunc->getUniqueName($mainBase,$destPath);
								@copy($imgI[3],$destName);
								$iurl = $this->siteUrl.substr($destName,strlen(PATH_site));
								$this->imageInsertJS($iurl,$imgI[0],$imgI[1],$imgMetaData['alt_text'],$imgMetaData[$this->imgTitleDAMColumn],substr($imgInfo[3],strlen(PATH_site)));
							}
						}
					}
					exit;
					break;
				case 'plain':
					if (is_array($imgInfo) && count($imgInfo)==4 && is_array($imgMetaData))	{
						$iurl = $this->siteUrl.substr($imgInfo[3],strlen(PATH_site));
						$this->imageInsertJS($iurl,$imgMetaData['hpixels'],$imgMetaData['vpixels'],$imgMetaData['alt_text'],$imgMetaData[$this->imgTitleDAMColumn],substr($imgInfo[3],strlen(PATH_site)));
					}
					exit;
					break;
			}
		}
	}
	
	function imageInsertJS($url,$width,$height,$altText,$titleText,$origFile) {
		global $TYPO3_CONF_VARS;
		
		echo'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Untitled</title>
</head>
<script language="javascript" type="text/javascript">
/*<![CDATA[*/
	var editor = window.opener.RTEarea[' . $this->editorNo . ']["editor"];
	var HTMLArea = window.opener.HTMLArea;
	function insertImage(file,width,height,alt,title,origFile)	{
		var styleWidth, styleHeight;
		styleWidth = parseInt(width);
		if (isNaN(styleWidth) || styleWidth == 0) {
			styleWidth = "auto";
		} else {
			styleWidth += "px";
		}
		styleHeight = parseInt(height);
		if (isNaN(styleHeight) || styleHeight == 0) {
			styleHeight = "auto";
		} else {
			styleHeight += "px";
		}
		editor.renderPopup_insertImage(\'<img src="\'+file+\'" alt="\'+alt+\'" title="\'+title+\'" style="width: \'+styleWidth+\'; height: \'+styleHeight+\';"'.(($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['enableClickEnlarge'] && !(is_array($this->buttonConfig['clickEnlarge.']) && $this->buttonConfig['clickEnlarge.']['disabled']))?' clickenlargesrc="\'+origFile+\'" clickenlarge="0"':'').' />\');
	}
/*]]>*/
</script>
<body>
<script type="text/javascript">
/*<![CDATA[*/
	insertImage('.t3lib_div::quoteJSvalue($url,1).','.$width.','.$height.','.t3lib_div::quoteJSvalue($altText,1).','.t3lib_div::quoteJSvalue($titleText,1).','.t3lib_div::quoteJSvalue($origFile,1).');
/*]]>*/
</script>
</body>
</html>';
	}

	/**
	 * Insert misc JS functions in ->doc
	 *
	 * @return	string
	 */
	function getJSCode()	{
		global $LANG,$BACK_PATH,$TYPO3_CONF_VARS;

		$JScode='
			var editor = window.opener.RTEarea[' . $this->editorNo . ']["editor"];
			var HTMLArea = window.opener.HTMLArea;
			function insertElement(table, uid, type, filename,fp,filetype,imagefile,action, close)	{
				return jumpToUrl(\''.$this->thisScript.'?act='.$this->act.'&mode='.$this->mode.'&bparams='.$this->bparams.'&insertImage='.'\'+fp);
			}
			function jumpToUrl(URL,anchor)	{	//
				var add_act = URL.indexOf("act=")==-1 ? "&act='.$this->act.'" : "";
				var add_editorNo = URL.indexOf("editorNo=")==-1 ? "&editorNo='.$this->editorNo.'" : "";
				var add_sys_language_content = URL.indexOf("sys_language_content=")==-1 ? "&sys_language_content='.$this->sys_language_content.'" : "";
				var RTEtsConfigParams = "&RTEtsConfigParams='.rawurlencode(t3lib_div::_GP('RTEtsConfigParams')).'";
				
				var cur_width = selectedImageRef ? "&cWidth="+selectedImageRef.style.width : "";
				var cur_height = selectedImageRef ? "&cHeight="+selectedImageRef.style.height : "";
				
				var theLocation = URL+add_act+add_editorNo+add_sys_language_content+RTEtsConfigParams+cur_width+cur_height+(anchor?anchor:"");
				window.location.href = theLocation;
				return false;
			}
			function launchView(url) {
				var thePreviewWindow="";
				thePreviewWindow = window.open("'.$this->siteUrl.TYPO3_mainDir.'show_item.php?table="+url,"ShowItem","height=300,width=410,status=0,menubar=0,resizable=0,location=0,directories=0,scrollbars=1,toolbar=0");
				if (thePreviewWindow && thePreviewWindow.focus)	{
					thePreviewWindow.focus();
				}
			}
			function getCurrentImageRef() {
				if (editor._selectedImage) {
					return editor._selectedImage;
				} else {
					return null;
				}
			}
			function printCurrentImageOptions() {
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if(classesImage) var styleSelector=\'<select name="iClass" style="width:140px;">' . $this->classesImageJSOptions  . '</select>\';
				var floatSelector=\'<select name="iFloat" id="iFloat"><option value="">' . $LANG->getLL('notSet') . '</option><option value="none">' . $LANG->getLL('nonFloating') . '</option><option value="left">' . $LANG->getLL('left') . '</option><option value="right">' . $LANG->getLL('right') . '</option></select>\';
				var bgColor=\' class="bgColor4"\';
				var sz="";
				sz+=\'<table border=0 cellpadding=1 cellspacing=1><form action="" name="imageData">\';
				if(classesImage) {
					sz+=\'<tr><td\'+bgColor+\'>'.$LANG->getLL('class').': </td><td>\'+styleSelector+\'</td></tr>\';
				}
				sz+=\'<tr><td\'+bgColor+\'><label for="iWidth">'.$LANG->getLL('width').': </label></td><td><input type="text" name="iWidth" id="iWidth" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iHeight">'.$LANG->getLL('height').': </label></td><td><input type="text" name="iHeight" id="iHeight" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iBorder">'.$LANG->getLL('border').': </label></td><td><input type="checkbox" name="iBorder" id="iBorder" value="1" /></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iFloat">'.$LANG->getLL('float').': </label></td><td>\'+floatSelector+\'</td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iHspace">'.$LANG->getLL('margin_lr').': </label></td><td><input type="text" name="iHspace" id="iHspace" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).'></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iVspace">'.$LANG->getLL('margin_tb').': </label></td><td><input type="text" name="iVspace" id="iVspace" value=""'.$GLOBALS['TBE_TEMPLATE']->formWidth(4).' /></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iTitle">'.$LANG->getLL('title').': </label></td><td><input type="text" name="iTitle" id="iTitle"'.$GLOBALS['TBE_TEMPLATE']->formWidth(20).' /></td></tr>\';
				sz+=\'<tr><td\'+bgColor+\'><label for="iAlt">'.$LANG->getLL('alt').': </label></td><td><input type="text" name="iAlt" id="iAlt"'.$GLOBALS['TBE_TEMPLATE']->formWidth(20).' /></td></tr>\';
				'.(($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['enableClickEnlarge'] && !(is_array($this->buttonConfig['clickEnlarge.']) && $this->buttonConfig['clickEnlarge.']['disabled']))?'if (selectedImageRef && selectedImageRef.getAttribute("clickenlargesrc")) sz+=\'<tr><td\'+bgColor+\'><label for="iClickEnlarge">'.$LANG->sL('LLL:EXT:cms/locallang_ttc.php:image_zoom',1).' </label></td><td><input type="checkbox" name="iClickEnlarge" id="iClickEnlarge" value="1" /></td></tr>\';':'').'
				sz+=\'<tr><td><input type="submit" value="'.$LANG->getLL('update').'" onClick="return setImageProperties();"></td></tr>\';
				sz+=\'</form></table>\';
				return sz;
			}
			function setImageProperties() {
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if (selectedImageRef)	{
					if(document.imageData.iWidth.value && document.imageData.iWidth.value != "auto") {
						selectedImageRef.style.width = document.imageData.iWidth.value + "px";
					} else {
						selectedImageRef.style.width = "auto";
					}
					selectedImageRef.removeAttribute("width");
					if(document.imageData.iHeight.value && document.imageData.iHeight.value != "auto") {
						selectedImageRef.style.height=document.imageData.iHeight.value + "px";
					} else {
						selectedImageRef.style.height = "auto";
					}
					selectedImageRef.removeAttribute("height");

					selectedImageRef.style.paddingTop = "0px";
					selectedImageRef.style.paddingBottom = "0px";
					selectedImageRef.style.paddingRight = "0px";
					selectedImageRef.style.paddingLeft = "0px";
					selectedImageRef.style.padding = "";  // this statement ignored by Mozilla 1.3.1
					if(document.imageData.iVspace.value != "" && !isNaN(parseInt(document.imageData.iVspace.value))) {
						selectedImageRef.style.paddingTop = parseInt(document.imageData.iVspace.value) + "px";
						selectedImageRef.style.paddingBottom = selectedImageRef.style.paddingTop;
					}
					if(document.imageData.iHspace.value != "" && !isNaN(parseInt(document.imageData.iHspace.value))) {
						selectedImageRef.style.paddingRight = parseInt(document.imageData.iHspace.value) + "px";
						selectedImageRef.style.paddingLeft = selectedImageRef.style.paddingRight;
					}
					selectedImageRef.removeAttribute("vspace");
					selectedImageRef.removeAttribute("hspace");

					selectedImageRef.title=document.imageData.iTitle.value;
					selectedImageRef.alt=document.imageData.iAlt.value;

					selectedImageRef.style.borderStyle = "none";
					selectedImageRef.style.borderWidth = "0px";
					selectedImageRef.style.border = "";  // this statement ignored by Mozilla 1.3.1
					if(document.imageData.iBorder.checked) {
						selectedImageRef.style.borderStyle = "solid";
						selectedImageRef.style.borderWidth = "thin";
					}
					selectedImageRef.removeAttribute("border");

					var iFloat = document.imageData.iFloat.options[document.imageData.iFloat.selectedIndex].value;
					if (iFloat || selectedImageRef.style.cssFloat || selectedImageRef.style.styleFloat)	{
						if(document.all) {
							selectedImageRef.style.styleFloat = iFloat;
						} else {
							selectedImageRef.style.cssFloat = iFloat;
						}
					}
					
					if(classesImage) {
						var iClass = document.imageData.iClass.options[document.imageData.iClass.selectedIndex].value;
						if (iClass || (selectedImageRef.attributes["class"] && selectedImageRef.attributes["class"].value))	{
							selectedImageRef.className = iClass;
						}
					}
					
					'.(($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['enableClickEnlarge'] && !(is_array($this->buttonConfig['clickEnlarge.']) && $this->buttonConfig['clickEnlarge.']['disabled']))?'
					if (document.imageData.iClickEnlarge && document.imageData.iClickEnlarge.checked) selectedImageRef.setAttribute("clickenlarge","1");
						else selectedImageRef.setAttribute("clickenlarge","0");':'').'
					
					HTMLArea.edHidePopup();
				}
				return false;
			}
			function insertImagePropertiesInForm()	{
				var classesImage = ' . ($this->thisConfig['classesImage']?'true':'false') . ';
				if (selectedImageRef)	{
					var styleWidth, styleHeight, paddingTop, paddingRight;
					styleWidth = selectedImageRef.style.width ? selectedImageRef.style.width : selectedImageRef.width;
					styleWidth = parseInt(styleWidth);
					if (isNaN(styleWidth) || styleWidth == 0) { styleWidth = "auto"; }
					document.imageData.iWidth.value = styleWidth;
					styleHeight = selectedImageRef.style.height ? selectedImageRef.style.height : selectedImageRef.height;
					styleHeight = parseInt(styleHeight);
					if (isNaN(styleHeight) || styleHeight == 0) { styleHeight = "auto"; }
					document.imageData.iHeight.value = styleHeight;

					paddingTop = selectedImageRef.style.paddingTop ? selectedImageRef.style.paddingTop : selectedImageRef.vspace;
					paddingTop = parseInt(paddingTop);
					if (isNaN(paddingTop) || paddingTop < 0) { paddingTop = ""; }
					document.imageData.iVspace.value = paddingTop;
					paddingRight = selectedImageRef.style.paddingRight ? selectedImageRef.style.paddingRight : selectedImageRef.hspace;
					paddingRight = parseInt(paddingRight);
					if (isNaN(paddingRight) || paddingRight < 0) { paddingRight = ""; }
					document.imageData.iHspace.value = paddingRight;

					document.imageData.iTitle.value = selectedImageRef.title;
					document.imageData.iAlt.value = selectedImageRef.alt;

					if((selectedImageRef.style.borderStyle && selectedImageRef.style.borderStyle != "none" && selectedImageRef.style.borderStyle != "none none none none") || selectedImageRef.border) {
						document.imageData.iBorder.checked = 1;
					}

					var fObj=document.imageData.iFloat;
					var value = (selectedImageRef.style.cssFloat ? selectedImageRef.style.cssFloat : selectedImageRef.style.styleFloat);
					var l=fObj.length;
					for (a=0;a<l;a++)	{
						if (fObj.options[a].value == value)	{
							fObj.selectedIndex = a;
						}
					}

					if(classesImage) {
						var fObj=document.imageData.iClass;
						var value=selectedImageRef.className;
						var l=fObj.length;
						for (a=0;a<l;a++)	{
							if (fObj.options[a].value == value)	{
								fObj.selectedIndex = a;
							}
						}
					}
					
					'.(($TYPO3_CONF_VARS['EXTCONF'][$this->extKey]['enableClickEnlarge'] && !(is_array($this->buttonConfig['clickEnlarge.']) && $this->buttonConfig['clickEnlarge.']['disabled']))?'if (selectedImageRef.getAttribute("clickenlargesrc")) {
						if (selectedImageRef.getAttribute("clickenlarge") == "1") document.imageData.iClickEnlarge.checked = 1;
							else document.imageData.iClickEnlarge.removeAttribute("checked");
					}':'').'
				}
				return false;
			}

			function openDragDrop()	{
				var url = "' . $BACK_PATH . 'browse_links.php?mode=filedrag&editorNo='.$this->editorNo.'&bparams=|'.implode(':', array($this->editorNo,$this->sys_language_content)).'||"+escape("gif,jpg,jpeg,png");
				window.opener.browserWin = window.open(url,"Typo3WinBrowser","height=350,width=600,status=0,menubar=0,resizable=1,scrollbars=1");
				HTMLArea.edHidePopup();
			}

			var selectedImageRef = getCurrentImageRef();	// Setting this to a reference to the image object.

			'.($this->act=='dragdrop'?'openDragDrop();':'');

			// Finally, add the accumulated JavaScript to the template object:
		$this->doc->JScode = $this->doc->wrapScriptTags($JScode);
	}
	
	function reinitParams() {
// We need to pass along some RTE parameters
		if ($this->editorNo) {
			$pArr = explode('|', $this->bparams);
			$pArr[1] = implode(':', array($this->editorNo, $this->sys_language_content));
			$pArr[2] = $this->RTEtsConfigParams;
			$this->bparams = implode('|', $pArr);
			
		}
// We need to pass along some RTE parameters
		parent::reinitParams();
	}



	/**
	 * Return true or false whether thumbs can be displayed or not
	 *
	 * @return	boolean
	 */
	function thumbsEnabled() {
			// Getting flag for showing/not showing thumbnails:
		$noThumbs = $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInEB') || ($this->mode == 'rte' && $GLOBALS['BE_USER']->getTSConfigVal('options.noThumbsInRTEimageSelect'));
		return !$noThumbs;
	}



	
	/**
	 * Renders the EB for rte mode
	 *
	 * @return	string HTML
	 */
	function main_rte()	{
		global $LANG, $TYPO3_CONF_VARS, $FILEMOUNTS, $BE_USER;
		
		$path = tx_dam::path_makeAbsolute($this->damSC->path);
		if (!$path OR !@is_dir($path))	{
			$fileProcessor = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
			$path = $fileProcessor->findTempFolder().'/';	// The closest TEMP-path is found
		}
		$this->damSC->path = tx_dam::path_makeRelative($path); // mabe not needed
		
			// Starting content:
		$content = $this->doc->startPage($LANG->getLL('Insert Image',1));
		
		$this->reinitParams();
		
			// Making menu in top:
		$menuDef = array();
		if (in_array('image',$this->allowedItems) && ($this->act=='image' || t3lib_div::_GP('cWidth'))) {
			$menuDef['page']['isActive'] = $this->act=='image';
			$menuDef['page']['label'] = $LANG->getLL('currentImage',1);
			$menuDef['page']['url'] = '#';
			$menuDef['page']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=image&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('magic',$this->allowedItems)){
			$menuDef['file']['isActive'] = $this->act=='magic';
			$menuDef['file']['label'] = $LANG->getLL('magicImage',1);
			$menuDef['file']['url'] = '#';
			$menuDef['file']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=magic&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('plain',$this->allowedItems)) {
			$menuDef['url']['isActive'] = $this->act=='plain';
			$menuDef['url']['label'] = $LANG->getLL('plainImage',1);
			$menuDef['url']['url'] = '#';
			$menuDef['url']['addParams'] = 'onClick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=plain&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		if (in_array('dragdrop',$this->allowedItems)) {
			$menuDef['mail']['isActive'] = $this->act=='dragdrop';
			$menuDef['mail']['label'] = $LANG->getLL('dragDropImage',1);
			$menuDef['mail']['url'] = '#';
			$menuDef['mail']['addParams'] = 'onClick="openDragDrop();return false;"';
		}
		if (in_array('upload', $this->allowedItems)) {
			$menuDef['upload']['isActive'] = ($this->act=='upload');
			$menuDef['upload']['label'] = $LANG->getLL('tx_dam_file_upload.title',1);
			$menuDef['upload']['url'] = '#';
			$menuDef['upload']['addParams'] = 'onclick="jumpToUrl(\''.htmlspecialchars($this->thisScript.'?act=upload&mode='.$this->mode.'&bparams='.$this->bparams).'\');return false;"';
		}
		$content .= $this->doc->getTabMenuRaw($menuDef);
		
		switch($this->act)	{
			case 'image':
				$JScode = '
				document.write(printCurrentImageOptions());
				insertImagePropertiesInForm();';
				$content.= '<br />'.$this->doc->wrapScriptTags($JScode);
				break;
				
			case 'upload':
				$content.= $this->dam_upload($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
				$content.='<br /><br />';
				if ($BE_USER->isAdmin() || $BE_USER->getTSConfigVal('options.createFoldersInEB'))	{
					$content.= $this->createFolder($path);
					$content.= '<br />';
				}
				break;
				
			case 'plain':
				$this->allowedFileTypes = explode(',', 'jpg,jpeg,gif,png');
				$this->addDisplayOptions();
				$content.= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
				$content .= $this->getMsgBox(sprintf($LANG->getLL('plainImage_msg'), $this->plainMaxWidth, $this->plainMaxHeight));
				break;
				
			case 'magic':
				$this->addDisplayOptions();
				$content.= $this->dam_select($this->allowedFileTypes, $this->disallowedFileTypes);
				$content.= $this->damSC->getOptions();
				$content .= $this->getMsgBox($LANG->getLL('magicImage_msg'));

				break;
			default:
				break;
		}
			// Ending page, returning content:
		$content.= $this->doc->endPage();
		$content = $this->damSC->doc->insertStylesAndJS($content);
		return $content;
	}
	
	/**
	 * Import from t3lib_page in order to create backend version
	 * Creates language-overlay for records in general (where translation is found in records from the same table)
	 *
	 * @param	string		Table name
	 * @param	array		Record to overlay. Must containt uid, pid and $table]['ctrl']['languageField']
	 * @param	integer		Pointer to the sys_language uid for content on the site.
	 * @param	string		Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but unset (and return value is false)
	 * @return	mixed		Returns the input record, possibly overlaid with a translation. But if $OLmode is "hideNonTranslated" then it will return false if no translation is found.
	 */
	function getRecordOverlay($table,$row,$sys_language_content,$OLmode='')	{
		global $TCA, $TYPO3_DB;
		if ($row['uid']>0 && $row['pid']>0)	{
			if ($TCA[$table] && $TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'])	{
				if (!$TCA[$table]['ctrl']['transOrigPointerTable'])	{
						// Will try to overlay a record only if the sys_language_content value is larger that zero.
					if ($sys_language_content>0)	{
							// Must be default language or [All], otherwise no overlaying:
						if ($row[$TCA[$table]['ctrl']['languageField']]<=0)	{
								// Select overlay record:
							$res = $TYPO3_DB->exec_SELECTquery(
								'*',
								$table,
								'pid='.intval($row['pid']).
									' AND '.$TCA[$table]['ctrl']['languageField'].'='.intval($sys_language_content).
									' AND '.$TCA[$table]['ctrl']['transOrigPointerField'].'='.intval($row['uid']).
									t3lib_BEfunc::BEenableFields($table).
									t3lib_BEfunc::deleteClause($table),
								'',
								'',
								'1'
								);
							$olrow = $TYPO3_DB->sql_fetch_assoc($res);
							//$this->versionOL($table,$olrow);
							
								// Merge record content by traversing all fields:
							if (is_array($olrow))	{
								foreach($row as $fN => $fV)	{
									if ($fN!='uid' && $fN!='pid' && isset($olrow[$fN]))	{
										if ($TCA[$table]['l10n_mode'][$fN]!='exclude' && ($TCA[$table]['l10n_mode'][$fN]!='mergeIfNotBlank' || strcmp(trim($olrow[$fN]),'')))	{
											$row[$fN] = $olrow[$fN];
										}
									}
								}
							} elseif ($OLmode==='hideNonTranslated' && $row[$TCA[$table]['ctrl']['languageField']]==0)	{	// Unset, if non-translated records should be hidden. ONLY done if the source record really is default language and not [All] in which case it is allowed.
								unset($row);
							}

							// Otherwise, check if sys_language_content is different from the value of the record - that means a japanese site might try to display french content.
						} elseif ($sys_language_content!=$row[$TCA[$table]['ctrl']['languageField']])	{
							unset($row);
						}
					} else {
							// When default language is displayed, we never want to return a record carrying another language!:
						if ($row[$TCA[$table]['ctrl']['languageField']]>0)	{
							unset($row);
						}
					}
				}
			}
		}

		return $row;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_media.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.tx_dam_rtehtmlarea_browse_media.php']);
}

?>