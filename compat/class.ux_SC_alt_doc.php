<?php

/**
 * @ignore
 */
class ux_SC_alt_doc extends SC_alt_doc {

	/**
	 * First initialization.
	 *
	 * @return	void
	 */
	function preInit()	{
		global $BE_USER;

		if (t3lib_div::_GP('justLocalized'))	{
			$this->localizationRedirect(t3lib_div::_GP('justLocalized'));
		}

		parent::preInit();
	}

	/**
	 * Main module operation
	 *
	 * @return	void
	 */
	function main()	{
		global $BE_USER,$LANG;

			// Starting content accumulation:
		$this->content='';
		$this->content.=$this->doc->startPage('TYPO3 Edit Document');

			// Begin edit:
		if (is_array($this->editconf))	{

				// Initialize TCEforms (rendering the forms)
			$this->tceforms = t3lib_div::makeInstance('t3lib_TCEforms');
			$this->tceforms->initDefaultBEMode();
			$this->tceforms->doSaveFieldName = 'doSave';
			$this->tceforms->localizationMode = t3lib_div::inList('text,media',$this->localizationMode) ? $this->localizationMode : '';	// text,media is keywords defined in TYPO3 Core API..., see "l10n_cat"
			$this->tceforms->returnUrl = $this->R_URI;
			$this->tceforms->palettesCollapsed = !$this->MOD_SETTINGS['showPalettes'];
			$this->tceforms->disableRTE = $this->MOD_SETTINGS['disableRTE'];
			$this->tceforms->enableClickMenu = TRUE;
			$this->tceforms->enableTabMenu = TRUE;

				// Clipboard is initialized:
			$this->tceforms->clipObj = t3lib_div::makeInstance('t3lib_clipboard');		// Start clipboard
			$this->tceforms->clipObj->initializeClipboard();	// Initialize - reads the clipboard content from the user session

				// Setting external variables:
			if ($BE_USER->uc['edit_showFieldHelp']!='text' && $this->MOD_SETTINGS['showDescriptions'])	$this->tceforms->edit_showFieldHelp='text';

			if ($this->editRegularContentFromId)	{
				$this->editRegularContentFromId();
			}

				// Creating the editing form, wrap it with buttons, document selector etc.
			$editForm = $this->makeEditForm();

			if ($editForm)	{
				reset($this->elementsData);
				$this->firstEl = current($this->elementsData);

					// language switch/selector for editing
					// show only when a single record is edited - multiple records are too confusing
				if (count($this->elementsData)==1) {
					$languageSwitch = $this->languageSwitch($this->firstEl['table'], $this->firstEl['uid'], NULL /*$this->firstEl['pid']*/);
				}


				if ($this->viewId)	{
						// Module configuration:
					$this->modTSconfig = t3lib_BEfunc::getModTSconfig($this->viewId,'mod.xMOD_alt_doc');
				} else $this->modTSconfig=array();

				$panel = $this->makeButtonPanel();
				$docSel = $this->makeDocSel();
				$cMenu = $this->makeCmenu();

				$formContent = $this->compileForm($panel,$docSel,$cMenu,$editForm,$languageSwitch);

				$this->content.= $this->tceforms->printNeededJSFunctions_top().
									$formContent.
									$this->tceforms->printNeededJSFunctions();
				$this->content.= $this->functionMenus();

					// Add CSH:
				$this->content.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'TCEforms', $GLOBALS['BACK_PATH'],'<br/>|',FALSE,'margin-top: 20px;');

				$this->content.= $this->shortCutLink();

				$this->tceformMessages();
			}
		}
	}


	/**
	 * Put together the various elements (buttons, selectors, form) into a table
	 *
	 * @param	string		The button panel HTML
	 * @param	string		Document selector HTML
	 * @param	string		Clear-cache menu HTML
	 * @param	string		HTML form.
	 * @param	string		Language selector HTML for localization
	 * @return	string		Composite HTML
	 */
	function compileForm($panel,$docSel,$cMenu,$editForm, $langSelector='')	{
		global $LANG;


		$formContent='';
		$formContent.='

			<!--
			 	Header of the editing page.
				Contains the buttons for saving/closing, the document selector and menu selector.
				Shows the path of the editing operation as well.
			-->
			<table border="0" cellpadding="0" cellspacing="1" width="470" id="typo3-altdoc-header">
				<tr>
					<td nowrap="nowrap" valign="top">'.$panel.'</td>
					<td nowrap="nowrap" valign="top" align="right">'.$docSel.$cMenu.'</td>
				</tr>';

		if ($langSelector) {
			$langSelector ='<div style="float:right" id="typo3-altdoc-lang-selector">'.$langSelector.'</div>';
		}
		$pagePath = '<div id="typo3-altdoc-page-path">'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path',1).': '.htmlspecialchars($this->generalPathOfForm).'</div>';

		$formContent.='
				<tr>
					<td colspan="2"><div id="typo3-altdoc-header-info-options">'.$langSelector.$pagePath.'<div></td>
				</tr>
			</table>




			<!--
			 	EDITING FORM:
			-->

			'.$editForm.'



			<!--
			 	Saving buttons (same as in top)
			-->

			'.$panel.
			'<input type="hidden" name="returnUrl" value="'.htmlspecialchars($this->retUrl).'" />
			<input type="hidden" name="viewUrl" value="'.htmlspecialchars($this->viewUrl).'" />';

		if ($this->returnNewPageId)	{
			$formContent.='<input type="hidden" name="returnNewPageId" value="1" />';
		}
		$formContent.='<input type="hidden" name="popViewId" value="'.htmlspecialchars($this->viewId).'" />';
		if ($this->viewId_addParams) {
			$formContent.='<input type="hidden" name="popViewId_addParams" value="'.htmlspecialchars($this->viewId_addParams).'" />';
		}
		$formContent.='<input type="hidden" name="closeDoc" value="0" />';
		$formContent.='<input type="hidden" name="doSave" value="0" />';
		$formContent.='<input type="hidden" name="_serialNumber" value="'.md5(microtime()).'" />';
		$formContent.='<input type="hidden" name="_disableRTE" value="'.$this->tceforms->disableRTE.'" />';

		return $formContent;
	}


	/***************************
	 *
	 * Localization stuff
	 *
	 ***************************/

	/**
	 * Make selector box for creating new translation for a record or switching to edit the record in an existing language.
	 * Displays only languages which are available for the current page.
	 *
	 * @param 	string 		Table name
	 * @param	integer		uid for which to create a new language
	 * @param	integer		pid of the record
	 * @return	string		<select> HTML element (if there were items for the box anyways...)
	 */
	function languageSwitch($table, $uid, $pid=NULL)	{
		global $TCA;

		$content = '';

		$languageField = $TCA[$table]['ctrl']['languageField'];
		$transOrigPointerField = $TCA[$table]['ctrl']['transOrigPointerField'];

			// table editable and activated for languages?
		if ($GLOBALS['BE_USER']->check('tables_modify',$table) && $languageField && $transOrigPointerField && !$TCA[$table]['ctrl']['transOrigPointerTable'])	{

			if(is_null($pid)) {
				$row = t3lib_befunc::getRecord($table, $uid, 'pid');
				$pid = $row['pid'];
			}

				// get all avalibale languages for the page
			$langRows = $this->getLanguages($pid);

				// page available in other languages than default language?
			if (is_array($langRows) && count($langRows)>1) {


				$rowsByLang = array();
				$fetchFields = 'uid,'.$languageField.','.$transOrigPointerField;

					// get record in current language
				$rowCurrent = t3lib_befunc::getRecord($table, $uid, $fetchFields);
				$currentLanguage = $rowCurrent[$languageField];

					// get record in default language if needed
				if ($currentLanguage) {
					$rowsByLang[0] = t3lib_befunc::getRecord($table, $rowCurrent[$transOrigPointerField], $fetchFields);
				} else {
					$rowsByLang[0] = $rowCurrent;
				}

					// get record in other languages to see what's already available
				$translations = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					$fetchFields,
					$table,
					'pid='.intval($pid).
						' AND '.$languageField.'>0'.
						' AND '.$transOrigPointerField.'='.intval($rowsByLang[0]['uid']).
						t3lib_BEfunc::deleteClause($table)
				);
				foreach ($translations as $row)	{
					$rowsByLang[$row[$languageField]] = $row;
				}

				$langSelItems=array();
				foreach ($langRows as $lang) {
					if ($GLOBALS['BE_USER']->checkLanguageAccess($lang['uid']))	{

						$newTranslation = isset($rowsByLang[$lang['uid']]) ? '' : ' ['.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.new',1).']';

							// create url for creating a localized record
						if($newTranslation) {
							$href = $this->doc->issueCommand(
								'&cmd['.$table.']['.$rowsByLang[0]['uid'].'][localize]='.$lang['uid'],
								$this->backPath.'alt_doc.php?justLocalized='.rawurlencode($table.':'.$rowsByLang[0]['uid'].':'.$lang['uid']).'&returnUrl='.rawurlencode($this->retUrl)
							);

							// create edit url
						} else {
							$href = $this->backPath.'alt_doc.php?';
							$href .= '&edit['.$table.']['.$rowsByLang[$lang['uid']]['uid'].']=edit';
							$href .= '&returnUrl='.rawurlencode($this->retUrl);
						}
						$langSelItems[$lang['uid']]='
								<option value="'.htmlspecialchars($href).'"'.($currentLanguage==$lang['uid']?' selected="selected"':'').'>'.htmlspecialchars($lang['title'].$newTranslation).'</option>';
					}
				}

					// If any languages are left, make selector:
				if (count($langSelItems)>1)		{
					$onChange = 'if(this.options[this.selectedIndex].value){window.location.href=(this.options[this.selectedIndex].value);}';
					$content = $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_general.xml:LGL.language',1).' <select name="_langSelector" onchange="'.htmlspecialchars($onChange).'">
							'.implode('',$langSelItems).'
						</select>';
				}
			}
		}
		return $content;
	}


	/**
	 * Redirects to alt_doc with new parameters to edit a just created localized record
	 *
	 * @param string 	String passed by GET &justLocalized=
	 * @return void
	 */
	function localizationRedirect($justLocalized)	{
		global $TCA;

		list($table,$orig_uid,$language) = explode(':',$justLocalized);

		if ($TCA[$table] && $TCA[$table]['ctrl']['languageField'] && $TCA[$table]['ctrl']['transOrigPointerField'])	{
			list($localizedRecord) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'uid',
					$table,
					$TCA[$table]['ctrl']['languageField'].'='.intval($language).' AND '.
						$TCA[$table]['ctrl']['transOrigPointerField'].'='.intval($orig_uid).
						t3lib_BEfunc::deleteClause($table)
				);

			if (is_array($localizedRecord))	{
					// Create parameters and finally run the classic page module for creating a new page translation
				$params = '&edit['.$table.']['.$localizedRecord['uid'].']=edit';
				$returnUrl = '&returnUrl='.rawurlencode(t3lib_div::_GP('returnUrl'));
				$location = $GLOBALS['BACK_PATH'].'alt_doc.php?'.$params.$returnUrl;

				header('Location: '.t3lib_div::locationHeaderUrl($location));
				exit;
			}
		}
	}


	/**
	 * Returns sys_language records.
	 *
	 * @param	integer		Page id: If zero, the query will select all sys_language records from root level which are NOT hidden. If set to another value, the query will select all sys_language records that has a pages_language_overlay record on that page (and is not hidden, unless you are admin user)
	 * @return	array		Language records including faked record for default language
	 */
	function getLanguages($id)	{
		global $LANG;

		$modSharedTSconfig = t3lib_BEfunc::getModTSconfig($id, 'mod.SHARED');

		$languages = array(
			0 => array(
				'uid' => 0,
				'pid' => 0,
				'hidden' => 0,
				'title' => strlen($modSharedTSconfig['properties']['defaultLanguageLabel']) ? $modSharedTSconfig['properties']['defaultLanguageLabel'].' ('.$GLOBALS['LANG']->sl('LLL:EXT:lang/locallang_mod_web_list.xml:defaultLanguage').')' : $GLOBALS['LANG']->sl('LLL:EXT:lang/locallang_mod_web_list.xml:defaultLanguage'),
				'flag' => $modSharedTSconfig['properties']['defaultLanguageFlag'],
			)
		);

		$exQ = $GLOBALS['BE_USER']->isAdmin() ? '' : ' AND sys_language.hidden=0';
		if ($id)	{
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'sys_language.*',
							'pages_language_overlay,sys_language',
							'pages_language_overlay.sys_language_uid=sys_language.uid AND pages_language_overlay.pid='.intval($id).$exQ,
							'pages_language_overlay.sys_language_uid,sys_language.uid,sys_language.pid,sys_language.tstamp,sys_language.hidden,sys_language.title,sys_language.static_lang_isocode,sys_language.flag',
							'sys_language.title'
						);
		} else {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'sys_language.*',
							'sys_language',
							'sys_language.hidden=0',
							'',
							'sys_language.title'
						);
		}
		if ($rows) {
			foreach ($rows as $row) {
				$languages[$row['uid']] = $row;
			}
		}
		return $languages;
	}




}

?>