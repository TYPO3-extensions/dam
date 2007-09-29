<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/* TODO

file_status
download name

*/

require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_tcefunc.php');




$TCA['tx_dam'] = Array (
	'ctrl' => $TCA['tx_dam']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,media_type,title,file_type'
	),
	'feInterface' => $TCA['tx_dam']['feInterface'],
	'txdamInterface' => $TCA['tx_dam']['txdamInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '1'
			)
		),
		'active' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
	/*
$txdamTypes['media2Codes'] = array (
	'undefined' => '0',
	'text' => '1',
	'image' => '2',
	'audio' => '3',
	'video' => '4',
	'interactive' => '5',
	'service' => '6',
	'font' => '7',
	'model' => '8',
	'dataset' => '9',
	'collection' => '10',
	'software' => '11',
	'application' => '12',
);
*/

		'media_type' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.media_type',
			'config' => Array (
				'type' => 'user',
				// 'type' => 'select',
				'items' => Array (
					Array('Text|', '1'),
					Array('Image||Bild', '2'),
					Array('Audio|', '3'),
					Array('Video|', '4'),
					Array('Dataset||Daten', '9'),
					Array('Interactive||Interaktiv', '5'),
					Array('Software|', '11'),
					Array('Model||Model', '8'),
					Array('Font||Schrift', '7'),
					Array('Collection||Sammlung', '10'),
					Array('Service|', '6'),
					Array('Application||Applikation', '12'),
					Array('Undefined||Sonstiges', '0'),
				),

				'userFunc' => 'tx_dam_tceFunc->tx_dam_mediaType',
				'noTableWrapping' => TRUE,
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
#				'type' => 'user',
#				'userFunc' => 'tx_dam_tceFunc->tx_dam_title',
#				'noTableWrapping' => TRUE,
			)
		),

		/*
		 * FILE ###########################################
		 */

		'file_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_name',
			'config' => Array (
				'type' => 'none',
				'size' => '15',
				'max' => '100',
				'eval' => 'required',
			)
		),
		'file_path' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_path',
			'config' => Array (
				'type' => 'none',
				'size' => '25',
				'max' => '100',
				'eval' => 'required',
			)
		),
		'file_dl_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_dl_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'trim',
			)
		),
		'file_type' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_type',
			'config' => Array (
				'type' => 'none',
				'size' => '4',
				'max' => '4',
				'eval' => 'required,trim',
			)
		),


		'file_type_version' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_type_version',
			'exclude' => '0',
			'config' => Array (
				'type' => 'none',
				'size' => '6',
				'max' => '9',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_size' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_size',
			'exclude' => '0',
			'config' => Array (
				'type' => 'none',
				'size' => '6',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'file_orig_location' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_orig_location',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '45',
				'max' => '255',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_orig_loc_desc' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_orig_loc_desc',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '45',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_creator' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_creator',
			'exclude' => '0',
			'config' => Array (
				'type' => 'none',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_mime_type' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_mime_type',
			'exclude' => '0',
			'config' => Array (
				'type' => 'none',
				'form_type' => 'user',
				'userFunc' => 'tx_dam_tceFunc->tx_dam_file_mime_type',
				'size' => '20',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'file_mime_subtype' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_mime_subtype',
			'exclude' => '0',
			'config' => Array (
				'type' => 'none',
				'size' => '20',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),

		'file_ctime' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_ctime',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'readonly' => '1',
				'size' => '11',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
			)
		),
		'file_mtime' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_mtime',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '11',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
			)
		),


		'file_usage' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.file_usage',
			'exclude' => '0',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_dam_tceFunc->tx_dam_fileUsage',
				'noTableWrapping' => TRUE,
			)
		),


		/*
		 * THUMB ###########################################
		 */

		'thumb' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.thumb',
			'exclude' => '0',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_dam_tceFunc->tx_dam_thumb',
				'noTableWrapping' => TRUE,
			)
		),
		'thumb_path' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.thumb_path',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '255',
				'eval' => '',
				'default' => ''
			)
		),



		/*
		 * COPYRIGHT ###########################################
		 */

		'ident' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.ident',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'creator' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.creator',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'publisher' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.publisher',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'copyright' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.copyright',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '35',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),


		/*
		 * META DESCRIPTION ###########################################
		 */

		'keywords' => Array (
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.keywords',
			'config' => Array (
				'type' => 'input',
				'size' => '45',
				'eval' => 'trim'
			)
		),
		'description' => Array (
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.description',
			'config' => Array (
				'type' => 'text',
				'cols' => '40',
				'rows' => '3'
			)
		),
		'instructions' => Array (
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.instructions',
			'config' => Array (
				'type' => 'text',
				'cols' => '40',
				'rows' => '2'
			)
		),
		'abstract' => Array (
			'exclude' => '1',
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.abstract',
			'config' => Array (
				'type' => 'none',
				'cols' => '40',
				'rows' => '3',
				'eval' => 'trim',
			)
		),
		'date_cr' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.date_cr',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
			)
		),
		'date_mod' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.date_mod',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
			)
		),
		'loc_desc' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.loc_desc',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '45',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'loc_country' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.loc_country',
			'exclude' => '0',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('','')
				),
				'foreign_table' => 'static_countries',
				'rootLevel' => '1',
				'size' => '1',
				'maxitems' => '1',
				'default' => ''
			)
		),

		'loc_city' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.loc_city',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => ''
			)
		),
		'language' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.language',
			'exclude' => '0',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',''),
				),
				'size' => '1',
				'maxitems' => '1',
				'default' => '',
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_languages',
					'indexField' => 'lg_iso_2',
					'prependHotlist' => 1,
					'hotlistApp' => 'dam',
				),
			)
		),




		/*
		 * TECHNICAL ###########################################
		 */

		'hres' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.hres',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'vres' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.vres',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'hpixels' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.hpixels',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'vpixels' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.vpixels',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'color_space' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.color_space',
			'exclude' => '0',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', ''),
					Array('RGB', 'RGB'),
					Array('sRGB', 'sRGB'),
					Array('CMYK', 'CMYK'),
					Array('CMY', 'CMY'),
					Array('YUV', 'YUV'),
					Array('indexed', 'indx'),
				),
				'default' => ''
			)
		),
		'width' => Array (
				'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.width',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'height' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.height',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'height_unit' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.height_unit',
			'exclude' => '0',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', ''),
					Array('px', 'px'),
					Array('mm', 'mm'),
					Array('cm', 'cm'),
					Array('m', 'm'),
					Array('p', 'p'),
				),
				'default' => ''
			)
		),
		'pages' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.pages',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '20',
				'eval' => 'int',
				'default' => '0'
			)
		) ,



		/*
		 * CATEGORY
		 */

		'category' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_field'],

		/*
		Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'exclude' => '0',
			'config' => Array (
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_dam_cObjFunc->getSingleField_selectTree',

				'treeView' => 1,
				'foreign_table' => 'tx_dam_cat',
				# 'foreign_table_where' => 'AND tx_dam_cat.pid=###CURRENT_PID### ORDER BY tx_dam_cat.uid',
				'size' => 10,
				'minitems' => 0,
				'maxitems' => 20,
				'MM' => 'tx_dam_mm_cat',

			)
		),
		*/

	),
	/*
$txdamTypes['media2Codes'] = array (
	'undefined' => '0',
	'text' => '1',
	'image' => '2',
	'audio' => '3',
	'video' => '4',
	'interactive' => '5',
	'service' => '6',
	'font' => '7',
	'model' => '8',
	'dataset' => '9',
	'collection' => '10',
	'software' => '11',
	'application' => '12',
);
*/
	'types' => Array (
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'hidden,starttime, endtime, fe_group', 'canNotCollapse' => '1'),

		'3' => Array('showitem' => 'loc_desc', 'canNotCollapse' => '1'),
		'4' => Array('showitem' => 'hpixels,vpixels', 'canNotCollapse' => '1'),
		'5' => Array('showitem' => 'loc_country,loc_city', 'canNotCollapse' => '1'),
		'6' => Array('showitem' => 'file_name,file_path', 'canNotCollapse' => '1'),
		'7' => Array('showitem' => 'file_size,file_type,file_mime_type', 'canNotCollapse' => '1'),
		'8' => Array('showitem' => 'file_ctime,file_mtime', 'canNotCollapse' => '1'),
		'9' => Array('showitem' => 'creator,publisher', 'canNotCollapse' => '1'),
		'10' => Array('showitem' => 'width,height,height_unit', 'canNotCollapse' => '1'),
		'11' => Array('showitem' => 'thumb,thumb_path'),
		'12' => Array('showitem' => 'file_creator,file_type_version', 'canNotCollapse' => '1'),
		'13' => Array('showitem' => 'date_cr,date_mod', 'canNotCollapse' => '1'),
		'14' => Array('showitem' => 'hres,vres', 'canNotCollapse' => '1'),
	)
);

