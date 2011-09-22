<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_dam_domain_model_asset'] = array(
	'ctrl' => $TCA['tx_dam_domain_model_asset']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'asset_type, title, description, keywords, creation_date, modification_date, download_name, identifier, creator, source',
	),
	'types' => array(
		
		'1' => array('showitem' => 'thumbnail, asset_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, status, title, description, language, alternative, caption, ranking, keywords, identifier, source,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.file, fal, creation_date, modification_date, download_name,
									--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
		
		'2' => array('showitem' => 'thumbnail, asset_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, status, title, description, alternative, caption, ranking, keywords, identifier, source,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.metrics, color_space, --palette--;;10;;, --palette--;;14;;,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.geolocation, location_country, location_region, location_city, latitude, longitude,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.file, fal, creation_date, modification_date, download_name,
									--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
		
		'3' => array('showitem' => 'thumbnail, asset_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, status, title, description, language, alternative, caption, ranking, keywords, identifier, source,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.metrics, duration, unit,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.file, fal, creation_date, modification_date, download_name,
									--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
		
		'4' => array('showitem' => 'thumbnail, asset_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, status, title, description, language, alternative, caption, ranking, keywords, identifier, source,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.file, fal, creation_date, modification_date, download_name,
									--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
		
		'5' => array('showitem' => 'thumbnail, asset_type, sys_language_uid, l10n_parent, l10n_diffsource, hidden, status, title, description, language, alternative, caption, ranking, keywords, identifier, source,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.metrics, --palette--;;10;;, --palette--;;14;;,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.copyright, creator, publisher,
									--div--;LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tabs.file, fal, creation_date, modification_date, download_name,
									--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'10' => array('showitem' => 'width, height, unit', 'canNotCollapse' => '1'),
		'14' => array('showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'),
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
		'thumbnail' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.thumbnail',
			'config' => array(
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/Resources/Private/TCEforms/class.tx_dam_tceforms.php:&tx_dam_tceforms->renderThumbnail',
				'noTableWrapping' => TRUE,
				'readOnly' => TRUE,
			),
		),
		'asset_type' => array(
			'exclude' => 0,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type',
			'config' => array(
				'type' => 'select',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => array(
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type.1',
						1,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/tx_dam_domain_model_text.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type.2',
						2,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/tx_dam_domain_model_image.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type.3',
						3,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/tx_dam_domain_model_audio.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type.4',
						4,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/tx_dam_domain_model_video.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.asset_type.5',
						5,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/tx_dam_domain_model_software.png'
					),
				),
			),
		),
		'status' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.status',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.status.1',
						1,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/status_1.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.status.2',
						2,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/status_2.png'
					),
					array(
						'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.status.3',
						3,
						t3lib_extMgm::extRelPath('dam') . 'Resources/Public/Icons/status_3.png'
					),
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
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.mime_type',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'extension' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.extension',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'creation_date' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
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
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
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
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.creator_tool',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'download_name' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.download_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'identifier' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.identifier',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'creator' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.creator',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'publisher' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.publisher',
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
		'pages' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.pages',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'note' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.note',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			),
		),
		'location_country' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.location_country',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'location_region' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.location_region',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'location_city' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.location_city',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'latitude' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.latitude',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			),
		),
		'longitude' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.longitude',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			),
		),
		'ranking' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.ranking',
			'config' => array(
				'type' => 'select',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => array(
					array(1,1),
					array(2,2),
					array(3,3),
					array(4,4),
					array(5,5),
				),
			),
		),
		'language' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.language',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			)
		),
		
		/*
		 * METRICS ###########################################
		 */
		'duration' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.duration',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'horizontal_resolution' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.horizontal_resolution',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'vertical_resolution' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.vertical_resolution',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'color_space' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.color_space',
			'l10n_mode' => 'exclude',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('RGB', 'RGB'),
// This is not a colorspace but a color profile					array('sRGB', 'sRGB'),
					array('CMYK', 'CMYK'),
					array('CMY', 'CMY'),
					array('YUV', 'YUV'),
					array('Grey', 'grey'),
					array('indexed', 'indx'),
				),
				'default' => ''
			)
		),
		'width' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.width',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			),
		),
		'height' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.height',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			),
		),
		'unit' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.unit',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', ''),
					array('px', 'px'),
					array('mm', 'mm'),
					array('cm', 'cm'),
					array('m', 'm'),
					array('p', 'p'),
				),
				'default' => ''
			),
		),
		
		'fal' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dam/Resources/Private/Language/locallang_db.xml:tx_dam_domain_model_asset.fal',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_file',
				'minitems' => 1,
				'maxitems' => 1,
				/*'appearance' => array(
					'collapse' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),*/
			),
		),
	),
);
?>