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
 * Part of the DAM (digital asset management) extension.
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
 *   91: class tx_dam_actionCall
 *
 *              SECTION: Constructor / Initialization
 *  141:     function tx_dam_actionCall($classes=NULL)
 *  152:     function __construct($classes=NULL)
 *  164:     function initClasses($classes=NULL)
 *  177:     function registerAction ($idName, $class)
 *  190:     function setEnv ($param1, $param2=NULL)
 *  214:     function setRequest ($type, $itemInfo, $mode, $moduleName)
 *
 *              SECTION: Iterator functions
 *  234:     function rewind()
 *  244:     function valid()
 *  255:     function next()
 *  265:     function key()
 *  275:     function &current()
 *  290:     function count ()
 *
 *              SECTION: Rendering
 *  311:     function renderActionsHorizontal($checkValidStrict=false, $showDisabled=true)
 *
 *              SECTION: Init item list
 *  356:     function initActions ($checkForPossiblyValid=false, $keepInvalid=false)
 *  368:     function initItems()
 *  406:     function addItem($idName, $position='', $divider='')
 *
 *              SECTION: Objects
 *  475:     function initObjects ($checkForPossiblyValid=false, $keepInvalid=false)
 *  500:     function &getByIDName ($idName)
 *  511:     function makeObject ($idName)
 *
 * TOTAL FUNCTIONS: 19
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */






/**
 * Action calling
 *
 * A action is something that renders buttons, control icons, ..., which executes command for an item.
 * This class can be used to find the right actions for an item and call the actions.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-BeLib
 * @subpackage Lib
 * @see tx_dam_actionbase
 */
class tx_dam_actionCall {

	/**
	 * stores action class references by idName keys
	 */
	var $classes = array();

	/**
	 * stores action objects
	 */
	var $objects = array();

	/**
	 * stores a sorted list of actions and spacers
	 */
	var $items = array();

	/**
	 * Environment
	 */
	 var $env = array(
	 	'returnUrl' => '',
	 	'defaultCmdScript' => '',
	 	);

	/**
	 * If set divider will be rendered, otherwise suppressed
	 */
	var $enableDivider = true;

	/**
	 * If set spacer will be rendered, otherwise suppressed
	 */
	var $enableSpacer = true;



	/***************************************
	 *
	 *	 Constructor / Initialization
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_actionCall($classes=NULL) {
		$this->__construct($classes=NULL);
	}


	/**
	 * Constructor
	 *
	 * @param	array		$classes Class reference array
	 * @return	void
	 */
	function __construct($classes=NULL) {
		$this->classes = is_array($classes) ? $classes : $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['actionClasses'];
		$this->classes = is_array($this->classes) ? $this->classes : array();
	}


	/**
	 * Initializes with own action classes
	 *
	 * @param	array		$classes Class reference array
	 * @return	void
	 */
	function initClasses($classes=NULL) {
		$this->__construct($classes);
	}


	/**
	 * Register a "action" class locally.
	 * This means it is known only by the instance of this class and not to the system.
	 *
	 * @param	string		$idName This is the ID of the action. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @return	void
	 */
	function registerAction ($idName, $class) {
		$this->classes[$idName] = $class;
	}


	/**
	 * Set values for the local environment.
	 * The environment are special keys/values that gives the action information what to do.
	 *
	 * @param	mixed		$param1 If array it is the env array. If string it is the key for the second paramater.
	 * @param	string		$param2 Is value if the first paramater is a string which is the key for this value.
	 * @return	void
	 */
	 function setEnv ($param1, $param2=NULL) {
	 	$env = array();

 		$this->env['mode'] = $this->mode;
 		$this->env['moduleName'] = $this->moduleName;

	 	if (is_array($param1)) {
	 		$env = $param1;
	 	}
	 	elseif (!is_array($param1) AND !is_null($param2)) {
	 		$env[$param1] = $param2;
	 	}
	 	$this->env = t3lib_div::array_merge_recursive_overrule($this->env, $env);
	 }

	/**
	 * Define what type of action are requested
	 *
	 * @param	string		$type Action type
	 * @param	array		$itemInfo Item info array. Eg pathInfo, meta data array
	 * @param	string		$mode unused for now
	 * @param	string		$moduleName Module name, eg. $GLOBALS['MCONF']['name']
	 * @return	void
	 */
	function setRequest ($type, $itemInfo, $mode, $moduleName) {
		$this->type = $type;
		$this->itemInfo = $itemInfo;
 		$this->mode = $mode;
 		$this->moduleName = $moduleName;
	}


