<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');



if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}

if (!defined ('PATH_txdam_siteRel')) {
	define('PATH_txdam_siteRel', t3lib_extMgm::siteRelPath('dam'));
}




	// extend beusers for access control
$tempColumns = array(
	'tx_dam_mountpoints' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:dam/locallang_db.xml:label.tx_dam_mountpoints',
		'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['mountpoints_config'],
	),
);

t3lib_div::loadTCA('be_groups');
t3lib_extMgm::addTCAcolumns('be_groups',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_groups','tx_dam_mountpoints','','after:file_mountpoints');

t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','tx_dam_mountpoints','','after:fileoper_perms');

unset($tempColumns);



	// extend tt_content with fields for general usage
$tempColumns = array(
	'tx_dam_images' => txdam_getMediaTCA('image_field', 'tx_dam_images'),
	'tx_dam_files' => txdam_getMediaTCA('media_field', 'tx_dam_files'),
	'tx_dam_flexform' => array(
		'l10n_display' => 'hideDiff',
		'label' => 'LLL:EXT:cms/locallang_ttc.php:pi_flexform',
		'config' => Array (
			'type' => 'flex',
			'ds_pointerField' => 'CType',
			'ds' => array(
				'default' => '
					<T3DataStructure>
					  <ROOT>
					    <type>array</type>
					    <el>
							<!-- Repeat an element like "xmlTitle" beneath for as many elements you like. Remember to name them uniquely  -->
					      <xmlTitle>
							<TCEforms>
								<label>The Title:</label>
								<config>
									<type>input</type>
									<size>48</size>
								</config>
							</TCEforms>
					      </xmlTitle>
					    </el>
					  </ROOT>
					</T3DataStructure>
				',
			)
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

unset($tempColumns);



if (TYPO3_MODE=='BE')	{

		// this forces the DAM sysfolder to be created if not yet available
	$temp_damFolder = tx_dam_db::getPid();
	if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['hideMediaFolder']) {
		t3lib_extMgm::addUserTSConfig('
			options.hideRecords.pages = '.$temp_damFolder.'
		');
	}

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

			// remove Web>File module
		if(!$TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['web_file']) {
			unset($temp_TBE_MODULES['file']);
		}
		$TBE_MODULES = $temp_TBE_MODULES;
		unset($temp_TBE_MODULES);
	}

		// add main module
	t3lib_extMgm::addModule('txdamM1','','',PATH_txdam.'mod_main/');


		// add file module
	t3lib_extMgm::addModule('txdamM1','file','',PATH_txdam.'mod_file/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_file',
		'tx_dam_file_list',
		PATH_txdam.'modfunc_file_list/class.tx_dam_file_list.php',
		'LLL:EXT:dam/modfunc_file_list/locallang.xml:tx_dam_file_list.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_file',
		'tx_dam_file_upload',
		PATH_txdam.'modfunc_file_upload/class.tx_dam_file_upload.php',
		'LLL:EXT:dam/modfunc_file_upload/locallang.xml:tx_dam_file_upload.title'

	);


		// add list module
	t3lib_extMgm::addModule('txdamM1','list','',PATH_txdam.'mod_list/');

		// insert module functions into list module
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_list',
		PATH_txdam.'modfunc_list_list/class.tx_dam_list_list.php',
		'LLL:EXT:dam/modfunc_list_list/locallang.xml:tx_dam_list_list.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_thumbs',
		PATH_txdam.'modfunc_list_thumbs/class.tx_dam_list_thumbs.php',
		'LLL:EXT:dam/modfunc_list_thumbs/locallang.xml:tx_dam_list_thumbs.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_editsel',
		PATH_txdam.'modfunc_list_editsel/class.tx_dam_list_editsel.php',
		'LLL:EXT:dam/modfunc_list_editsel/locallang.xml:tx_dam_list_editsel.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_dam_list_batch',
		PATH_txdam.'modfunc_list_batch/class.tx_dam_list_batch.php',
		'LLL:EXT:dam/modfunc_list_batch/locallang.xml:tx_dam_list_batch.title'
	);



	t3lib_extMgm::addModule('txdamM1','tools','',PATH_txdam.'mod_tools/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_indexsetup',
		PATH_txdam.'modfunc_tools_indexsetup/class.tx_dam_tools_indexsetup.php',
		'LLL:EXT:dam/modfunc_tools_indexsetup/locallang.xml:tx_dam_tools_indexsetup.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_indexupdate',
		PATH_txdam.'modfunc_tools_indexupdate/class.tx_dam_tools_indexupdate.php',
		'LLL:EXT:dam/modfunc_tools_indexupdate/locallang.xml:tx_dam_tools_indexupdate.title'
	);

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_tools',
		'tx_dam_tools_filerelcheck',
		PATH_txdam.'modfunc_tools_filerelcheck/class.tx_dam_tools_filerelcheck.php',
		'LLL:EXT:dam/modfunc_tools_filerelcheck/locallang.xml:tx_dam_tools_filerelcheck.title'
	);


		// command modules (invisible)
	t3lib_extMgm::addModule('txdamM1','cmd','',PATH_txdam.'mod_cmd/');

	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_nothing',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_nothing.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_nothing.title'
	);

		// file command modules (invisible)
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filerename',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filerename.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filereplace',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filereplace.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filereplace.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filedelete',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filedelete.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filedelete.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_filenew',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_filenew.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filenew.title'
	);

		// folder command modules (invisible)
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_foldernew',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_foldernew.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_foldernew.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_folderdelete',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_folderdelete.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_cmd',
		'tx_dam_cmd_folderrename',
		PATH_txdam.'mod_cmd/class.tx_dam_cmd_folderrename.php',
		'LLL:EXT:dam/mod_cmd/locallang.xml:tx_dam_cmd_filerename.title'
	);


		// add context menu
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][]=array(
		'name' => 'tx_dam_cm1',
		'path' => PATH_txdam.'class.tx_dam_cm1.php'
	);


		// media folder type and icon
	$ICON_TYPES['dam'] = array('icon' => PATH_txdam_rel.'modules_dam.gif');
	$TCA['pages']['columns']['module']['config']['items'][] = array('Media', 'dam');



		// language hotlist
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:dam/class.tx_dam_tce_languagehotlist.php:&tx_dam_tce_languagehotlist';



	tx_dam::register_action ('tx_dam_action_newFolder',    'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_newFolder');
	tx_dam::register_action ('tx_dam_action_renameFolder', 'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_renameFolder');
	tx_dam::register_action ('tx_dam_action_deleteFolder', 'EXT:dam/components/class.tx_dam_actionsFolder.php:&tx_dam_action_deleteFolder');

	tx_dam::register_action ('tx_dam_action_newTextfile',     'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_newTextfile');
	tx_dam::register_action ('tx_dam_action_editFileRecord',  'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_editFileRecord');
	tx_dam::register_action ('tx_dam_action_viewFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_viewFile');
	tx_dam::register_action ('tx_dam_action_infoFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_infoFile');
