<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Frontend',
	'DAM Frontend'
);

//$pluginSignature = str_replace('_','',$_EXTKEY) . '_' . frontend;
//$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_' .frontend. '.xml');





if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'dam',	// Submodule key
		'',						// Position
		array(
			'Indexing' => 'index',
		//	'Asset' => 'show, list, new, create, edit, update, delete',
		//	'Collection' => 'list, show, new, create, edit, update, delete',
		//	'Filter' => 'list, show, new, create, edit, update, delete',
		//	'File' => 'show, list, new, create, edit, update, delete',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_dam.xml',
		)
	);

	// Make sure the class exists to avoid a Runtime Error
	if (class_exists('Tx_Vidi_Service_ModuleLoader')) {
		/** @var Tx_Vidi_Service_ModuleLoader $moduleLoader */
		$moduleLoader = t3lib_div::makeInstance('Tx_Vidi_Service_ModuleLoader', $_EXTKEY);
		$moduleLoader->addStandardTree(Tx_Vidi_Service_ModuleLoader::TREE_FILES);
		$moduleLoader->setAllowedDataTypes(array('__FILES'));
		$moduleLoader->setMainModule('file');
		$moduleLoader->setModuleLanguageFile('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_dam.xml');
		$moduleLoader->setIcon('EXT:' . $_EXTKEY . '/ext_icon.gif');
		$moduleLoader->register();
	}
}


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'DAM');


t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_asset', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_asset.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_asset');
$TCA['tx_dam_domain_model_asset'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'asset_type',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Asset.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_asset.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_collection', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_collection.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_collection');
$TCA['tx_dam_domain_model_collection'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_collection',
		'label' => 'collection_name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Collection.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_collection.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_filter', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_filter.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_filter');
$TCA['tx_dam_domain_model_filter'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_filter',
		'label' => 'filter_name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Filter.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_filter.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_assettype', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_assettype.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_assettype');
$TCA['tx_dam_domain_model_assettype'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_assettype',
		'label' => 'asset_type',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/AssetType.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_assettype.gif'
	),
);

/* tx_dam_domain_model_file is only a dummy for sys_file, mapped by Typoscript */
/*t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_file', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_file.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_file');
$TCA['tx_dam_domain_model_file'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_file',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/File.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_file.gif'
	),
);*/

t3lib_extMgm::addLLrefForTCAdescr('tx_dam_domain_model_mimetype', 'EXT:dam/Resources/Private/Language/locallang_csh_tx_dam_domain_model_mimetype.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_domain_model_mimetype');
$TCA['tx_dam_domain_model_mimetype'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_mimetype',
		'label' => 'mime_type',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/MimeType.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dam_domain_model_mimetype.gif'
	),
);

?>