$tx_dam_header = 'media_type;;;;3-3-3, thumb, ';


$tx_dam_descr = 'title;;;;3-3-3, keywords, description, --palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_abstract = 'title;;;;3-3-3, keywords, description, abstract;;;;3-3-3, --palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_txt = 'title;;;;3-3-3, keywords, description, abstract;;;;3-3-3, language, pages, --palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.date_pheader;13;;, ';
$tx_dam_descr_img = 'title;;;;3-3-3, keywords, description, --palette--;Location:||Ortsangabe:;5;;, --palette--;;3;;, --palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.date_pheader;13;;, ';

$tx_dam_metrics_img = 'color_space;;;;4-4-4, --palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.metrics;4, --palette--;;10;;, --palette--;;14;;, ';
$tx_dam_metrics_txt = '--palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.metrics;10;;4-4-4, ';

$tx_dam_file = '--palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.file_pheader;6;;3-3-3, --palette--;;7;;, --palette--;;8;;, --palette--;;12;;, file_dl_name, file_orig_location;;;;, file_orig_loc_desc, ';

$tx_dam_copyright = 'creator;;;;3-3-3, publisher, copyright, ident, ';
$tx_dam_category = 'category;;;;4-4-4, ';

$tx_dam_frontend = '--palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.frontend_pheader;1;;1-1-1';

