<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * DAM file listing class
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  113: class tx_dam_listbase
 *
 *              SECTION: Setup
 *  264:     function tx_dam_listbase()
 *  275:     function __construct()
 *  296:     function clearColumns()
 *  308:     function addColumn($name, $label)
 *  324:     function removeColumn($name)
 *  338:     function setCurrentSorting ($sortField, $sortRev)
 *  351:     function setParameterNames ($sortField, $sortRev)
 *  363:     function setPointer($pointer)
 *
 *              SECTION: Set data
 *  384:     function addData($dataObject, $idName='')
 *
 *              SECTION: Table rendering
 *  403:     function getListTable()
 *  443:     function renderTable()
 *  462:     function renderHeader ()
 *  490:     function renderList()
 *  553:     function renderFooter ()
 *
 *              SECTION: Column rendering
 *  571:     function getItemColumns ($item)
 *  608:     function getItemAction ($item)
 *  619:     function getItemIcon ($item)
 *
 *              SECTION: Row rendering
 *  659:     function addRow($setup, $position='')
 *  736:     function addRowRaw ($content)
 *
 *              SECTION: Controls
 *  756:     function getHeaderControl()
 *  767:     function getHeaderColumnControl($field)
 *  778:     function getItemControl($item)
 *
 *              SECTION: Browsing
 *  799:     function addRowBrowse($type)
 *  819:     function fwd_rwd_HTML($type)
 *
 *              SECTION: Link and title rendering
 *  858:     function cropTitle ($title, $field)
 *  874:     function linkWrapDir($title, $path)
 *  892:     function linkWrapFile($title, $pathInfo)
 *  925:     function linkWrapSort($title, $column)
 *
 *              SECTION: Misc
 *  972:     function thumbnailPossible ($item)
 *  989:     function getThumbnail($filepath, $addAtrr='', $size='')
 * 1000:     function getFilePermString ($perms)
 *
 *              SECTION: Clipboard
 * 1074:     function clipboard_linkHeaderIcon($string, $table, $cmd, $warning='')
 * 1086:     function clipboard_getItemControl($columns)
 * 1131:     function clipboard_getHeaderControl ()
 *
 * TOTAL FUNCTIONS: 34
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Class for rendering of Media>File>List
 * The class is not really abstract but on a good way to become so ...
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 */
class tx_dam_listbase {



	/**
	 * enable display of thumbnails for images
	 */
	var $showThumbs = true;

	/**
	 * if set field wrap is enabled
	 */
	var $enableFieldWrap = false;

	/**
	 * if set sorting by clicking on the header is possible
	 */
	var $enableSorting = true;

	/**
	 * if set file links will be created to show the file in popup window
	 */
	var $enableFilePopup = true;

	/**
	 * If enabled titles will not be shortend to $titleLength but 200 and field wrap for title will be eneabled
	 */
	 var $showfullTitle = false;

	/**
	 * max title length if field wrap is disabled
	 */
	var $titleLength = 30;

	/**
	 * enable alternating background colors in table rows
	 */
	var $showAlternateBgColors = false;

	/**
	 * enable display of action column which is the first
	 */
	var $showActions = false;

	/**
	 * enable display of icon column which is the second
	 */
	var $showIcons = true;

	/**
	 * name of the thumbnail script
	 */
	var $thumbScript = '';


	/**
	 * defines the columns to display and provide a language label
	 */
	var $columnList = array();

	/**
	 * defines the key of the title column for columnList
	 */
	var $titleColumnKey;

	/**
	 * The current sorting field
	 * Just for display
	 */
	var $sortField = '';

	/**
	 * Defines if reverse sorting is enabled or not
	 * Just for display
	 */
	var $sortRev = false;

	/**
	 * Keys are fieldnames and values are td-parameters to add in addRow();
	 */
	var $columnTDAttr = array();

	/**
	 * stores html table rows
	 */
	var $tableRows = array();

	/**
	 * array of control names that are enabled for display
	 */
	var $showControls = array();

	/**
	 * array of parameter names used in links
	 */
	var $paramName = array();

