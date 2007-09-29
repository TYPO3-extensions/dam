<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}


	// PHP5 compatiblity
if (!function_exists('stripos')) {
	require_once(PATH_txdam.'compat/stripos.php');
}
if (!function_exists('str_ireplace')) {
	require_once(PATH_txdam.'compat/str_ireplace.php');
}


	// that's the base API
require_once(PATH_txdam.'lib/class.tx_dam.php');


	// get extension setup
$TYPO3_CONF_VARS['EXTCONF']['dam']['setup'] = unserialize($_EXTCONF);

$TYPO3_CONF_VARS['EXTCONF']['dam']['indexing']['defaultSetup'] = '<phparray>
	<pid>0</pid>
	<pathlist type="array">
		<numIndex index="0">fileadmin/</numIndex>
	</pathlist>
	<recursive>0</recursive>
	<ruleConf type="array">
		<tx_damdemo_indexRule type="array">
			<enabled>0</enabled>
			<option1>0</option1>
		</tx_damdemo_indexRule>
		<tx_damindex_rule_recursive type="array">
			<enabled>0</enabled>
		</tx_damindex_rule_recursive>
		<tx_damindex_rule_folderAsCat type="array">
			<enabled>0</enabled>
			<fuzzy>0</fuzzy>
		</tx_damindex_rule_folderAsCat>
		<tx_damindex_rule_doReindexing type="array">
			<enabled>0</enabled>
			<mode>0</mode>
		</tx_damindex_rule_doReindexing>
		<tx_damindex_rule_dryRun type="array">
			<enabled>0</enabled>
		</tx_damindex_rule_dryRun>
		<tx_damindex_rule_devel type="array">
			<enabled>0</enabled>
		</tx_damindex_rule_devel>
	</ruleConf>
	<dataPreset type="array">
		<title></title>
		<keywords></keywords>
		<description></description>
		<caption></caption>
		<alt_text></alt_text>
		<file_orig_location></file_orig_location>
		<file_orig_loc_desc></file_orig_loc_desc>
		<ident></ident>
		<creator></creator>
		<publisher></publisher>
		<copyright></copyright>
		<instructions></instructions>
		<date_cr></date_cr>
		<date_mod></date_mod>
		<loc_desc></loc_desc>
		<loc_country></loc_country>
		<loc_city></loc_city>
		<language></language>
		<category></category>
		<tx_damdemo_info></tx_damdemo_info>
	</dataPreset>
	<dataPostset type="array">
	</dataPostset>
	<dryRun>0</dryRun>
	<doReindexing>0</doReindexing>
	<collectMeta type="boolean">1</collectMeta>
	<extraSetup></extraSetup>
</phparray>';



	// set some config values from extension setup
tx_dam::config_setValue('setup.devel', $TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['devel']);
tx_dam::config_setValue('setup.debug', $TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['debug']);

	// register default icons
tx_dam::register_fileIconPath(PATH_txdam.'i/18/');


	// field templates for usage in other tables to link media records
require_once(PATH_txdam.'tca_media_field.php');


if(t3lib_div::int_from_ver(TYPO3_version) < t3lib_div::int_from_ver('4.0')) {
	if (!defined('TYPO3_OS')) define('TYPO3_OS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin')?'WIN':'');
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/class.alt_menu_functions.inc'] = PATH_txdam.'compat/class.ux_alt_menu_functions.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/template.php'] = PATH_txdam.'compat/class.ux_template.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/show_item.php'] = PATH_txdam.'compat/class.ux_SC_show_item.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/browse_links.php'] = PATH_txdam.'compat/class.ux_SC_browse_links.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/alt_doc.php'] = PATH_txdam.'compat/class.ux_SC_alt_doc.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tceforms.php'] = PATH_txdam.'compat/class.ux_t3lib_tceforms.php';
}


	// register show item rendering
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/show_item.php']['typeRendering'][] = 'EXT:dam/class.tx_dam_show_item.php:&tx_dam_show_item';
	// register element browser rendering
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/browse_links.php']['browserRendering'][] = 'EXT:dam/class.tx_dam_browse_media.php:&tx_dam_browse_media';

	// register navigation tree and select rule for nav tree.
tx_dam::register_selection ('txdamFolder',    'EXT:dam/components/class.tx_dam_selectionFolder.php:&tx_dam_selectionFolder');
tx_dam::register_selection ('txdamCat',       'EXT:dam/components/class.tx_dam_selectionCategory.php:&tx_dam_selectionCategory');
tx_dam::register_selection ('txdamMedia',     'EXT:dam/components/class.tx_dam_selectionMeTypes.php:&tx_dam_selectionMeTypes');
tx_dam::register_selection ('txdamStatus',    'EXT:dam/components/class.tx_dam_selectionStatus.php:&tx_dam_selectionStatus');
tx_dam::register_selection ('txdamIndexRun',  'EXT:dam/components/class.tx_dam_selectionIndexRun.php:&tx_dam_selectionIndexRun');
tx_dam::register_selection ('txdamStrSearch', 'EXT:dam/components/class.tx_dam_selectionStringSearch.php:&tx_dam_selectionStringSearch');

	// register DAM internal db change trigger
tx_dam::register_dbTrigger ('tx_dam_dbTriggerMediaTypes', 'EXT:dam/components/class.tx_dam_dbTriggerMediaTypes.php:&tx_dam_dbTriggerMediaTypes');

	// register special TCE tx_dam processing
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:dam/class.tx_dam_tce_process.php:&tx_dam_tce_process';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/class.tx_dam_tce_process.php:&tx_dam_tce_process';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/class.tx_dam_tce_filetracking.php:&tx_dam_tce_filetracking';

?>