$tx_dam_usage = 'instructions;;;;3-3-3, file_usage, ';


$tx_dam_footer = $tx_dam_usage.$tx_dam_category.$tx_dam_frontend;

$TCA['tx_dam']['types'] = Array (
	/* undefined */
	'0' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* text */
	'1' => Array('showitem' => $tx_dam_header.$tx_dam_descr_txt.$tx_dam_metrics_txt.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* image */
	'2' => Array('showitem' => $tx_dam_header.$tx_dam_descr_img.$tx_dam_metrics_img.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* audio */
	'3' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* video */
	'4' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* interactive */
	'5' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* service */
	'6' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* font */
	'7' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* model */
	'8' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* dataset */
	'9' => Array('showitem' => $tx_dam_header.$tx_dam_descr_abstract.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* collection */
	'10' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* software */
	'11' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
	/* application */
	'12' => Array('showitem' => $tx_dam_header.$tx_dam_descr.$tx_dam_file.$tx_dam_copyright.$tx_dam_footer),
);

#t3lib_extMgm::addTCAcolumns('tx_dam',$tempCatalogSelector,1);

$TCA['tx_dam_cat'] = Array (
	'ctrl' => $TCA['tx_dam_cat']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,fe_group,title'
	),
	'feInterface' => $TCA['tx_dam_cat']['feInterface'],

	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '1'
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'nav_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cms/locallang_tca.php:pages.nav_title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '256',
				'checkbox' => '',
				'eval' => 'trim'
			)
		),
		'subtitle' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:cms/locallang_tca.php:pages.subtitle',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '256',
				'eval' => ''
			)
		),
		'keywords' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.keywords',
			'config' => Array (
				'type' => 'text',
				'cols' => '40',
				'rows' => '3'
			)
		),
		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.description',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'eval' => 'trim'
			)
		),
/*
		'parent_id' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_cat_item.parent_id',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_dam_cat',
				'size' => '3',
				'maxitems' => '1',
				'minitems' => '0',
				'show_thumbs' => '1'
			)
		),
*/
		'parent_id' => Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_cat_item.parent_id',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'],
		),



	),
	'types' => Array (
		'1' => Array (
			'showitem' => 'title,subtitle,nav_title,description,keywords,parent_id,--palette--;LLL:EXT:dam/locallang_db.php:tx_dam_item.frontend_pheader;1;;1-1-1'
		)
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'hidden,fe_group'),
	)

);

?>