	/**
	 * additional attributes for some elements
	 */
	 var $elementAttr = array(
	 	'table' => ' border="0" cellpadding="0" cellspacing="0" style="width:100%" class="typo3-dblist"',
		'headerTD' => ' nowrap="nowrap" class="c-headLine"',
		'itemTD' => ' class="typo3-dblist-item"',
		'actionTD' => ' width="1%" valign="top" align="left" nowrap="nowrap"',
		'iconTD' => ' width="1%" valign="top" align="left" nowrap="nowrap"',
		'dataTD' => ' valign="top"',

	 );

	/**
	 * additional css styles for some elements
	 */
	 var $elementStyle = array(
	 	'table' => '',
		'headerTD' => 'border-bottom:1px solid #888;',
		'itemTD' => '',
		'actionTD' => 'padding: 3px 0px 0px 5px;',
		'iconTD' => 'padding-left:5px;',
		'dataTD' => 'padding-left:5px;',

	 );


###### Clipboard - todo

	/**
	 * If true click menus are generated on files and folders
	 */
	var $clickMenus = false;
	var $clipBoard = false;
	var $CBnames = array();

##################



	/***************************************
	 *
	 *	 Setup
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_listbase()	{
		$this->__construct();
	}


	/**
	 * Initialize the object
	 * PHP5 constructor
	 *
	 * @return	void
	 */
	function __construct() {
		global $BE_USER;

		if (!$GLOBALS['TYPO3_CONF_VARS']['GFX']['thumbnails'])	{
			$this->thumbScript = 'gfx/notfound_thumb.gif';
		} else {
			$this->thumbScript = $GLOBALS['BACK_PATH'].'thumbs.php';
		}
		$this->showThumbs = $BE_USER->uc['thumbnailsByDefault'];

		$this->colorTRHover = $GLOBALS['SOBE']->doc->hoverColorTR ? $GLOBALS['SOBE']->doc->hoverColorTR : t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-20,-20,-20);
		$this->colorTREven = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-5,-5,-5);
		$this->colorTROdd = t3lib_div::modifyHTMLcolor($GLOBALS['SOBE']->doc->bgColor,-10,-10,-10);
	}


	/**
	 * Clears the list of columns for display
	 *
	 * @return	void
	 */
	function clearColumns() {
		$this->columnList = array();
	}


	/**
	 * Add a columns for display
	 *
	 * @param	string		$name Column field name
	 * @param	string		$label Language label for header
	 * @return	void
	 */
	function addColumn($name, $label) {
		$this->columnList[$name] = array(
				'name' => $name,
				'label' => $label,
			);
		reset($this->columnList);
		$this->titleColumnKey = key($this->columnList);
	}


	/**
	 * Removes a column
	 *
	 * @param	string		$name Column field name
	 * @return	void
	 */
	function removeColumn($name) {
		unset($this->columnList[$name]);
		reset($this->columnList);
		$this->titleColumnKey = key($this->columnList);
	}


	/**
	 * Set the current sorting definition
	 *
	 * @param	string		$sortField Column field name
	 * @param	boolean		$sortRev Forward or reverse sorting
	 * @return	void
	 */
	function setCurrentSorting ($sortField, $sortRev) {
		$this->sortField = $sortField;
		$this->sortRev = $sortRev;
	}


	/**
	 * Defines the sorting parameter names used for links
	 *
	 * @param	string		$sortField
	 * @param	boolean		$sortRev
	 * @return	void
	 */
	function setParameterNames ($sortField, $sortRev) {
		$this->paramName['sortField'] = $sortField;
		$this->paramName['sortRev'] = $sortRev;
	}


	/**
	 * Set the pointer object
	 *
	 * @param	object		$pointer
	 * @return	void
	 */
	function setPointer($pointer) {
		$this->pointer = $pointer;
	}




	/***************************************
	 *
	 *	 Set data
	 *
	 ***************************************/


	/**
	 * Set data objects which provides the data to display
	 *
	 * @param	object		$dataObject data object. Eg. tx_dam_dir
	 * @param	mixed		$idName Key which is used to store the object in $this->dataObjects[$idName]
	 * @return	void
	 */
	function addData($dataObject, $idName='')	{
		$idName = $idName ? $idName : uniqid('tx_dam_listbase');
		$this->dataObjects[$idName] = $dataObject;
	}



	/***************************************
	 *
	 *	 Table rendering
	 *
	 ***************************************/


	/**
	 * Returns a table with directories and files listed.
	 *
	 * @return	string		HTML-table
	 */
	function getListTable()	{


			// add rewind browse button
		$this->addRowBrowse('rwd');

			// add item list or empty row
		if($this->pointer->countTotal) {
			$this->renderList();
		} else {
			$this->addRow(array(
							'data' => array($this->titleColumnKey => '&nbsp;',
							'tdStyle' => 'border-bottom:1px solid #888;'
						)));
		}

			// add forward browse button
		$this->addRowBrowse('fwd');

			// add bottom line
#		$this->addRow(array('tdStyle' => 'border-top:1px solid #888;'));


			// add header - column titles with sorting links
			// this is added after the list is rendered because we might need info's from the list
		$this->renderHeader();

			// add footer (eg. counter) info line
		$this->renderFooter();

			// wrap the table around and return HTML
		return $this->renderTable();
	}


	/**
	 * Returns a table with items listed.
	 *
	 * @return	string		HTML-table
	 */
	function renderTable()	{

		return '


		<!--
			list table:
		-->
			<table '.$this->elementAttr['table'].'>
				'.implode('', $this->tableRows).'
			</table>';
	}


	/**
	 * Adds a header row with column titles with sorting links
	 *
	 * @return	void
	 */
	function renderHeader () {
		$columns = array();
		foreach($this->columnList as $field => $descr)	{
			if ($field == '_CLIPBOARD_' AND is_object($this->clipboard))	{
				$columns[$field] = $this->clipboard->getHeaderControl();
			} elseif ($field == '_CONTROL_')	{
				$columns[$field] = $this->getHeaderControl();
			} else {
				$columns[$field] = $this->getHeaderColumnControl($field);
				$columns[$field] .= $this->linkWrapSort($descr['label'], $field);
			}
			if ($columns[$field] == '') {
				$columns[$field] = '&nbsp;';
			}
		}
		$this->addRow(array(
				'data' => $columns,
				'tdAttribute' => $this->elementAttr['headerTD'],
				'tdStyle' => $this->elementStyle['headerTD'],
			), 'top');
	}


	/**
	 * This renders tablerows for the directory
	 *
	 * @return	void
	 */
	function renderList()	{
		$allItemCount = 0;
		$pageItemCounter = 0;

		foreach ($this->dataObjects as $list) {

			if ($list->count())	{

				$tdStyleAppend = '';

				while ($list->valid()) {

					$item = $list->current();

					$allItemCount++;

					if (($allItemCount > $this->pointer->firstItemNum) AND ($pageItemCounter < $this->pointer->itemsPerPage))	{

						$pageItemCounter++;

							// 	Columns rendering
						$itemAction = $this->getItemAction ($item);
						$itemIcon = $this->getItemIcon ($item);
						$itemColumns = $this->getItemColumns ($item);

						# $trStyle = '';
						$trStyle = ' background-color:'.$this->colorTREven.';';
						if ($this->showAlternateBgColors) {
							if ($allItemCount % 2) {
								$trStyle = ' background-color:'.$this->colorTREven.';';
							}
							else {
								$trStyle = ' background-color:'.$this->colorTROdd.';';
							}
						}

							// this is the last line which should have a line afterwards
						if (($allItemCount > $this->pointer->lastItemNum) OR ($pageItemCounter >= $this->pointer->itemsPerPage)) {
							$tdStyleAppend = ' border-bottom:1px solid #888;';
						}

						$this->addRow(	array(
								'action' => $itemAction,
								'icon' => $itemIcon,
								'data' => $itemColumns,
								'tdAttribute' => $this->elementAttr['itemTD'],
								'tdStyle' => $this->elementStyle['itemTD'].$tdStyleAppend,
								'trStyle' => $trStyle,
								'trHover' => true,
							));
					}
					$list->next();
				}
			}
		}
	}


	/**
	 * Add a footer to the table
	 *
	 * @return	void
	 */
	function renderFooter () {
	}



	/***************************************
	 *
	 *	 Column rendering
	 *
	 ***************************************/


	/**
	 * Renders the data columns
	 *
	 * @param	array		$item item array
	 * @return	array
	 */
	function getItemColumns ($item) {
		$columns = array();
		foreach($this->columnList as $field => $descr)	{

			switch($field)	{
				case '_CLIPBOARD_':
					if(is_object($this->clipboard)) {
						$columns[$field] = $this->clipboard->getItemControl($item);
					}
				break;
				case '_CONTROL_':
					 $columns[$field] = $this->getItemControl($item);
					 $this->columnTDAttr[$field] = ' nowrap="nowrap"';
				break;
				default:
					$columns[$field] = htmlspecialchars(t3lib_div::fixed_lgd($item[$field], $this->titleLength));
				break;
			}
			if ($columns[$field] == '') {
				$columns[$field] = '&nbsp;';
			}
		}

			// Thumbsnails?
		if ($this->showThumbs AND $this->thumbnailPossible($item))	{
			$columns['title'] .= '<div style="margin:2px 0 2px 0;">'.$this->getThumbNail($item['file_path_absolute'].$item['file_name']).'</div>';
		}
		return $columns;
	}


	/**
	 * Renders the action
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemAction ($item) {
		return '';
	}


	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon ($item) {
		return '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/i/default_gray1.gif', 'width="18" height="16"').' alt="" />';
	}



	/***************************************
	 *
	 *	 Row rendering
	 *
	 ***************************************/


	/**
	 * Returns a table-row with the content from the fields in the input data array.
	 * OBS: $this->columnList MUST be set! (represents the list of fields to display)
	 *
	 * Paramater have to be passed as array:
	 * 	$setup = array(
	 * 	'action' => '',
	 * 	'icon' => '',
	 * 	'data' => '',
	 * 	'tdAttribute' => '',
	 * 	'tdStyle' => '',
	 * 	'trStyle' => '',
	 * 	'trHover' => '',
	 * 	);
	 *
	 * param	string		$action Could be a checkbox or button as action for this element. Leave blank if not needed. (global enable/disable with $this->showAction)
	 * param	string		$icon is the <img>+<a> of the entry.
	 * param	array		$data is the data array, record with the fields. Notice: These fields are (currently) NOT htmlspecialchar'ed before being wrapped in <td>-tags
	 * param	string		$tdAttribute is inserted in the <td>-tags.
	 * param	string		$tdStyle is inserted in the <td>-tags as additional css style.
	 * param	string		$trStyle is inserted in the <tr>-tag as additional css style. Might inlcude 'background-color' which will be detected for use as default color when tr-hover is enabled.
	 * param	boolean		$trHover If set hover color is enabled for the row;
	 *
	 * @param	array		$setupSetup array
	 * @param	string		$position If position is 'top' the line will be inserted on top of the table
	 * @return	string		HTML content for the table row
	 */
	function addRow($setup, $position='')	{

		$action = '';
		$icon = '';
		$data = array();
		$tdAttribute = '';
		$tdStyle = '';
		$trStyle = '';
		$trHover = false;
		extract ($setup, EXTR_IF_EXISTS);

		$td = array();

		$tdAttribute = $tdAttribute ? ' '.$tdAttribute : '';

			// Show action
		if ($this->showActions)	{
			$td[] = '
			<td'.$this->elementAttr['actionTD'].$tdAttribute.' style="'.$this->elementStyle['actionTD'].$tdStyle.'">'.
			($action ? $action : '<span><br /></span>').
			'</td>';
		}

			// Show icon
		if ($this->showIcons)	{
			$td[] = '
			<td'.$this->elementAttr['iconTD'].$tdAttribute.' style="'.$this->elementStyle['iconTD'].$tdStyle.'">'.
			($icon ? $icon : '<span><br /></span>').
			'</td>';
		}

			// Traverse field array which contains the data to present:
		foreach($this->columnList as $field => $descr)	{

			if ($field=='_CONTROL_') {
				$tdAttribute .= ' width="1%"';
			}

			if(isset($this->columnNoWrap[$field])) {
				$noWrap = ($this->columnNoWrap[$field]) ? ' nowrap="nowrap"' : '';
			}
			else {
				$noWrap = ($this->enableFieldWrap) ? '' : ' nowrap="nowrap"';
			}

			$td[] = '
				<td '.$this->elementAttr['dataTD'].' style="'.$this->elementStyle['dataTD'].$tdStyle.'"'.
				$noWrap.
				$tdAttribute.
				$this->columnTDAttr[$field].
				'>'.($data[$field] ? $data[$field] : '<span><br /></span>').'</td>';
		}

			// make hover for TR
		$match = array();
		preg_match('/background-color[ ]*:[ ]*(#[0-9a-f]+)/', $trStyle, $match);
		$trHover = $trHover ? (' onmouseover="this.style.backgroundColor = \''.$this->colorTRHover.'\';" onmouseout="this.style.backgroundColor = \''.$match[1].'\';"') : '';
		$trStyle = $trStyle ? ' style="'.$trStyle.'"' : '';

		$out='
		<!-- Element, begin: -->
		<tr'.$trStyle.$trHover.'>'.implode('', $td).'</tr>';

		if ($position == 'top') {
			array_unshift($this->tableRows, $out);
		} else {
			$this->tableRows[] = $out;
		}
	}


	/**
	 * Add a list row as raw eg HTML
	 *
	 * @param	string		$content
	 * @return	void
	 */
	function addRowRaw ($content) {
		$this->tableRows[] = $content;
	}





	/***************************************
	 *
	 *	 Controls
	 *
	 ***************************************/


	/**
	 * Creates the control panel for the header.
	 *
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderControl() {
		return '';
	}


	/**
	 * Creates the column control panel for the header.
	 *
	 * @param	string		$field Column key
	 * @return	string		control panel (unless disabled)
	 */
	function getHeaderColumnControl($field) {
		return '';
	}


	/**
	 * Creates the control panel for a single record in the listing.
	 *
	 * @param	array		The record for which to make the control panel.
	 * @return	string		HTML table with the control panel (unless disabled)
	 */
	function getItemControl($item)	{
		return '';
	}





	/***************************************
	 *
	 *	 Browsing
	 *
	 ***************************************/


	/**
	 * Creates a forward/reverse button based on the status of $this->pointer
	 *
	 * @param	string		$type Type name: fwd, rwd
	 * @return	void
	 */
	function addRowBrowse($type)	{
		$columns = array();
		if ($type=='fwd')	{
			if($this->pointer->lastItemNum < ($this->pointer->countTotal-1)) {
				$columns[$this->titleColumnKey] = $this->fwd_rwd_HTML('fwd');
				$this->addRow(array('data' => $columns));
			}
		} elseif ($this->pointer->page) {
			$columns[$this->titleColumnKey] = $this->fwd_rwd_HTML('rwd');
			$this->addRow(array('data' => $columns));
		}
	}


	/**
	 * Creates the button with link to either forward or reverse
	 *
	 * @param	string		Type: "fwd" or "rwd"
	 * @return	string		HTML
	 */
	function fwd_rwd_HTML($type)	{
		$content = '';

		switch($type)	{
			case 'fwd':
				$href = t3lib_div::linkThisScript(array($this->pointer->pagePointerParamName => $this->pointer->getPagePointer(1)));
				$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pildown.gif', 'width="14" height="14"').' alt="" />';
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						$icon.
						'</a> <i>['.($this->pointer->lastItemNum+1).' - '.min($this->pointer->lastItemNum + 1 + $this->pointer->itemsPerPage, $this->pointer->countTotal).']</i>';
			break;
			case 'rwd':
				$href = t3lib_div::linkThisScript(array($this->pointer->pagePointerParamName => $this->pointer->getPagePointer(-1)));
				$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilup.gif', 'width="14" height="14"').' alt="" />';
				$content = '&nbsp;<a href="'.htmlspecialchars($href).'">'.
						$icon.
						'</a> <i>['.max(1, $this->pointer->firstItemNum - $this->pointer->itemsPerPage).' - '.($this->pointer->firstItemNum - 1).']</i>';
			break;
		}
		return $content;
	}




	/***************************************
	 *
	 *	 Link and title rendering
	 *
	 ***************************************/


	/**
	 * Crop the title to adefined lenght or set the wrapping off for long titles
	 *
	 * @param	string		$title Title string
	 * @param	string		$field Field name needed to disbale wrapping if needed
	 * @return	string		Title string
	 */
	function cropTitle ($title, $field) {
		$title = t3lib_div::fixed_lgd_cs($title, ($this->showfullTitle ? 200: $this->titleLength));
		if($this->showfullTitle) {
			$this->columnNoWrap[$field] = false;
		}
		return $title;
	}


	/**
	 * Wraps the directory-titles
	 *
	 * @param	string		$title String to be wrapped in links
	 * @param	string		$path Path
	 * @return	string		HTML
	 */
	function linkWrapDir($title, $path)	{
		$href = t3lib_div::linkThisScript(array($this->paramName['setFolder'] => $path));
			// Sometimes $title contains plain HTML tags. In such a case the string should not be modified!
		if(!strcmp($title,strip_tags($title)))	{
			return '<a href="'.htmlspecialchars($href).'" title="'.htmlspecialchars($title).'">'.htmlspecialchars($title).'</a>';
		} else	{
			return '<a href="'.htmlspecialchars($href).'">'.$title.'</a>';
		}
	}


	/**
	 * Wraps filenames in links which opens them in a window IF they are in web-path.
	 *
	 * @param	string		$title String to be wrapped in link
	 * @param	string		$pathInfo
	 * @return	string		A tag
	 */
	function linkWrapFile($title, $pathInfo)	{

		if (!$this->enableFilePopup) {
			return htmlspecialchars($title);
		}

		if(!isset($pathInfo['file_path_absolute'])) {
			$pathInfo['file_path_absolute'] = tx_dam::path_makeAbsolute($pathInfo['file_path']);
		}

		if (t3lib_div::isFirstPartOfStr($pathInfo['file_path_absolute'], PATH_site))	{

			$href = tx_dam::file_relativeSitePath ($pathInfo['file_path_absolute'].$pathInfo['file_name']);
			$aOnClick = "return top.openUrlInWindow('".t3lib_div::getIndpEnv('TYPO3_SITE_URL').$href."','WebFile');";

			if(!strcmp($title,strip_tags($title)))	{
				return '<a href="'.htmlspecialchars($href).'" onclick="'.htmlspecialchars($aOnClick).'" title="'.htmlspecialchars($title).'">'.htmlspecialchars($title).'</a>';
			} else	{
				return '<a href="'.htmlspecialchars($href).'" onclick="'.htmlspecialchars($aOnClick).'">'.$title.'</a>';
			}
		}

		return $title;
	}


	/**
	 * Wraps the directory-titles ($code) in a link to file_list.php (id = $path) and sorting commands...
	 *
	 * @param	string		$title String to be wrapped
	 * @param	string		$column Column field name
	 * @return	string		HTML
	 */
	function linkWrapSort($title, $column)	{
		$content = '';

		if ($this->enableSorting) {
			if ($this->sortField == $column AND !$this->sortRev)	{		// reverse sorting
				$params = array($this->paramName['sortField'] => $column, $this->paramName['sortRev'] => '1');
			} else {
				$params = array($this->paramName['sortField'] => $column, $this->paramName['sortRev'] => '0');
			}

			$href = t3lib_div::linkThisScript($params);
			$sortArrow = ($this->sortField == $column? '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/red'.($this->sortRev?'up':'down').'.gif', 'width="7" height="4"').' style="padding:0px 3px 0px 3px;" alt="" />':'');
			$content .= '<a href="'.htmlspecialchars($href).'">'.htmlspecialchars($title).'</a>'.$sortArrow;


				// remove sorting
			if ($this->sortField == $column) {

				$params = array($this->paramName['sortField'] => '-', $this->paramName['sortRev'] => '0');
				$href = t3lib_div::linkThisScript($params);
				$content .= '<a href="'.htmlspecialchars($href).'">'.'<img'.t3lib_iconWorks :: skinImg($GLOBALS['BACK_PATH'], 'gfx/close.gif', 'width="11" height="10"').' title="'.$GLOBALS['LANG']->getLL('defaultSorting',1).'" alt="" />'.'</a>';
			}
		} else {
			$content .= htmlspecialchars($title);
		}

		return $content;

	}





	/***************************************
	 *
	 *	 Misc
	 *
	 ***************************************/


	/**
	 * Checks if a thumbnail can be generated for a file
	 *
	 * @param	array		$item	Fileinfo array
	 * @return	boolean
	 */
	function thumbnailPossible ($item) {
		$thumbnailPossible = false;
		if( ($item['media_type']==TXDAM_mtype_image) AND t3lib_div::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], $item['file_type'])) {
			$thumbnailPossible = true;
		}
		return $thumbnailPossible;
	}


	/**
	 * Returns single image tag to thumbnail using a thumbnail script (like thumbs.php)
	 *
	 * @param	string		$filepath must be the proper reference to the file thumbs.php should show
	 * @param	string		$addAtrr are additional attributes for the image tag
	 * @param	integer		$size is the size of the thumbnail send along to "thumbs.php"
	 * @return	string		Image tag
	 */
	function getThumbnail($filepath, $addAtrr='', $size='')	{
		return t3lib_BEfunc::getThumbNail($this->thumbScript, $filepath, $addAtrr, $size);
	}


	/**
	 * Returns unix like string of file permission
	 *
	 * @param	integer		$perms Permissions eg from fileperms()
	 * @return	string		Eg. rwxr-x---
	 */
	function getFilePermString ($perms) {
		if (($perms & 0xC000) == 0xC000) {
			// Socket
			$info = 's';
		 } elseif (($perms & 0xA000) == 0xA000) {
			// Symbolic Link
			$info = 'l';
		 } elseif (($perms & 0x8000) == 0x8000) {
			// Regular
			$info = '-';
		 } elseif (($perms & 0x6000) == 0x6000) {
			// Block special
			$info = 'b';
		 } elseif (($perms & 0x4000) == 0x4000) {
			// Directory
			$info = 'd';
		 } elseif (($perms & 0x2000) == 0x2000) {
			// Character special
			$info = 'c';
		 } elseif (($perms & 0x1000) == 0x1000) {
			// FIFO pipe
			$info = 'p';
		 } else {
			// Unknown
			$info = 'u';
		 }

		 // Owner
		 $info .= (($perms & 0x0100) ? 'r' : '-');
		 $info .= (($perms & 0x0080) ? 'w' : '-');
		 $info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));

		 // Group
		 $info .= (($perms & 0x0020) ? 'r' : '-');
		 $info .= (($perms & 0x0010) ? 'w' : '-');
		 $info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));

		 // World
		 $info .= (($perms & 0x0004) ? 'r' : '-');
		 $info .= (($perms & 0x0002) ? 'w' : '-');
		 $info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}


