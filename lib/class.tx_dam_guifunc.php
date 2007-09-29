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
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   81: class tx_dam_guiFunc
 *
 *              SECTION: icons
 *  102:     function icon_getFileTypeImgTag($infoArr, $addAttrib='')
 *  118:     function icon_getMediaType($infoArr, $addAttrib='', $addTitleAttr=true)
 *  143:     function icon_getTitleAttribute($infoArr, $displayItems='')
 *  159:     function convert_mediaType($type)
 *
 *              SECTION: Small GUI elements
 *  189:     function getMediaTypeIconBox($infoArr, $iconPlusType=TRUE)
 *
 *              SECTION: Table GUI elements
 *  221:     function getRecordInfoHeader($row)
 *
 *              SECTION: Path related functions
 *  285:     function getFolderInfoBar($pathInfo, $maxLength=55)
 *  319:     function getPathBreadcrumbMenu($pathInfo, $browsable=FALSE, $maxLength=55, $param='SET[tx_dam_folder]')
 *
 *              SECTION: Thumbnail like a dia
 *  383:     function getDia($row, $diaSize=115, $diaMargin=10, $showElements='', $onClick=NULL, $makeIcon=TRUE)
 *  484:     function getDiaStyles($diaSize=115, $diaMargin=10, $margin=0)
 *
 *              SECTION: Meta data related - prepare for output
 *  562:     function meta_compileHoverText ($row, $displayItems='', $implodeWith="\n")
 *  580:     function meta_compileInfoData ($row, $displayItems='', $formatData='')
 *
 *              SECTION: Tools - used internally but might be useful for general usage
 *  689:     function tools_formatValue ($itemValue, $format, $config)
 *  773:     function thumbnail($theFile, $size='', $title='', $attribs='', $attribsIcon='', $onClick=NULL, $makeFileIcon=TRUE, $backPath='')
 *
 * TOTAL FUNCTIONS: 14
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




/**
 * Misc DAM BE GUI functions
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage GUI
 */
class tx_dam_guiFunc {



	/***************************************
	 *
	 *	 icons
	 *
	 ***************************************/



	/**
	 * Returns a file or folder icon for a given (file)path as HTML img tag.
	 * A title attribute will be added by default.
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$addAttrib Additional attributes for the image tag.
	 * @return	string		Icon img tag
	 * @see tx_dam::path_getInfo()
	 */
	function icon_getFileTypeImgTag($infoArr, $addAttrib='')	{
		if (strpos($addAttrib, 'title=')===false) {
			$addAttrib .= tx_dam_guiFunc::icon_getTitleAttribute($infoArr);
		}
		return tx_dam::icon_getFileTypeImgTag($infoArr, $addAttrib);
	}


	/**
	 * Returns a big media type icon from a record
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$addAttrib Additional attributes for the image tag.
	 * @param	boolean		$addTitleAttr If set a title attribute will be added
	 * @return	string		Rendered icon img tag
	 */
	function icon_getMediaType($infoArr, $addAttrib='', $addTitleAttr=true) {
		global $LANG;

		if($addTitleAttr) {
			$label = t3lib_befunc::getLabelFromItemlist('tx_dam', 'media_type', $infoArr['media_type']);
			$label = strtoupper(trim($LANG->sL($label)));
			$addAttrib .= ' title="'.htmlspecialchars($label).'"';
		}

		$iconname = tx_dam::convert_mediaType($infoArr['media_type']);
		$iconfile = PATH_txdam_rel.'i/media-'.$iconname.'.png';

		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconfile, 'width="29" height="27"').' '.trim($addAttrib).' alt="" />';