#	tx_dam::register_action ('tx_dam_action_editFile',        'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_editFile');
	tx_dam::register_action ('tx_dam_action_renameFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_renameFile');
	tx_dam::register_action ('tx_dam_action_replaceFile',     'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_replaceFile');
	tx_dam::register_action ('tx_dam_action_deleteFile',      'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_deleteFile');
#	tx_dam::register_action ('tx_dam_action_deleteFileQuick', 'EXT:dam/components/class.tx_dam_actionsFile.php:&tx_dam_action_deleteFileQuick');

	tx_dam::register_action ('tx_dam_action_editRec',        'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_editRec');
	tx_dam::register_action ('tx_dam_action_viewFileRec',        'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_viewFileRec');
	tx_dam::register_action ('tx_dam_action_infoRec',        'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_infoRec');
	tx_dam::register_action ('tx_dam_action_revertRec',      'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_revertRec');
	tx_dam::register_action ('tx_dam_action_hideRec',        'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_hideRec');
	tx_dam::register_action ('tx_dam_action_deleteRec',      'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_deleteRec');
//	tx_dam::register_action ('tx_dam_action_deleteQuickRec',      'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_deleteQuickRec');
	tx_dam::register_action ('tx_dam_action_lockWarningRec', 'EXT:dam/components/class.tx_dam_actionsRecord.php:&tx_dam_action_lockWarningRec');


	tx_dam::register_previewer ('tx_dam_previewerImage', 'EXT:dam/components/class.tx_dam_previewerImage.php:&tx_dam_previewerImage');
	tx_dam::register_previewer ('tx_dam_previewerMP3',   'EXT:dam/components/class.tx_dam_previewerMP3.php:&tx_dam_previewerMP3');

}




tx_dam::register_mediaTable ('tx_dam');
t3lib_extMgm::allowTableOnStandardPages('tx_dam');

t3lib_extMgm::addLLrefForTCAdescr('tx_dam','EXT:dam/locallang_csh_dam.xml');

$TCA['tx_dam'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'media_type',
		'sortby' => 'title',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',

		'versioningWS' => true,

		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',

		'useColumnsForDefaultValues' => '',

		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => '1',
		'typeicon_column' => 'media_type',
// TODO move icon to somewhere else?
		'typeicons' => array(
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

		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, media_type, title, file_type',
	),
	'txdamInterface' => array(
		'index_fieldList' => 'title,keywords,description,caption,alt_text,file_orig_location,file_orig_loc_desc,ident,creator,publisher,copyright,instructions,date_cr,date_mod,loc_desc,loc_country,loc_city,language,category',
		'info_fieldList_add' => '',
// TODO ?
		'xinfo_displayFields_exclude' => 'category',
		'info_displayFields_isNonEditable' => 'media_type,thumb,file_usage',
	),
);


tx_dam::register_mediaTable ('tx_dam_cat');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_cat');

$TCA['tx_dam_cat'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_cat_item',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY sorting,title',
		'delete' => 'deleted',
		'treeParentField' => 'parent_id',

		'versioningWS' => true,

		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',

		'enablecolumns' => array(
			'disabled' => 'hidden',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam_cat.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, fe_group, title',
	)
);




tx_dam::register_mediaTable ('tx_dam_selection');
t3lib_extMgm::allowTableOnStandardPages('tx_dam_selection');

$TCA['tx_dam_selection'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_selection',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'versioning' => '0',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => PATH_txdam.'tca.php',
		'iconfile' => PATH_txdam_rel.'icon_tx_dam_selection.gif',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, type, title, definition',
	)
);



?>