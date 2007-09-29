<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Contains class with layout/output function for TYPO3 Backend Scripts
 *
 * $Id: class.ux_template.php,v 1.1 2005/07/26 07:56:04 cvsrene Exp $
 * Revised for TYPO3 3.6 2/2003 by Kasper Skaarhoj
 * XHTML-trans compliant
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @ignore
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   93: class ux_template extends template
 *  102:     function getDynTabMenuJScode()
 *
 *
 *  201: class ux_bigDoc extends ux_template
 *
 *
 *  210: class ux_noDoc extends ux_template
 *
 *
 *  219: class ux_smallDoc extends ux_template
 *
 *
 *  228: class ux_mediumDoc extends ux_template
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



if (!defined('TYPO3_MODE'))	die("Can't include this file directly.");








/**
 * TYPO3 Backend Template Class
 *
 * This class contains functions for starting and ending the HTML of backend modules
 * It also contains methods for outputting sections of content.
 * Further there are functions for making icons, links, setting form-field widths etc.
 * Color scheme and stylesheet definitions are also available here.
 * Finally this file includes the language class for TYPO3's backend.
 *
 * After this file $LANG and $TBE_TEMPLATE are global variables / instances of their respective classes.
 * This file is typically included right after the init.php file,
 * if language and layout is needed.
 *
 * Please refer to Inside TYPO3 for a discussion of how to use this API.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage core
 * @ignore
 */
class ux_template extends template {



	/**
	 * Returns dynamic tab menu header JS code.
	 *
	 * @return	string		JavaScript section for the HTML header.
	 */
	function getDynTabMenuJScode()	{
		return '
			<script type="text/javascript">
			/*<![CDATA[*/
				var DTM_array = new Array();
				var DTM_origClass = new String();

					// if tabs are used in a popup window the array might not exists
				if(!top.DTM_currentTabs) {
					top.DTM_currentTabs = new Array();
				}

				function DTM_activate(idBase,index,doToogle)	{	//
						// Hiding all:
					if (DTM_array[idBase])	{
						for(cnt = 0; cnt < DTM_array[idBase].length ; cnt++)	{
							if (DTM_array[idBase][cnt] != idBase+"-"+index)	{
								document.getElementById(DTM_array[idBase][cnt]+"-DIV").style.display = "none";
								document.getElementById(DTM_array[idBase][cnt]+"-MENU").attributes.getNamedItem("class").nodeValue = "tab";
							}
						}
					}

						// Showing one:
					if (document.getElementById(idBase+"-"+index+"-DIV"))	{
						if (doToogle && document.getElementById(idBase+"-"+index+"-DIV").style.display == "block")	{
							document.getElementById(idBase+"-"+index+"-DIV").style.display = "none";
							if(DTM_origClass=="") {
								document.getElementById(idBase+"-"+index+"-MENU").attributes.getNamedItem("class").nodeValue = "tab";
							} else {
								DTM_origClass = "tab";
							}
							top.DTM_currentTabs[idBase] = -1;
						} else {
							document.getElementById(idBase+"-"+index+"-DIV").style.display = "block";
							if(DTM_origClass=="") {
								document.getElementById(idBase+"-"+index+"-MENU").attributes.getNamedItem("class").nodeValue = "tabact";
							} else {
								DTM_origClass = "tabact";
							}
							top.DTM_currentTabs[idBase] = index;
						}
					}
				}
				function DTM_toggle(idBase,index,isInit)	{	//
						// Showing one:
					if (document.getElementById(idBase+"-"+index+"-DIV"))	{
						if (document.getElementById(idBase+"-"+index+"-DIV").style.display == "block")	{
							document.getElementById(idBase+"-"+index+"-DIV").style.display = "none";
							if(isInit) {
								document.getElementById(idBase+"-"+index+"-MENU").attributes.getNamedItem("class").nodeValue = "tab";
							} else {
								DTM_origClass = "tab";
							}
							top.DTM_currentTabs[idBase+"-"+index] = 0;
						} else {
							document.getElementById(idBase+"-"+index+"-DIV").style.display = "block";
							if(isInit) {
								document.getElementById(idBase+"-"+index+"-MENU").attributes.getNamedItem("class").nodeValue = "tabact";
							} else {
								DTM_origClass = "tabact";
							}
							top.DTM_currentTabs[idBase+"-"+index] = 1;
						}
					}
				}

				function DTM_mouseOver(obj) {	//
						DTM_origClass = obj.attributes.getNamedItem(\'class\').nodeValue;
						obj.attributes.getNamedItem(\'class\').nodeValue += "_over";
				}

				function DTM_mouseOut(obj) {	//
						obj.attributes.getNamedItem(\'class\').nodeValue = DTM_origClass;
						DTM_origClass = "";
				}


			/*]]>*/
			</script>
		';
	}

}



// ******************************
// Extension classes of the template class.
// These are meant to provide backend screens with different widths.
// They still do because of the different class-prefixes used for the <div>-sections
// but obviously the final width is determined by the stylesheet used.
// ******************************

/**
 * Extension class for "template" - used for backend pages which are wide. Typically modules taking up all the space in the "content" frame of the backend
 * The class were more significant in the past than today.
 *
 */
class ux_bigDoc extends ux_template {
	var $divClass = 'typo3-bigDoc';
}

/**
 * Extension class for "template" - used for backend pages without the "document" background image
 * The class were more significant in the past than today.
 *
 */
class ux_noDoc extends ux_template {
	var $divClass = 'typo3-noDoc';
}

/**
 * Extension class for "template" - used for backend pages which were narrow (like the Web>List modules list frame. Or the "Show details" pop up box)
 * The class were more significant in the past than today.
 *
 */
class ux_smallDoc extends ux_template {
	var $divClass = 'typo3-smallDoc';
}

/**
 * Extension class for "template" - used for backend pages which were medium wide. Typically submodules to Web or File which were presented in the list-frame when the content frame were divided into a navigation and list frame.
 * The class were more significant in the past than today. But probably you should use this one for most modules you make.
 *
 */
class ux_mediumDoc extends ux_template {
	var $divClass = 'typo3-mediumDoc';
}



// Include extension to the template class?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_template.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/class.ux_template.php']);
}

?>