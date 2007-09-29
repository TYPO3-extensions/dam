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
 *   59: class tx_dam_div 
 *   78:     function getRelPath ($path, $mountpath=NULL) 
 *   94:     function getAbsPath ($path) 
 *
 *              SECTION: icons
 *  121:     function fileIcon ($type,$mediaType,$attribs='align="middle"',$collection='18') 
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_types.php');


/**
 * Misc DAM functions
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_div {


//	array of asset items used by some functions to pass meta data
//
//	var $itemArr = array (
//		'some_id' => array (
//				'uid' => $row['uid'],	// if uid is 0 then meta is NOT from a record
//				'meta' => $row,
//				'meta_is_all' => 1, // set if not full record is in meta
//				'errors' => array(
//					0 => array (
//						'major' => $error,	// the main error number
//						'minor' => $details_nr,	// the detail error number
//						'msg' => sprintf($details, $data[0],$data[1],$data[2],$data[3],$data[4]), // this should be set localized if available
//					),
//				)
//			)
//		);

	/**
	 * Extract a list of uid's from an item array
	 * 
	 * @param	array		array of item arrays
	 * @param	boolean		If set a comma list string is returned, otherwise an array
	 * @return	array		List of uid's
	 */
	function getUIDsFromItemArray ($itemArr, $makeList=TRUE) {
		$uidList = array();
		
		foreach ($itemArr as $item) {
			if($item['uid']=intval($item['uid'])) {
				$uidList[$item['uid']] = $item['uid'];
			}
		}
		$uidList = $makeList ? implode(',',$uidList) : $uidList;
		return $uidList;
	}

	/**
	 * Extract an error message from an item
	 * 
	 * @param	array		item array
	 * @return	string		error message
	 */
	function getErrorMsgFromItem ($item) {
		$errMsg = '';
		
		foreach ($itemArr as $item) {
			if(is_array($item['errors'])) {
				$error = end($item['errors']);
				$errMsg = $error['msg'];
			}
		}
		return $errMsg;
	}
	
	/**
	 * Creates a list of indexed files by uid
	 * 
	 * @return	array		Data
	 */
	function compileItemArray($uidList, $res=FALSE)	{
		global $BACK_PATH, $LANG;
		
		$items = array();
		
		if ($res) {
			$GLOBALS['TYPO3_DB']->sql_data_seek($res,0);
		} else {
			$infoFields = tx_dam_db::getInfoFieldListDAM();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($infoFields, 'tx_dam', 'tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($uidList).')');
		}
		
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			
			$items[$row['uid']] = array (
				'uid' => $row['uid'],
				'meta' => $row,
				'meta_is_all' => 0, // set if not full record is in meta
				'errors' => array(),
			);
		}

		return $items;
	}
		
	/***************************************
	 *
	 *	 files folders paths
	 *
	 ***************************************/


	 
	/**
	 * convert a path to a relative path if possible
	 * 
	 * @param	string		Path to convert
	 * @param	string		Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Relative path
	 */
	function getRelPath ($path, $mountpath=NULL) {
		
		$mountpath = is_null($mountpath) ? PATH_site : $mountpath;
		
			// remove the site path from the beginning to make the path relative
			// all other's stay absolute
		return preg_replace('#^'.preg_quote($mountpath).'#','',$path);
	}
	

	/**
	 * Convert a path to an absolute path
	 * 
	 * @param	string		Path to convert
	 * @param	string		Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Absolute path
	 */
	function getAbsPath ($path) {
		if(t3lib_div::isAbsPath($path)) {
			return $path;
		}
		$mountpath = is_null($mountpath) ? PATH_site : $mountpath;
		return $mountpath.$path;
	}



	/**
	 * convert/cleans a file name to be more usable as title
	 * 
	 * @param	string		Filename or similar
	 * @return	string		Title string
	 */
	function makeTitleFromFilename ($title) {
		$extpos = strrpos($title,'.');
		$title= $extpos ? substr($title, 0, $extpos) : $title; // remove extension
		$title=str_replace('_',' ',$title);	// Substituting "_" for " " because many filenames may have this instead of a space char.
		$title=str_replace('%20',' ',$title);		
		return $title;
	}



	/***************************************
	 *
	 *	 Arrays
	 *
	 ***************************************/


	function array_copy($target, $source, $keys='') {
		if (!is_array($keys)) {
			$keys = t3lib_div::trimExplode(',', $keys, 1);
		}
		foreach ($keys as $key) {
			if (isset($source[$key])) {
				$target[$key] = $source[$key];
			}
		}
		return $target;
	}


	/***************************************
	 *
	 *	 icons and GUI
	 *
	 ***************************************/



	/**
	 * get file type <img> tag
	 * $collection is unused
	 * 
	 * @param	[type]		$type: ...
	 * @param	[type]		$mediaType: ...
	 * @param	[type]		$attribs: ...
	 * @param	[type]		$collection: ...
	 * @return	[type]		...
	 */
	function fileIcon ($type,$mediaType,$attribs='align="middle"',$collection='18') {
		global $BACK_PATH;
		
		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];


		if(intval($mediaType)) {
			$mediaType = $TX_DAM['code2media'][$mediaType];
		}
		if(is_object($this->cObj)) {
			$path = $BACK_PATH.t3lib_extMgm::siteRelPath('dam').'i/'.$collection.'/';
		} else {
			$path = $BACK_PATH.PATH_txdam_rel.'i/'.$collection.'/';
		}
		$filePath = t3lib_extMgm::extPath('dam').'i/'.$collection.'/';

		if (@is_readable($filePath.$type.'.gif')) {
			$file =$type.'.gif';
		} else {
			$file ='mtype_'.$mediaType.'.gif';
		}

		return '<img src="'.$path.$file.'" width="18" height="16" hspace="2" border="0" title="'.htmlspecialchars($mediaType.'/'.$type).'" '.$attribs.' alt="" />';
	}

   /**
	 * Returns a media type icon from a record
	 * 
	 * @param	array		Record array
	 * @param	boolean		If set the name of the media type is printed below the icon
	 * @return	string		Rendered icon
	 */	
	function mediatypeIcon($row, $iconPlusType=TRUE) {
		global $LANG, $BACK_PATH;
		
		$label=t3lib_befunc::getLabelFromItemlist('tx_dam', 'media_type', $row['media_type']);

		list($iconname) = explode('|',strtolower($label));
		$label = strtoupper(trim($LANG->sL($label)));
			
		if(!$iconPlusType) {
			$title = ' title="'.htmlspecialchars($label).'"';
		}
		
		$icon = '<img src="'.$BACK_PATH.PATH_txdam_rel.'i/media-'.$iconname.'.png'.'" border="0"'.$title.' />';
		
		if($iconPlusType) {
			$icon = '<div style="text-align:center;">'.$icon.'<br /><span style="color: #555;">'.htmlspecialchars($label).'</span></div>';
		}
		
		return $icon;
	}

	
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
	
 
    /**
	 * Returns a table with some info and a thumbnail from a record
	 * 
	 * @param	array		Record array
	 * @return	string		HTNL content
	 */
    function getDAMRecordInfo($row) {
    	global $LANG;
		
		$content = '';
		
		$icon = tx_dam_div::mediatypeIcon($row);
		
		$content.= '
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" width="1%">'.$icon.'</td>
					<td valign="top" align="left" style="padding-left:20px;">';

		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:lang/locallang_general.php:LGL.title').'</strong><br />'.
					htmlspecialchars($row['title']).'</div>';

		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.php:tx_dam_item.file_name').'</strong><br />'.
					htmlspecialchars($row['file_name']).'</div>';
					
		$content.=	'<div style="margin-bottom:7px;"><strong>'.$LANG->sL('LLL:EXT:dam/locallang_db.php:tx_dam_item.file_path').'</strong><br />'.
					htmlspecialchars($row['file_path']).'</div>';

		if ($row['media_type'] == 2) {
			$out = '';
			$out.= $row['hpixels']? $row['hpixels'].'x'.$row['vpixels'].' px, ' :'';
			$out.= t3lib_div::formatSize($row['file_size']);
			$out.= $row['color_space'] ? ', '.$LANG->sL(t3lib_befunc::getLabelFromItemlist($PA['table'],'color_space',$row['color_space'])) : "";

			$content.=	'<div style="margin-bottom:7px;"><nobr>'.htmlspecialchars($out).'</nobr></div>';
		}

		$content.= '
					</td>';
					
		$thumb = tx_dam_SCbase::getDia($row, 115, 5, $showElements='', $onClick=NULL, $makeIcon=FALSE);
		$content.= '
					<td valign="top" width="1%" style="padding-left:25px;">'.$thumb.'</td>';	
					
		$content.= '
				</tr>
			</table>';
			
		return '<div style="margin:0px;">'.$content.'</div>';
    }

	/*
$txdamTypes['media2Codes'] = array (
	'undefined' => '0',
	'text' => '1',
	'image' => '2',
	'audio' => '3',
	'video' => '4',
	'interactive' => '5',
	'service' => '6',
	'font' => '7',
	'model' => '8',
	'dataset' => '9',
	'collection' => '10',
	'software' => '11',
	'application' => '12',
);
*/

	
    /**
	 * Returns a linked icon with title from a record
	 * 
	 * @param	string		Table name (tt_content,...)
	 * @param	array		Record array
	 * @param	boolean		For pages records the rootline will be rendered
	 * @return	string		Rendered icon
	 */
    function getItemFromRecord($refTable, $row, $showRootline=FALSE) {
        global $BACK_PATH, $LANG, $TCA, $SOBE;

        $iconAltText = t3lib_BEfunc::getRecordIconAltText($row, $refTable);

            // Prepend table description for non-pages tables
        if(!($refTable=='pages')) {
            $iconAltText = htmlspecialchars($LANG->sl($TCA[$refTable]['ctrl']['title']).': ').$iconAltText;
        }

            // Create record title or rootline for pages if option is selected
        if($refTable=='pages' AND $showRootline) {
            $elementTitle = t3lib_BEfunc::getRecordPath($row['uid'], '1=1', 0);
            $elementTitle = t3lib_div::fixed_lgd_cs($elementTitle, -($BE_USER->uc['titleLen']));
        } else {
            $elementTitle = t3lib_BEfunc::getRecordTitle($refTable, $row, 1);
        }

            // Create icon for record
        $elementIcon = t3lib_iconworks::getIconImage($refTable, $row, $BACK_PATH, 'class="c-recicon" title="'.$iconAltText.'"');

            // Return item with edit link
        return tx_dam_SCbase::wrapLink_edit($elementIcon. $elementTitle, $refTable, $row['uid']);
    }	
}

// No XCLASS inclusion code: this class shouldn't be instantiated
//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_div.php'])    {
//    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_div.php']);
//}
?>