		return $icon;
	}


	/**
	 * Returns title attribute from a record for use with icons
	 *
	 * @param	array		$infoArr Record/info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	string		$displayItems Item names for the hover text as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @return	string		title attribute
	 */
	function icon_getTitleAttribute($infoArr, $displayItems='') {

		$hoverText = tx_dam_guiFunc::meta_compileHoverText($infoArr, $displayItems);
		$titleAttrib = ' title="'.htmlspecialchars($hoverText).'"';

		return $titleAttrib;
	}


	/**
	 * Converts the media type code to a name .
	 * In comparison to tx_dam::convert_mediaType() this function returns a localized name if possible.
	 *
	 * @param	mixed		$type Media type name or media type code to convert. Integer or 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	mixed		Media type name or media type code
	 */
	function convert_mediaType($type) {
		global $LANG;

		if(!strcmp($type,intval($type)) AND is_object($LANG)) {
			$type = t3lib_befunc::getLabelFromItemlist('tx_dam', 'media_type', $type);
			$type = $LANG->sL($type);
		} else {
				// convert to code
			$type = tx_dam::convert_mediaType($type);
				// convert to localized name
			$type = tx_dam_guiFunc::convert_mediaType($type);
		}
		return $type;
	}


	/***************************************
	 *
	 *	 Small GUI elements
	 *
	 ***************************************/


	/**
	 * Returns a media type icon from a record
	 *
	 * @param	array		$infoArr Record array
	 * @param	boolean		$iconPlusType If set the name of the media type is printed below the icon
	 * @return	string		Rendered icon
	 */
	function getMediaTypeIconBox($infoArr, $iconPlusType=TRUE) {
		global $LANG, $BACK_PATH;

		$label = t3lib_befunc::getLabelFromItemlist('tx_dam', 'media_type', $infoArr['media_type']);
		$label = strtoupper(trim($LANG->sL($label)));

		$icon = tx_dam_guiFunc::icon_getMediaType($infoArr, '', !$iconPlusType);

		if($iconPlusType) {
			$icon = '<div class="txdam-typeiconbox" style="text-align:center;">'.$icon.'<br /><span style="color: #555;">'.htmlspecialchars($label).'</span></div>';
		}

		return $icon;
	}




	/***************************************
	 *
	 *	 Table GUI elements
	 *
	 ***************************************/



	/**
	 * Returns a table with some info and a thumbnail from a record
	 *
	 * @param	array		Record array
	 * @return	string		HTML content
	 */
	function getRecordInfoHeader($row) {
		global $LANG;

		$content = '';

		$icon = tx_dam_guiFunc::getMediaTypeIconBox($row);

		$content.= '
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" width="1%">'.$icon.'</td>
					<td valign="top" align="left" style="padding-left:20px;">';

		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:lang/locallang_general.xml:LGL.title',1).'</strong><br />'.
					htmlspecialchars($row['title']).'</div>';

		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_name',1).'</strong><br />'.
					htmlspecialchars($row['file_name']).'</div>';

		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.xml:tx_dam_item.file_path',1).'</strong><br />'.
					htmlspecialchars($row['file_path']).'</div>';

		if ($row['media_type'] == TXDAM_mtype_image) {
			$out = '';
			$out.= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'].' px, ' :'';
			$out.= t3lib_div::formatSize($row['file_size']);
			$out.= $row['color_space'] ? ', '.$LANG->sL(t3lib_befunc::getLabelFromItemlist('tx_dam','color_space',$row['color_space'])) : '';

			$content.=	'<div style="margin-bottom:7px;"><nobr>'.htmlspecialchars($out).'</nobr></div>';
		}

		$content.= '
					</td>';

		$thumb = tx_dam_guiFunc::getDia($row, 115, 5, $showElements='', $onClick=NULL, $makeIcon=FALSE);
		$content.= '
					<td valign="top" width="1%" style="padding-left:25px;">'.$thumb.'</td>';

		$content.= '
				</tr>
			</table>';

		return '<div style="margin:0px;">'.$content.'</div>';
	}






	/********************************
	 *
	 * Path related functions
	 *
	 ********************************/


	/**
	 * Makes the code for the foldericon in the top
	 *
	 * @param	array		$pathInfo Path info array: $pathInfo = tx_dam::path_getInfo($path)
	 * @param	integer		$maxLength Maximum Text length
	 * @return	string		HTML code
	 */
	function getFolderInfoBar($pathInfo, $maxLength=55)	{
		global $BACK_PATH, $LANG;

		if (is_array($pathInfo))	{

			$iconFolder = tx_dam::icon_getFolder($pathInfo);
			$elements['icon'] = '<img'.t3lib_iconWorks::skinImg($BACK_PATH, $iconFolder, 'width="18" height="16"').' alt="" />';

			$elements['path'] = tx_dam_guiFunc::getPathBreadcrumbMenu($pathInfo, false, $maxLength);

			$out = '

		<!--
			Page header for file list
		-->
				<table border="0" cellpadding="0" cellspacing="0" id="typo3-pathBreadcrumbBar">
					<tr><td>'.implode('</td><td>', $elements).'</td></tr>
				</table>';

		}
		return $out;
	}


	/**
	 * Returns a path with links for browsing.
	 * Is like a breadcrumb menu
	 *
	 * @param	array		$pathInfo tx_dam::path_compileInfo($path);
	 * @param	boolean		$browsable If set links are enabled
	 * @param	integer		$maxLength Maximum Text length
	 * @param	string		$param The name of the GET parameter. Default: SET[tx_dam_folder]
	 * @return	string		Linked Path
	 */
	 function getPathBreadcrumbMenu($pathInfo, $browsable=FALSE, $maxLength=55, $param='SET[tx_dam_folder]') {
	 	$pathArr = explode('/', $pathInfo['dir_path_from_mount']);
		array_pop($pathArr);
	 	$pathArrRev = array_reverse($pathArr, TRUE);

		$len = 0;

		$mountPart = '';
		if($pathInfo['mount_id']) {
			if ($browsable) {
				$mountTitle = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array($param => $pathInfo['mount_path']))).'">'.htmlspecialchars($pathInfo['mount_name']).'</a>';
			} else {
				$mountTitle = htmlspecialchars($pathInfo['mount_name']);
			}
			$mountPart = '['.$mountTitle.']: ';
	 		$len = strlen($pathInfo['mount_name'])+4;
		}

	 	$newPathArr = array();
	 	foreach ($pathArrRev as $key => $part) {

			$part = t3lib_div::fixed_lgd($part, 20);
		 	if ($part) {
		 		$len += strlen($part)+1;
		 		if ($len > $maxLength) {
		 			$part = '...';
		 		}
			 	if ($browsable) {
		 			$linkPath = implode('/', $pathArr).'/';
					$newPathArr[] = '<a href="'.htmlspecialchars(t3lib_div::linkThisScript(array($param => $pathInfo['mount_path'].$linkPath))).'">'.htmlspecialchars($part).'/</a>';
			 	} else {
					$newPathArr[] = htmlspecialchars($part.'/');
			 	}
		 	}
		 	if ($len > $maxLength) { break; }
			array_pop($pathArr);
	 	}
	 	$newPathArr = array_reverse($newPathArr);

		$BreadcrumbMenu = $mountPart.implode('', $newPathArr);
		return '<span class="typo3-pathBreadcrumbMenu">'.$BreadcrumbMenu.'</span>';
	}



	/***************************************
	 *
	 *	 Thumbnail like a dia
	 *
	 ***************************************/



	/**
	 * Returns a dia like thumbnail
	 *
	 * @param	array		tx_dam record
	 * @param	integer		dia size
	 * @param	integer		dia margin
	 * @param	array		Extra elements to show: "title,info,icons"
	 * @param	string		$onClick: ...
	 * @param	boolean		$makeIcon: ...
	 * @return	string		HTML output
	 */
	function getDia($row, $diaSize=115, $diaMargin=10, $showElements='', $onClick=NULL, $makeIcon=TRUE) {

		if(!is_array($showElements)) {
			$showElements = t3lib_div::trimExplode(',', $showElements,1);
		}


			// extra CSS code for HTML header
		if(is_object($GLOBALS['SOBE']) AND !isset($GLOBALS['SOBE']->doc->inDocStylesArray['tx_dam_SCbase_dia'])) {
			$GLOBALS['SOBE']->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_guiFunc::getDiaStyles($diaSize, $diaMargin);
		}

// using css/stylesheet
		$iconBgColor = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-10,-10,-10);
		$titleLen = ceil( (30*($diaSize-$diaMargin))/(200-$diaMargin) );

		$hpixels = $row['hpixels'];
		$vpixels = $row['vpixels'];

		if ($hpixels AND $vpixels) {
			$maxpx = max($hpixels, $vpixels);
			$minpx = min($hpixels, $vpixels);
			$px = intval(round($minpx * $diaSize / $maxpx));
			if ($hpixels > $vpixels) {
				$hpixels = $diaSize;
				$vpixels = $px;
			} else {
				$hpixels = $px;
				$vpixels = $diaSize;
			}
		} else {
			if($hpixels > $diaSize) {
				$hpixels = $diaSize;
			}
			if($vpixels > $diaSize) {
				$vpixels = $diaSize;
			}
		}


		$uid = $row['uid'];
		$tooltip = str_replace("\n", '', t3lib_div::fixed_lgd_cs($row['description'], 50));
		if ($hpixels) {
			$attribs = ' width="'.$hpixels.'" height="'.$vpixels.'" style="margin-top:'.(ceil(($diaSize-$vpixels)/2)+$diaMargin).'px;"';
		} else {
			$attribs = ' style="margin-top:'.$diaMargin.'px;"';
		}
		#$attribsIcon = ' style="margin-top:'.$diaMargin.'px;padding:'.(ceil(($diaSize-18)/2)).'px"';
		$attribsIcon = '';
		$thumb = tx_dam_guiFunc::thumbnail($row['file_path'].$row['file_name'], $diaSize, $tooltip, $attribs, $attribsIcon, $onClick, FALSE);
		#$attribsIcon = ' style="margin-top:'.$diaMargin.'px;padding:'.(ceil(($diaSize-29)/2)).'px"';
		#$thumb = $thumb ? $thumb : '<a href="#"'.$onClick.'><span'.$attribsIcon.'>'.tx_dam_guiFunc::getMediaTypeIconBox($row).'</span></a>';


		if (!$makeIcon AND empty($thumb)) { return; }
		$thumb = $thumb ? $thumb : '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.tx_dam_guiFunc::getMediaTypeIconBox($row).'</a>';

		$descr = '';
		if (in_array('title', $showElements)) {
			$descr.= htmlspecialchars(t3lib_div::fixed_lgd_cs($row['title'], $titleLen)).'<br />';
		}
		if (in_array('info', $showElements)) {
			$code = strtoupper($row['file_type']).', ';
			$code.= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'].', ' :'';
			$code.= t3lib_div::formatSize($row['file_size']);
			# $code.= $row['color_space'] ? ', '.$LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tx_dam', 'color_space', $row['color_space'])) : '';
			$descr .= '<span class="txdam-descr">'.htmlspecialchars($code).'</span>';
		}
		if($descr) {
			$descr = '<div class="txdam-title">'.$descr.'</div>';
		}

		$icons  = '';
		if (in_array('icons', $showElements)) {
			$iconArr = array();
			$iconArr[] = tx_dam_SCbase::icon_editRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_editRec_inNewWindow('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::icon_infoRec('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');
			$iconArr[] = tx_dam_SCbase::btn_removeRecFromSel('tx_dam', $row['uid'], 'style="margin-left:3px;margin-right:3px;"');

			$icons = '<div style="margin:3px;">'.implode('<span style="width:40px;"></span>', $iconArr).'</div>';
		}

		$diaCode = '
		<table class="txdam-dia" cellspacing="0" cellpadding="0" border="0">
		<tr><td><span><span class="txdam-dia">'.$thumb.'</span></td></tr>
		'. ( ($descr.$icons) ? '<tr><td align="center" bgcolor="'.$iconBgColor.'">'.$descr.$icons.'</td></tr>' : '').'
		</table> ';

		return $diaCode;
	}

	/**
	 * Return CSS code to be used with used with thumbnail created by getDia()
	 *
	 * @param	integer		$diaSize: ...
	 * @param	integer		$diaMargin: ...
	 * @param	integer		$margin: ...
	 * @return	string		CSS code
	 * @see getDia()
	 */
	function getDiaStyles($diaSize=115, $diaMargin=10, $margin=0) {
			// extra CSS code for HTML header
		$styles = '

			.txdam-title, .txdam-descr {
				font-family:verdana,sans-serif;
				font-size:9.5px;
				line-height:12px;
				margin:2px;
			}
			.txdam-descr {
				color:#777;
			}
			table.txdam-dia {
				float:left;
				margin-bottom:8px;
			}
			span.txdam-dia {
				float:left;
				width:'.($diaSize+($diaMargin*2)+2).'px;
				height:'.($diaSize+($diaMargin*2)+2).'px;

				text-align:center;
				vertical-align:middle;

				margin:'.$margin.'px;
				padding:0px;
				background-color:#fbfbfb;
				border:solid #999 1px;
				border-top:solid #ddd 1px;
				border-bottom:solid #000 1px;
			}

			span.txdam-dia a {
				text-decoration:none;
			}
			span.txdam-dia > a > img {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				vertical-align:50%;
			}
			span.txdam-dia > a > div {
				border:solid 1px #ccc;
				margin:'.($diaMargin).'px;
				padding:'.($diaMargin).'px;
				width:'.($diaSize-$diaMargin-$diaMargin).'px;
				height:'.($diaSize-$diaMargin-$diaMargin).'px;
				vertical-align:middle;
			}
			span.txdam-dia .txdam-typeiconbox {
				line-height:2em; /* IE */
				margin-top: 2em; /* IE */
				border:none;
			}
			';
		return $styles;
	}





	/***************************************
	 *
	 *	 Meta data related - prepare for output
	 *
	 ***************************************/



	/**
	 * Compiles from a meta data array text to be used in title attributes.
	 *
	 * @param	array		$row Meta data record array
	 * @param	string		$displayItems Item names as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @param	string		$implodeWith String that is used to implode the content lines. If false the array will not be imploded and an array will be returned.
	 * @return	mixed		Info data string or non-imploded array
	 */
	function meta_compileHoverText ($row, $displayItems='', $implodeWith="\n") {
		$displayItems = $displayItems ? $displayItems : '_media_type:strtoupper, title, file_name, file_size:filesize, _dimensions';

		$infoData = tx_dam_guiFunc::meta_compileInfoData ($row, $displayItems, 'value-array');
		if (is_string($implodeWith)) {
			$infoData = implode($implodeWith, $infoData);
		}
		return $infoData;
	}

	/**
	 * Compiles from a meta data array human readable content.
	 *
	 * @param	array		$row Meta data record array
	 * @param	string		$displayItems Item names as comma list which are array keys or special names like "_dimensions". Format and option can be added (separated with ":") to call tx_dam::tools_formatValue().
	 * @param	string		$formatData If set the array wll be formatted as "paragraph" or "table".
	 * @return	array		Info data array
	 */
	function meta_compileInfoData ($row, $displayItems='', $formatData='') {

		$infoData = array();

		$displayItems = $displayItems ? $displayItems : 'title, file_name, file_size:filesize, _dimensions, description:truncate:50';
		$displayItems = t3lib_div::trimExplode(',', $displayItems, true);

		foreach ($displayItems as $item) {

			list($item, $format, $config) = t3lib_div::trimExplode(':', $item, true);

			$label = '';
			switch ($item) {



				case '_media_type':
					$infoData[$item]['value'] = tx_dam_guiFunc::convert_mediaType($row['media_type']);
					t3lib_div::loadTCA('tx_dam');
					$label = $GLOBALS['TCA']['tx_dam']['columns']['media_type']['label'];
				break;

				case '_dimensions':
					if ($row['media_type'] == TXDAM_mtype_image AND $row['hpixels']) {
						$infoData[$item]['value'] = $row['hpixels'].'x'.$row['vpixels'].' px';
						$label = 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.metrics';
					}
// TODO document size: mm, cm, ...
				break;
// TODO
//				case '_techinfo':
//					if ($row['media_type'] == TXDAM_mtype_image AND $row['color_space'] AND class_exists('t3lib_befunc')) {
//						$value = t3lib_befunc::getLabelFromItemlist('tx_dam', 'color_space', $row['color_space']);
//						if (is_object($GLOBALS['LANG'])) {
//							$value = $LANG->sL($value);
//						}
//						$infoData[$item]['value'] = $value;
//					}
//				break;

				default:
					if (isset($row[$item])) {
						$infoData[$item]['value'] = $row[$item];
					}
				break;
			}
			if ($format AND isset($infoData[$item])) {
				$infoData[$item]['value'] = tx_dam_guiFunc::tools_formatValue ($infoData[$item]['value'], $format, $config);
			}
			if ($label AND is_object($GLOBALS['LANG'])) {
				$infoData[$item]['label'] = $GLOBALS['LANG']->sL($label);
			}
			if (isset($infoData[$item]) AND !isset($infoData[$item]['label']) AND is_object($GLOBALS['LANG'])) {
				t3lib_div::loadTCA('tx_dam');
				$infoData[$item]['label'] = $GLOBALS['LANG']->sL($GLOBALS['TCA']['tx_dam']['columns'][$item]['label']);
			}
		}


		switch ($formatData) {
			case 'p':
			case 'paragraph':
					$infoText = '';
					foreach($infoData as $val) {
						$infoText .= '<p><strong>'.htmlspecialchars($val['label']).'</strong> '.htmlspecialchars($val['value']).'</p>';
					}
					$infoData = $infoText;
			break;
			case 'table':
					$infoText = '';
					foreach($infoData as $val) {
						$infoText .= '<tr><td><strong>'.htmlspecialchars($val['label']).'</strong>&nbsp;</td><td>'.htmlspecialchars($val['value']).'</td></tr>';
					}
					$infoData = '<table>'.$infoText.'</table>';
			break;
			case 'value-array':
					$infoArr = array();;
					foreach($infoData as $item => $val) {
						$infoArr[$item] = $val['value'];
					}
					$infoData = $infoArr;
			break;
			default:
			break;
		}

		return $infoData;
	}




	/***************************************
	 *
	 *   Tools - used internally but might be useful for general usage
	 *
	 ***************************************/



	/**
	 * Format content of various types if $format is set to date, filesize, ...
	 *
	 * @param	string		$itemValue The value to display
	 * @param	array		$format Configuration for the display
	 * @param	string		$config Additional configuration options for the format type
	 * @return	string		Formatted content
	 * @see t3lib_tceforms::formatValue()
	 */
	function tools_formatValue ($itemValue, $format, $config)	{
		switch($format)	{
			case 'date':
				$config = $config ? $config : 'd-m-Y';
				$itemValue = date($config,$itemValue);
				break;
			case 'dateage':
				$config = $config ? $config : 'd-m-Y';
				$itemValue = date($config,$itemValue);
				$itemValue .= ' ('.t3lib_BEfunc::calcAge((time()-$itemValue), $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.minutesHoursDaysYears')).')';
				break;
			case 'datetime':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i d-m-Y',$itemValue);
				break;
			case 'time':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i',$itemValue);
				break;
			case 'timesec':	// compatibility with "eval" (type "input")
				$itemValue = date('H:i:s',$itemValue);
				break;
			case 'year':	// compatibility with "eval" (type "input")
				$itemValue = date('Y',$itemValue);
				break;
			case 'int':
				$baseArr = array('dec'=>'d','hex'=>'x','HEX'=>'X','oct'=>'o','bin'=>'b');
				$base = trim($config);
				$format = $baseArr[$base] ? $baseArr[$base] : 'd';
				$itemValue = sprintf('%'.$format,$itemValue);
				break;
			case 'float':
				$precision = t3lib_div::intInRange($config,1,10,2);
				$itemValue = sprintf('%.'.$precision.'f',$itemValue);
				break;
			case 'number':
				$itemValue = sprintf('%'.$config,$itemValue);
				break;
			case 'md5':
				$itemValue = md5($itemValue);
				break;
			case 'filesize':
				$itemValue = t3lib_div::formatSize(intval($itemValue)).'b';
				break;
			case 'filesize+bytes':
				$itemValue = t3lib_div::formatSize(intval($itemValue)).'b';
				$itemValue .= ' ('.$itemValue.')';
				break;
			case 'truncate':
				$config = $config ? $config : 20;
				$itemValue = t3lib_div::fixed_lgd_cs($itemValue, $config);
				break;
			case 'strtoupper':
				$itemValue = strtoupper($itemValue);
				break;
			case 'strtolower':
				$itemValue = strtolower($itemValue);
				break;
			default:
			break;
		}

		return $itemValue;
	}


