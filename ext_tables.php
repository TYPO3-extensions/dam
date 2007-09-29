<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');




if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}




$tempColumns = Array (
	'tx_dam_mountpoints' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:dam/locallang_db.php:label.tx_dam_mountpoints',
		'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['mountpoints_config'],
	),
);


t3lib_div::loadTCA('be_groups');
t3lib_extMgm::addTCAcolumns('be_groups',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_groups','tx_dam_mountpoints','','after:file_mountpoints');


t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_dam_mountpoints','','after:fileoper_perms');





if (TYPO3_MODE=='BE')	{

		// add module after 'File'
	if (!isset($TBE_MODULES['txdamM1']))	{
		$temp_TBE_MODULES = array();
		foreach($TBE_MODULES as $key => $val) {
			if ($key=='file') {
				$temp_TBE_MODULES[$key] = $val;
				$temp_TBE_MODULES['txdamM1'] = $val;
			} else {
				$temp_TBE_MODULES[$key] = $val;
			}
		}
		$TBE_MODULES = $temp_TBE_MODULES;
	}

		// add main module
	t3lib_extMgm::addModule('txdamM1','','',PATH_txdam.'mod_main/');
		// add list module
	t3lib_extMgm::addModule('txdamM1','list','',PATH_txdam.'mod_list/');

		// insert module functions into list module
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_list',
		PATH_txdam.'modfunc_list_list/class.tx_dam_list_list.php',
		'LLL:EXT:dam/modfunc_list_list/locallang.php:tx_dam_list_list.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_thumbs',
		PATH_txdam.'modfunc_list_thumbs/class.tx_dam_list_thumbs.php',
		'LLL:EXT:dam/modfunc_list_thumbs/locallang.php:tx_dam_list_thumbs.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_editsel',
		PATH_txdam.'modfunc_list_editsel/class.tx_dam_list_editsel.php',
		'LLL:EXT:dam/modfunc_list_editsel/locallang.php:tx_dam_list_editsel.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_batch',
		PATH_txdam.'modfunc_list_batch/class.tx_dam_list_batch.php',
		'LLL:EXT:dam/modfunc_list_batch/locallang.php:tx_dam_list_batch.title'
	);



	t3lib_extMgm::addModule('txdamM1','txdamMtools','',PATH_txdam.'mod_tools/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_txdamMtools',
		'tx_dam_tools_filerelcheck',
		PATH_txdam.'modfunc_tools_filerelcheck/class.tx_dam_tools_filerelcheck.php',
		'LLL:EXT:dam/modfunc_tools_filerelcheck/locallang.php:tx_dam_tools_filerelcheck.title'
	);


		// command modules (invisible)
	t3lib_extMgm::addModule('txdamM1','cmd','',PATH_txdam.'mod_cmd/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_nothing',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_nothing.php',
		'LLL:EXT:dam/mod_cmd/locallang.php:tx_dam_cmd_nothing.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filerename',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filerename.php',
		'LLL:EXT:dam/mod_cmd/locallang.php:tx_dam_cmd_filerename.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filereplace',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filereplace.php',
		'LLL:EXT:dam/mod_cmd/locallang.php:tx_dam_cmd_filereplace.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filedelete',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filedelete.php',
		'LLL:EXT:dam/mod_cmd/locallang.php:tx_dam_cmd_filedelete.title'
	);


		// add context menu
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_dam_cm1',
		'path' => PATH_txdam.'class.tx_dam_cm1.php'
	);


		// media folder type and icon
	$ICON_TYPES['dam'] = array('icon' => PATH_txdam_rel.'modules_dam.gif');
	$TCA['pages']['columns']['module']['config']['items'][] = array('Mediabase', 'dam');



		// language hotlist
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/class.tx_dam_languagehotlist.php:&tx_dam_languagehotlist';

}




$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamFolder'] = 'EXT:dam/lib/class.tx_dam_stdselection.php:&tx_dam_stdselectionFolder';

$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamCat'] = 'EXT:dam/lib/class.tx_dam_stdselection.php:&tx_dam_stdselectionCategory';

$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamMedia'] = 'EXT:dam/lib/class.tx_dam_stdselection.php:&tx_dam_stdselectionMeTypes';

$TYPO3_CONF_VARS['EXTCONF']['dam']['selectionClasses']['txdamStrSearch'] = 'EXT:dam/lib/class.tx_dam_stdselection.php:&tx_dam_stringSearch';




t3lib_extMgm::allowTableOnStandardPages('tx_dam');

$TCA['tx_dam'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'media_type',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting,title',
		'delete' => 'deleted',

		'versioning' => false,
		'copyAfterDuplFields' => 'sys_language_uid',
		'useColumnsForDefaultValues' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',


//		"requestUpdate" => "category",
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => '1',
		'typeicon_column' => 'media_type',
		'typeicons' => array (
			'0' => PATH_txdam_rel.'i/18/mtype_undefined.gif',
			'1' => PATH_txdam_rel.'i/18/mtype_text.gif',
			'2' => PATH_txdam_rel.'i/18/mtype_image.gif',
			'3' => PATH_txdam_rel.'i/18/mtype_audio.gif',
			'4' => PATH_txdam_rel.'i/18/mtype_video.gif',
			'5' => PATH_txdam_rel.'i/18/mtype_interactive.gif',
			'6' => PATH_txdam_rel.'i/18/mtype_service.gif',
			'7' => PATH_txdam_rel.'i/18/mtype_font.gif',
			'8' => PATH_txdam_rel.'i/18/mtype_model.gif',
			'9' => PATH_txdam_rel.'i/18/mtype_dataset.gif',
			'10' => PATH_txdam_rel.'i/18/mtype_collection.gif',
			'11' => PATH_txdam_rel.'i/18/mtype_software.gif',
			'12' => PATH_txdam_rel.'i/18/mtype_application.gif',
		),

//$txdamTypes['media2Codes'] = array (
//	'undefined' => '0',
//	'text' => '1',
//	'image' => '2',
//	'audio' => '3',
//	'video' => '4',
//	'interactive' => '5',
//	'service' => '6',
//	'font' => '7',
//	'model' => '8',
//	'dataset' => '9',
//	'collection' => '10',
//	'software' => '11',
//	'application' => '12',
//);

		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam.gif',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, media_type, title, file_type',
	),
	'txdamInterface' => array (
		'index_fieldList' => 'title,keywords,description,file_orig_location,file_orig_loc_desc,ident,creator,publisher,copyright,instructions,date_cr,date_mod,loc_desc,loc_country,loc_city,language,category',
		'info_fieldList_exclude' => 'category',
		'info_fieldList_isNonEditable' => 'media_type,thumb,file_usage',
	),
);

t3lib_extMgm::allowTableOnStandardPages('tx_dam_cat');

$TCA['tx_dam_cat'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:dam/locallang_db.php:tx_dam_cat_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting,title',
		'delete' => 'deleted',
		'treeParentField' => 'parent_id',
		'enablecolumns' => array (
			'disabled' => 'hidden',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam_cat.gif',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, fe_group, title',
	)
);

?>