	/***************************************
	 *
	 *	 Iterator functions
	 *
	 ***************************************/


	/**
	 * Set the internal pointer to its first element.
	 *
	 * @return	void
	 */
	function rewind() {
		reset($this->items);
	}


	/**
	 * Return true is the current element is valid.
	 *
	 * @return	boolean
	 */
	function valid() {
		$key = key($this->items);
		return isset($key);
	}


	/**
	 * Advance the internal pointer
	 *
	 * @return	void
	 */
	function next() {
		next($this->items);
	}


	/**
	 * Return the pointer to the current element
	 *
	 * @return	mixed
	 */
	function key() {
		return key($this->items);
	}


	/**
	 * Return the current element
	 *
	 * @return	array
	 */
	function &current() {
		$item = current($this->items);
		if (substr($item,0,2) == '__') {
				// returning a reference is not nice but there's no way
			return $item;
		}
		return $this->objects[$item];
	}


	/**
	 * Count elements
	 *
	 * @return	integer
	 */
	function count () {
		return count($this->items);
	}



	/***************************************
	 *
	 *	 Rendering
	 *
	 ***************************************/


	/**
	 * Walk through the list of actions and render them.
	 * Dividers and spacer are rendered for horizontal use.
	 *
	 * @param	boolean		$checkValidStrict Perform a strict valid test with isValid() for each action.
	 * @param	boolean		$showDisabled Will render diabled items for non-valid actions. Eg. a greyed icon without link.
	 * @return	array		Array of rendered items. Can be imploded for example.
	 */
	function renderActionsHorizontal($checkValidStrict=false, $showDisabled=true) {
		$actions = array();
		$valid = true;
		$this->rewind();
		while ($this->valid()) {
			$item = $this->current();
			if ($checkValidStrict) {
				$valid = $this->checkItemValid($item);
			}

			if ($valid OR $showDisabled) {
				if ($this->enableSpacer AND $item == '__spacer') {
					$actions[] = '&nbsp; &nbsp;';
				}
				elseif ($this->enableDivider AND $item == '__divider') {
					$actions[] = '&nbsp;<span style="border-left:1px dotted #666">&nbsp;</span>';
				}
				elseif (is_object($item)) {
					$actions[] = $item->render($this->type, !$valid);
				}
			}
			$this->next();
		}
		return $actions;
	}



