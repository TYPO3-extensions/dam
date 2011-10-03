<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Frontend',
	'Media Frontend'
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
		'media',	// Submodule key
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
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_media.xml',
		)
	);

	// Make sure the class exists to avoid a Runtime Error
	if (class_exists('Tx_Vidi_Service_ModuleLoader')) {
		/** @var Tx_Vidi_Service_ModuleLoader $moduleLoader */
		$moduleLoader = t3lib_div::makeInstance('Tx_Vidi_Service_ModuleLoader', $_EXTKEY);
		$moduleLoader->addStandardTree(Tx_Vidi_Service_ModuleLoader::TREE_FILES);
		$moduleLoader->setAllowedDataTypes(array('__FILES'));
		$moduleLoader->setMainModule('file');
		$moduleLoader->setModuleLanguageFile('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_media.xml');
		$moduleLoader->setIcon('EXT:' . $_EXTKEY . '/ext_icon.gif');
		$moduleLoader->register();
	}
}


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Media Management');


t3lib_extMgm::addLLrefForTCAdescr('tx_media', 'EXT:media/Resources/Private/Language/locallang_csh_tx_media.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_media');
$TCA['tx_media'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media',
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Media.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_media.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_media_mediatype', 'EXT:media/Resources/Private/Language/locallang_csh_tx_media_mediatype.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_media_mediatype');
$TCA['tx_media_mediatype'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media_mediatype',
		'label' => 'asset_type',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/MediaType.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_media_mediatype.gif'
	),
);

t3lib_extMgm::addLLrefForTCAdescr('tx_media_mimetype', 'EXT:media/Resources/Private/Language/locallang_csh_tx_media_mimetype.xml');
t3lib_extMgm::allowTableOnStandardPages('tx_media_mimetype');
$TCA['tx_media_mimetype'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:media/Resources/Private/Language/locallang_db.xml:tx_media_mimetype',
		'label' => 'mime_type',
		'dividers2tabs' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/MimeType.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_media_mimetype.gif'
	),
);

?>