// TODO cleeaaan up thumbnail()


	/**
	 * Returns a linked image-tag for thumbnail(s)
	 * All $TYPO3_CONF_VARS[GFX][imagefile_ext] extension are made to thumbnails + ttf file (renders font-example)
	 * Thumbsnails are linked to the show_item.php script which will display further details.
	 *
	 * Usage:
	 *
	 * @param	string		File name with path relative to PATH_site or absolute
	 * @param	mixed		Optional: $size is [w]x[h] of the thumbnail. 56 is default.
	 * @param	string		Optional: Used as string for the title= attribute
	 * @param	string		Optional: $attribs is additional attributes for the image tags
	 * @param	string		Optional: additional attributes for the image tags for file icons
	 * @param	string		Back path prefix for image tag src="" field. $BACK_PATH is default.
	 * @param	boolean		$makeFileIcon: ...
	 * @param	string		$backPath: ...
	 * @return	string		Thumbnail image tag.
	 */
	function thumbnail($theFile, $size='', $title='', $attribs='', $attribsIcon='', $onClick=NULL, $makeFileIcon=TRUE, $backPath='')	{
		global $BACK_PATH;

		$backPath = $backPath ? $backPath : $BACK_PATH;
		$thumbScript='thumbs.php';
		$title = $title? ' title="'.htmlspecialchars($title).'"' : '';
		$attribs = $attribs ? ' '.$attribs : '';
		$attribsIcon = $attribsIcon ? ' '.$attribsIcon : $attribs;

			// Check and parse the size parameter
		$sizeParts=array();
		if ($size = trim($size)) {
			$sizeParts = explode('x', $size.'x'.$size);
			if(!intval($sizeParts[0])) $size='';
		}

		$thumbData='';


		$filepath = (t3lib_div::isAbsPath($theFile)?'':'../').$theFile;

		if ($filepath)	{
			$fI = t3lib_div::split_fileref($theFile);
			$ext = $fI['fileext'];
			$max=0;


$uploaddir = '';
			if (t3lib_div::inList('gif,jpg,png',$ext)) {
				$imgInfo=@getimagesize(PATH_site.$uploaddir.$theFile);
				if (is_array($imgInfo))	{$max = max($imgInfo[0],$imgInfo[1]);}
			}
				// use the original image if it's size fits to the thumbnail size
			if ($max && $max<=(count($sizeParts)&&max($sizeParts)?max($sizeParts):56))	{
				$thumbData = '<img src="'.$backPath.$filepath.'" '.$imgInfo[3].' border="0"'.$title.$attribs.' alt="" />';

			} elseif ($ext=='ttf' || t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],$ext)) {
				$params = '&file='.rawurlencode($filepath);
				$params.= $size?'&size='.$size:'';
				$url = $thumbScript.'?&dummy='.$GLOBALS['EXEC_TIME'].$params;
				$thumbData = '<img src="'.$backPath.$url.'" border="0"'.$title.$attribs.' alt="" />';

			} elseif($makeFileIcon) {
				$icon = t3lib_BEfunc::getFileIcon($ext);
				$url = 'gfx/fileicons/'.$icon;
				$thumbData = '<img src="'.$backPath.$url.'" border="0"'.$title.$attribsIcon.' alt="" />';
			}

			$onClick = !is_null($onClick) ? $onClick : 'top.launchView(\''.$filepath.'\',\'\',\''.$backPath.'\');return false;';
			if ($thumbData AND $onClick) {
				$thumbData = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$thumbData.'</a>';
			}
		}

		return $thumbData;
	}


					// Create link to showing details about the file in a window:
//		if (true /*popup*/) {
//			$onClick='top.launchView(\''.$fI['file_name_absolute'].'\',\'\',\''.$GLOBALS['BACK_PATH'].'\');return false;';
//			$ATag_info ='<a href="#" onclick="'.htmlspecialchars($onClick).'">';
//		} else {
//			$Ahref = $GLOBALS['BACK_PATH'].'show_item.php?table='.rawurlencode($fI['file_name_absolute']).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
//			$ATag_info = '<a href="'.htmlspecialchars($Ahref).'">';
//		}


}

// No XCLASS inclusion code: this class shouldn't be instantiated
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guifunc.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_guifunc.php']);
//}
?>