	/**
	 * Function calls the actions own ->isValid function.
	 * If that returns true - meaning that the action is accessible a hook taking effect which allows external validation of the
	 * action.
	 *
	 * @param	object		$item Reference to the action object currently in process
	 * @return	boolean		returns true or false
	 */
	function checkItemValid (&$item) {
		global $TYPO3_CONF_VARS;

		$valid = $item->isValid($this->type, $this->itemInfo, $this->env);

		if ($valid) {
			$item->getIdName();

				// hook
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation']) AND count($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation']))	{
				foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['actionValidation'] as $classKey => $classRef)	{
					if (strtolower($classKey) == strtolower($item->idName)) {
						if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
							if (method_exists($obj, 'isTypeValid')) {
								$valid = $obj->isTypeValid($item->idName, $this->itemInfo);
								if ($valid === false) {
									break;
								}
							}
						}
					}
				}
			}
		}

		return $valid;
	}



	/***************************************
	 *
	 *	 Init item list
	 *
	 ***************************************/



	/**
	 * Initializes the action objects.
	 *
	 * @param	boolean		$checkForPossiblyValid If set invalid will be done with isPossiblyValid().
	 * @param	boolean		$keepInvalid If set invalid actions will not removed
	 * @return	void
	 */
	function initActions ($checkForPossiblyValid=false, $keepInvalid=false) {
		$this->initObjects ($checkForPossiblyValid, $keepInvalid);
		$this->initItems ();
		$this->rewind();
	}


	/**
	 * Initializes the action item list including sorting
	 *
	 * @return	void
	 */
	function initItems() {
		$this->items = array();

		foreach ($this->objects as $idName => $action) {
			$this->addItem ($idName, $action->getWantedPosition($this->type), $action->getWantedDivider($this->type));
		}
			// remove first and last spacer etc
		while (substr($this->items[0],0,2) == '__') {
			unset($this->items[0]);
		}	// remove first and last spacer etc
		while (substr(end($this->items),0,2) == '__') {
			unset($this->items[key($this->items)]);
		}
			// remove double spacer etc
		$last = NULL;
		foreach ($this->items as $key => $item) {
			if ($last) {
				if ($this->items[$last] == $item)
					unset ($this->items[$last]);
				if ($this->items[$last] == '__spacer' AND $item == '__divider')
					unset ($this->items[$last]);
				if ($this->items[$last] == '__divider' AND $item == '__spacer')
					unset ($this->items[$key]);
			}
			$last = $key;
		}
	}



	/**
	 * Adds a module (main or sub) to the backend interface
	 *
	 * @param	string		$idName
	 * @param	string		$position can be used to set the position of the action within the list of existing action items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @param	string		$divider Diver before and after the element can be defined. Example: "spacer:divider". Spacer before, divider after.
	 * @return	void
	 */
	function addItem($idName, $position='', $divider='')	{

		$position .= ';bottom';
		$posList = t3lib_div::trimExplode(';', $position, 1);

		list($dividerBefore, $dividerAfter) = explode(':', $divider);
		$dividerBefore = $dividerBefore ? '__'.$dividerBefore : false;
		$dividerAfter = $dividerAfter ? '__'.$dividerAfter : false;

		$element = t3lib_div::trimExplode(';', $dividerBefore.';'.$idName.';'.$dividerAfter, 1);

		$placed = false;

		foreach ($posList as $posDef) {
			list($place, $itemRef) = t3lib_div::trimExplode(':', $posDef, 1);
			switch($place)	{
				case 'after':
				case 'before':
					$found = false;
					$pointer = 0;
					foreach($this->items as $k => $m)	{
						if (!strcmp($m, $itemRef))	{
							$pointer = $place=='after' ? $k+1 : $k;
							$found = true;
						}
					}
					if ($found) {

						$element = (count($element)>1) ? $element : $idName;
						array_splice(
							$this->items,
							$pointer,
							0,
							$element
						);
						$placed = true;
					}
				break;
				case 'top':
					$this->items = array_merge($element, $this->items);
					$placed = true;
				break;
				default:
						// append to the list
					$this->items = array_merge($this->items, $element);
					$placed = true;
				break;
			}
			if ($placed) break;
		}
	}




	/***************************************
	 *
	 *	 Objects
	 *
	 ***************************************/


	/**
	 * Initializes the action objects.
	 *
	 * @param	boolean		$checkForPossiblyValid If set invalid will be done with isPossiblyValid().
	 * @param	boolean		$keepInvalid If set invalid actions will not removed
	 * @return	void
	 */
	function initObjects ($checkForPossiblyValid=false, $keepInvalid=false) {
		foreach ($this->classes as $idName => $classRef) {
			if ($this->makeObject($idName)) {
				$this->objects[$idName]->setItemInfo($this->itemInfo);
				$this->objects[$idName]->setEnv($this->env);
				if ($checkForPossiblyValid) {
					$valid = $this->objects[$idName]->isPossiblyValid($this->type);
				}
				else {
					$valid = $this->objects[$idName]->isValid($this->type);
				}
				if (!$keepInvalid AND !$valid) {
					unset ($this->objects[$idName]);
				}
			}
		}
	}


	/**
	 * Get an object by it's idName
	 *
	 * @param	string		$idName
	 * @return	object
	 */
	function &getByIDName ($idName) {
		return (is_object($this->objects[$idName]) ? $this->objects[$idName] : NULL);
	}


	/**
	 * Initialize an object by it's idName
	 *
	 * @param	string		$idName
	 * @return	boolean
	 */
	function makeObject ($idName) {
		if (!isset($this->objects[$idName]) AND isset($this->classes[$idName])) {
			if (!is_object($this->objects[$idName] = t3lib_div::getUserObj($this->classes[$idName]))) {
				unset($this->objects[$idName]);
			}
		}
		return is_object($this->objects[$idName]);
	}


}




if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actioncall.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_actioncall.php']);
}
?>