// TODO Clipboard ######################




	/***************************************
	 *
	 *	 Clipboard
	 *
	 ***************************************/




	/**
	 * Wrapping input string in a link with clipboard command.
	 *
	 * @param	string		String to be linked - must be htmlspecialchar'ed / prepared before.
	 * @param	string		table - NOT USED
	 * @param	string		"cmd" value
	 * @param	string		Warning for JS confirm message
	 * @return	string		Linked string
	 */
	function clipboard_linkHeaderIcon($string, $table, $cmd, $warning='')	{
		$onClickEvent = 'document.dblistForm.cmd.value=\''.$cmd.'\';document.dblistForm.submit();';
		if ($warning)	$onClickEvent = 'if (confirm('.$GLOBALS['LANG']->JScharCode($warning).')){'.$onClickEvent.'}';
		return '<a href="#" onclick="'.htmlspecialchars($onClickEvent).'return false;">'.$string.'</a>';
	}

	/**
	 * Creates the clipboard control pad
	 *
	 * @param	array		Array with information about the file/directory for which to make the clipboard panel for the listing.
	 * @return	string		HTML-table
	 */
	function clipboard_getItemControl($columns)	{
return;
		$cells=array();
		$fullIdent = $columns['path'].$columns['file'];
		$md5=t3lib_div::shortmd5($fullIdent);

			// For normal clipboard, add copy/cut buttons:
		if ($this->clipObj->current=='normal')	{
			$isSel = $this->clipObj->isSelected('_FILE', $md5);
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->selUrlFile($fullIdent,1,($isSel=='copy'))).'">'.
						'<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_copy'.($isSel=='copy'?'_h':'').'.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.copy',1).'" alt="" />'.
						'</a>';
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->selUrlFile($fullIdent,0,($isSel=='cut'))).'">'.
						'<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_cut'.($isSel=='cut'?'_h':'').'.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:cm.cut',1).'" alt="" />'.
						'</a>';
		} else {	// For numeric pads, add select checkboxes:
			$n='_FILE|'.$md5;
			$this->CBnames[] = $n;

			$checked = ($this->clipObj->isSelected('_FILE', $md5)?' checked="checked"':'');
			$cells[]='<input type="hidden" name="CBH['.$n.']" value="0" />'.
					'<input type="checkbox" name="CBC['.$n.']" value="'.htmlspecialchars($fullIdent).'" class="smallCheckboxes"'.$checked.' />';
		}

			// Display PASTE button, if directory:
		$elFromTable = $this->clipObj->elFromTable('_FILE');
		if (@is_dir($fullIdent) AND count($elFromTable))	{
			$cells[]='<a href="'.htmlspecialchars($this->clipObj->pasteUrl('_FILE', $fullIdent)).'" onclick="return '.htmlspecialchars($this->clipObj->confirmMsg('_FILE', $fullIdent, 'into', $elFromTable)).'">'.
						'<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_pasteinto.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_pasteInto',1).'" alt="" />'.
						'</a>';
		}

			// Compile items into a DIV-element:
		return '							<!-- CLIPBOARD PANEL: -->
											<div class="typo3-clipCtrl">
												'.implode('
												', $cells).'
											</div>';
	}

	/**
	 * Creates the clipboard header control
	 *
	 * @return	string HTML content
	 */
	function clipboard_getHeaderControl () {
		return '';
				$cells = array();
				$table = '_FILE';
				$elFromTable = $this->clipObj->elFromTable($table);
				if (count($elFromTable))	{
					$cells[]='<a href="'.htmlspecialchars($this->clipObj->pasteUrl('_FILE', $this->path)).'" onclick="return '.htmlspecialchars($this->clipObj->confirmMsg('_FILE', $this->path, 'into', $elFromTable)).'">'.
						'<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_pasteafter.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_paste',1).'" alt="" /></a>';
				}
				if ($this->clipObj->current!='normal' AND $this->pointer->countTotal)	{
					$cells[] = $this->clipboard_linkHeaderIcon('<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_copy.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_selectMarked',1).'" alt="" />', $table, 'setCB');
					$cells[] = $this->clipboard_linkHeaderIcon('<img'.t3lib_iconWorks::skinImg('', 'gfx/garbage.gif', 'width="11" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_deleteMarked',1).'" alt="" />', $table, 'delete', $GLOBALS['LANG']->getLL('clip_deleteMarkedWarning'));
					$onClick = 'checkOffCB(\''.implode(',', $this->CBnames).'\'); return false;';
					$cells[]='<a href="#" onclick="'.htmlspecialchars($onClick).'">'.
							'<img'.t3lib_iconWorks::skinImg('', 'gfx/clip_select.gif', 'width="12" height="12"').' title="'.$GLOBALS['LANG']->getLL('clip_markRecords',1).'" alt="" />'.
							'</a>';
				}

				return implode('', $cells);
	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listbase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_listbase.php']);
}
?>