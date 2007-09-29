<?php

########################################################################
# Extension Manager/Repository config file for ext: "dam"
#
# Auto generated 29-09-2007 00:30
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Media (DAM)',
	'description' => 'The Digital Asset Management (DAM) is simply a tool for organizing digital media assets for storage and retrieval. Metadata can be used to search and organize image, text, audio, video (...) files.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod_main,mod_list,mod_cmd,mod_tools',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'René Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.3.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
			'cms' => '',
			'static_info_tables' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:145:{s:20:"class.tx_dam_cm1.php";s:4:"9754";s:32:"class.tx_dam_languagehotlist.php";s:4:"282f";s:28:"class.ux_SC_browse_links.php";s:4:"6320";s:25:"class.ux_SC_show_item.php";s:4:"fefb";s:21:"ext_conf_template.txt";s:4:"cd56";s:12:"ext_icon.gif";s:4:"999b";s:17:"ext_localconf.php";s:4:"acf7";s:15:"ext_php_api.dat";s:4:"eae7";s:14:"ext_tables.php";s:4:"99c1";s:14:"ext_tables.sql";s:4:"04e2";s:15:"icon_tx_dam.gif";s:4:"999b";s:19:"icon_tx_dam_cat.gif";s:4:"2596";s:16:"locallang_cm.php";s:4:"b9eb";s:16:"locallang_db.php";s:4:"5a83";s:15:"modules_dam.gif";s:4:"06f8";s:7:"tca.php";s:4:"f735";s:19:"tca_media_field.inc";s:4:"4834";s:42:"modfunc_list_list/class.tx_dam_db_list.php";s:4:"32e8";s:44:"modfunc_list_list/class.tx_dam_list_list.php";s:4:"4d0d";s:31:"modfunc_list_list/locallang.php";s:4:"f686";s:38:"compat/class.ux_alt_menu_functions.php";s:4:"106f";s:37:"compat/class.ux_t3lib_loaddbgroup.php";s:4:"560a";s:33:"compat/class.ux_t3lib_tcemain.php";s:4:"7565";s:38:"compat/class.ux_t3lib_transferdata.php";s:4:"1500";s:28:"compat/class.ux_template.php";s:4:"5828";s:46:"modfunc_list_batch/class.tx_dam_list_batch.php";s:4:"5dd0";s:32:"modfunc_list_batch/locallang.php";s:4:"c2c7";s:39:"mod_cmd/class.tx_dam_cmd_filedelete.php";s:4:"e25e";s:39:"mod_cmd/class.tx_dam_cmd_filerename.php";s:4:"4fcb";s:40:"mod_cmd/class.tx_dam_cmd_filereplace.php";s:4:"bc7c";s:36:"mod_cmd/class.tx_dam_cmd_nothing.php";s:4:"1f2a";s:16:"mod_cmd/conf.php";s:4:"076f";s:17:"mod_cmd/index.php";s:4:"9d06";s:21:"mod_cmd/locallang.php";s:4:"6b32";s:25:"mod_cmd/locallang_mod.php";s:4:"9afe";s:22:"mod_cmd/moduleicon.gif";s:4:"adc5";s:17:"mod_main/conf.php";s:4:"4bea";s:26:"mod_main/locallang_mod.php";s:4:"77d6";s:23:"mod_main/moduleicon.gif";s:4:"3833";s:28:"mod_main/tx_dam_navframe.php";s:4:"ef2b";s:20:"lib/class.tx_dam.php";s:4:"5aac";s:33:"lib/class.tx_dam_batchprocess.php";s:4:"97f5";s:32:"lib/class.tx_dam_browsetrees.php";s:4:"2618";s:23:"lib/class.tx_dam_db.php";s:4:"d370";s:24:"lib/class.tx_dam_div.php";s:4:"cea8";s:29:"lib/class.tx_dam_filelist.php";s:4:"1b99";s:29:"lib/class.tx_dam_indexing.php";s:4:"b64d";s:34:"lib/class.tx_dam_indexrulebase.php";s:4:"e17f";s:29:"lib/class.tx_dam_navframe.php";s:4:"1d5c";s:29:"lib/class.tx_dam_querygen.php";s:4:"7210";s:27:"lib/class.tx_dam_scbase.php";s:4:"07d0";s:30:"lib/class.tx_dam_selection.php";s:4:"0de6";s:32:"lib/class.tx_dam_selprocbase.php";s:4:"c64b";s:32:"lib/class.tx_dam_simpleforms.php";s:4:"dcb2";s:33:"lib/class.tx_dam_stdselection.php";s:4:"ed63";s:27:"lib/class.tx_dam_svlist.php";s:4:"1d8c";s:29:"lib/class.tx_dam_tce_file.php";s:4:"2a27";s:28:"lib/class.tx_dam_tcefunc.php";s:4:"a751";s:26:"lib/class.tx_dam_types.php";s:4:"e3df";s:17:"lib/locallang.php";s:4:"9593";s:48:"modfunc_list_thumbs/class.tx_dam_list_thumbs.php";s:4:"3efa";s:33:"modfunc_list_thumbs/locallang.php";s:4:"007a";s:21:"i/button_deselect.gif";s:4:"7903";s:19:"i/button_remove.gif";s:4:"0414";s:21:"i/button_reselect.gif";s:4:"f06a";s:9:"i/cat.gif";s:4:"0e9a";s:10:"i/cat2.gif";s:4:"89a7";s:16:"i/cat2folder.gif";s:4:"b7f4";s:10:"i/cat3.gif";s:4:"2ed1";s:16:"i/cat3folder.gif";s:4:"2797";s:15:"i/catfolder.gif";s:4:"a16b";s:21:"i/cm_replace_file.gif";s:4:"bdcc";s:12:"i/equals.gif";s:4:"fcb9";s:15:"i/equals_16.gif";s:4:"54a3";s:23:"i/media-application.png";s:4:"b316";s:17:"i/media-audio.png";s:4:"ec87";s:22:"i/media-collection.png";s:4:"3089";s:19:"i/media-dataset.png";s:4:"05eb";s:16:"i/media-font.png";s:4:"5651";s:17:"i/media-image.png";s:4:"5342";s:23:"i/media-interactive.png";s:4:"3b67";s:17:"i/media-model.png";s:4:"b6e0";s:19:"i/media-service.png";s:4:"13cd";s:20:"i/media-software.png";s:4:"3d09";s:16:"i/media-text.png";s:4:"c844";s:21:"i/media-undefined.png";s:4:"bc98";s:18:"i/media2-video.png";s:4:"cc04";s:17:"i/mediafolder.gif";s:4:"f898";s:15:"i/mediatype.gif";s:4:"769b";s:16:"i/mimefolder.gif";s:4:"16bd";s:14:"i/mimetype.gif";s:4:"379f";s:11:"i/minus.gif";s:4:"5f98";s:14:"i/minus_16.gif";s:4:"cfa1";s:10:"i/plus.gif";s:4:"80dd";s:13:"i/plus_16.gif";s:4:"ab3d";s:18:"i/statusfolder.gif";s:4:"efdc";s:16:"i/statustype.gif";s:4:"062f";s:11:"i/18/au.gif";s:4:"042a";s:12:"i/18/doc.gif";s:4:"0975";s:12:"i/18/dot.gif";s:4:"0975";s:19:"i/18/file-icons.xcf";s:4:"d654";s:13:"i/18/html.gif";s:4:"5647";s:12:"i/18/jpg.gif";s:4:"dee4";s:26:"i/18/mtype_application.gif";s:4:"1552";s:20:"i/18/mtype_audio.gif";s:4:"042a";s:25:"i/18/mtype_collection.gif";s:4:"9a2f";s:22:"i/18/mtype_dataset.gif";s:4:"4285";s:19:"i/18/mtype_font.gif";s:4:"7afa";s:20:"i/18/mtype_image.gif";s:4:"1d47";s:26:"i/18/mtype_interactive.gif";s:4:"c40b";s:20:"i/18/mtype_model.gif";s:4:"bf41";s:22:"i/18/mtype_service.gif";s:4:"bf41";s:23:"i/18/mtype_software.gif";s:4:"3e9f";s:19:"i/18/mtype_text.gif";s:4:"6c86";s:24:"i/18/mtype_undefined.gif";s:4:"bf41";s:20:"i/18/mtype_video.gif";s:4:"9b60";s:12:"i/18/pdf.gif";s:4:"49a4";s:13:"i/18/pdf2.gif";s:4:"9451";s:12:"i/18/swf.gif";s:4:"c584";s:12:"i/18/ttf.gif";s:4:"9af9";s:12:"i/18/txt.gif";s:4:"f09a";s:12:"i/18/zip.gif";s:4:"9a2f";s:18:"mod_list/clear.gif";s:4:"cc11";s:17:"mod_list/conf.php";s:4:"3d1d";s:18:"mod_list/index.php";s:4:"fc54";s:22:"mod_list/locallang.php";s:4:"a91a";s:26:"mod_list/locallang_mod.php";s:4:"4536";s:23:"mod_list/moduleicon.gif";s:4:"adc5";s:62:"modfunc_tools_filerelcheck/class.tx_dam_tools_filerelcheck.php";s:4:"de17";s:40:"modfunc_tools_filerelcheck/locallang.php";s:4:"e3cb";s:19:"mod_tools/clear.gif";s:4:"cc11";s:18:"mod_tools/conf.php";s:4:"a6b0";s:19:"mod_tools/index.php";s:4:"ea4c";s:23:"mod_tools/locallang.php";s:4:"f4dc";s:27:"mod_tools/locallang_mod.php";s:4:"b64d";s:24:"mod_tools/moduleicon.gif";s:4:"cce3";s:29:"mod_tools/tx_dam_navframe.php";s:4:"8e5f";s:50:"modfunc_list_editsel/class.tx_dam_list_editsel.php";s:4:"71ce";s:34:"modfunc_list_editsel/locallang.php";s:4:"d032";s:12:"doc/TODO.txt";s:4:"a883";s:17:"doc/changelog.txt";s:4:"473b";s:16:"doc/ext_icon.xcf";s:4:"3516";s:17:"doc/ext_icon2.xcf";s:4:"ee3e";s:14:"doc/manual.sxw";s:4:"a7d9";s:15:"doc/roadmap.txt";s:4:"0070";}',
);

?>