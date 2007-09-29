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
 * DAM nav frame.
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
 *   73: class tx_dam_navframe 
 *   89:     function init()	
 *  111:     function jumpTo(params,linkObj,highLightID)	
 *  127:     function refresh_nav()	
 *  136:     function _refresh_nav()	
 *  179:     function main()	
 *  207:     function printContent()	
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */



unset($MCONF);
include ('conf.php');
include ($BACK_PATH.'init.php');
include ($BACK_PATH.'template.php');



define('PATH_txdam', t3lib_extMgm::extPath('dam'));

require_once(PATH_txdam.'lib/class.tx_dam_div.php');
require_once(PATH_txdam.'lib/class.tx_dam_browsetrees.php');


/**
 * Main script class for the tree navigation frame
 * 
 * @author	@author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_navframe {

	var $folderTree;
	var $categoryTree;
	var $typeTree;
	var $statusTree;
	var $mimeTree;
	var $mediaTree;

	var $doc;
	var $content;
	
		// Internal, static: _GP
	var $currentSubScript;

		// Constructor:
	function init()	{
		global $AB,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$HTTP_GET_VARS,$HTTP_POST_VARS,$CLIENT,$TYPO3_CONF_VARS;

		$this->currentSubScript = t3lib_div::_GP('currentSubScript');
		
			// Setting highlight mode:
		$this->doHighlight = !$BE_USER->getTSConfigVal('options.pageTree.disableTitleHighlight');
		
		
			// the trees
		$this->browseTrees = t3lib_div::makeInstance('tx_dam_browseTrees');
		$this->browseTrees->init('tx_dam_navframe.php');
		
		
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->JScode='';

			// Setting JavaScript for menu.
		$this->doc->JScode=$this->doc->wrapScriptTags(
			($this->currentSubScript?'top.currentSubScript=unescape("'.rawurlencode($this->currentSubScript).'");':'').'
		
			function jumpTo(params,linkObj,highLightID)	{
				var theUrl = top.TS.PATH_typo3+top.currentSubScript+"?"+params;
		
				if (top.condensedMode)	{
					top.content.document.location=theUrl;
				} else {
					parent.list_frame.document.location=theUrl;
				}
		        '.($this->doHighlight?'hilight_row("row"+top.fsMod.recentIds["txdamM1"],highLightID);':'').'
				'.(!$GLOBALS['CLIENT']['FORMSTYLE'] ? '' : 'if (linkObj) {linkObj.blur();}').'
				return false;
			}
			
					
				// Call this function, refresh_nav(), from another script in the backend if you want to refresh the navigation frame (eg. after having changed a page title or moved pages etc.)
				// See t3lib_BEfunc::getSetUpdateSignal()
			function refresh_nav()	{
				window.setTimeout("_refresh_nav();",0);
			}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
			function _refresh_nav()	{
				document.location="tx_dam_navframe.php?unique='.time().'";
			}
			
				// Highlighting rows in the page tree:
			function hilight_row(frameSetModule,highLightID) {	//
		
					// Remove old:
				theObj = document.getElementById(top.fsMod.navFrameHighlightedID[frameSetModule]);
				if (theObj)	{
					theObj.style.backgroundColor="";
				}
				
					// Set new:
				top.fsMod.navFrameHighlightedID[frameSetModule] = highLightID;
				theObj = document.getElementById(highLightID);
				if (theObj)	{
					theObj.style.backgroundColor="'.t3lib_div::modifyHTMLColorAll($this->doc->bgColor,-20).'";
				}
			}
		');

		# $CMparts=$this->doc->getContextMenuCode();
		#$this->doc->bodyTagAdditions = $CMparts[1];
		#$this->doc->JScode.=$CMparts[0];
		#$this->doc->postCode.= $CMparts[2];
		
			// should be float but gives bad results
		$this->doc->inDocStyles .= '
			.txdam-editbar, .txdam-editbar > a >img {
				background-color:'.t3lib_div::modifyHTMLcolor($this->doc->bgColor,-15,-15,-15).';
			}
			';
	}




	/**
	 * Main function, rendering the browsable page tree
	 * 
	 * @return	void		
	 */
	function main()	{
		global $LANG,$BACK_PATH;

		$this->content = '';
		$this->content.= $this->doc->startPage('Navigation');
		
		$this->content.= $this->browseTrees->getTrees();
		
		$this->content.= '
			<p class="c-refresh">
				<a href="'.htmlspecialchars(t3lib_div::getIndpEnv('REQUEST_URI')).'">'.
				'<img'.t3lib_iconWorks::skinImg('',$BACK_PATH.'gfx/refresh_n.gif','width="14" height="14"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.refresh',1).'" alt="" />'.
				$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.refresh',1).'</a>
			</p>
			<br />';

			// Adding highlight - JavaScript
		if ($this->doHighlight)	$this->content .=$this->doc->wrapScriptTags('
			hilight_row("",top.fsMod.navFrameHighlightedID["web"]);
		');	
	}
	
	
	/**
	 * Outputting the accumulated content to screen
	 * 
	 * @return	void		
	 */
	function printContent()	{
		$this->content.= $this->doc->endPage();
		echo $this->content;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_main/tx_dam_navframe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_main/tx_dam_navframe.php']);
}




// Make instance:

$SOBE = t3lib_div::makeInstance('tx_dam_navframe');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();


?>