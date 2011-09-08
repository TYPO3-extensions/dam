<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_dam_domain_model_asset'] = array(
	'ctrl' => $TCA['tx_dam_domain_model_asset']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, description, keywords, mime_type, extension, creation_date, modification_date, creator_tool, download_name, identifier, creator, source, alternative, caption, fal, asset_type',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, description, keywords, mime_type, extension, creation_date, modification_date, creator_tool, download_name, identifier, creator, source, alternative, caption, fal, asset_type,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dam_domain_model_asset',
				'foreign_table_where' => 'AND tx_dam_domain_model_asset.pid=###CURRENT_PID### AND tx_dam_domain_model_asset.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
		),
		'keywords' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.keywords',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
		),
		'mime_type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.mime_type',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'extension' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.extension',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'creation_date' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.creation_date',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'modification_date' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.modification_date',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'creator_tool' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.creator_tool',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'download_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.download_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'identifier' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'creator' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.creator',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'source' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.source',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'alternative' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.alternative',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'caption' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.caption',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'fal' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.fal',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_dam_domain_model_file',
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'collapse' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
		'asset_type' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_dam_domain_model_assettype',
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'collapse' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),
	),
);
?>