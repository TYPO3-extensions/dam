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
	'description' => 'The Digital Asset Management (DAM) is simply a tool for organizing digital media assets for storage and retrieval. Metadata can be used to search and organize image, text, audio, video (...) files. Needs TYPO3 V. 3.7',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod_main,mod_list,mod_cmd',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'René Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.2.2',
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
	'_md5_values_when_last_written' => 'a:162:{s:20:"class.tx_dam_cm1.php";s:4:"9754";s:26:"class.tx_dam_elbrowser.php";s:4:"5da6";s:32:"class.tx_dam_languagehotlist.php";s:4:"282f";s:31:"class.ux_alt_menu_functions.php";s:4:"a572";s:12:"ext_icon.gif";s:4:"49ba";s:17:"ext_localconf.php";s:4:"7a11";s:15:"ext_php_api.dat";s:4:"eae7";s:14:"ext_tables.php";s:4:"adb9";s:14:"ext_tables.sql";s:4:"2895";s:15:"icon_tx_dam.gif";s:4:"49ba";s:19:"icon_tx_dam_cat.gif";s:4:"49ba";s:16:"locallang_cm.php";s:4:"b9eb";s:16:"locallang_db.php";s:4:"f490";s:15:"modules_dam.gif";s:4:"06f8";s:13:"project.index";s:4:"f360";s:7:"tca.php";s:4:"007b";s:19:"tca_media_field.inc";s:4:"9695";s:42:"modfunc_list_list/class.tx_dam_db_list.php";s:4:"10ce";s:44:"modfunc_list_list/class.tx_dam_list_list.php";s:4:"6b9e";s:31:"modfunc_list_list/locallang.php";s:4:"f686";s:46:"modfunc_list_batch/class.tx_dam_list_batch.php";s:4:"226d";s:32:"modfunc_list_batch/locallang.php";s:4:"c2c7";s:39:"mod_cmd/class.tx_dam_cmd_filedelete.php";s:4:"ba71";s:39:"mod_cmd/class.tx_dam_cmd_filerename.php";s:4:"9fc4";s:40:"mod_cmd/class.tx_dam_cmd_filereplace.php";s:4:"abf4";s:36:"mod_cmd/class.tx_dam_cmd_nothing.php";s:4:"a332";s:16:"mod_cmd/conf.php";s:4:"2d03";s:17:"mod_cmd/index.php";s:4:"7242";s:21:"mod_cmd/locallang.php";s:4:"6b32";s:25:"mod_cmd/locallang_mod.php";s:4:"9afe";s:22:"mod_cmd/moduleicon.gif";s:4:"adc5";s:17:"mod_main/conf.php";s:4:"4bea";s:26:"mod_main/locallang_mod.php";s:4:"c902";s:23:"mod_main/moduleicon.gif";s:4:"49ba";s:28:"mod_main/tx_dam_navframe.php";s:4:"6587";s:33:"lib/class.tx_dam_batchprocess.php";s:4:"b7eb";s:32:"lib/class.tx_dam_browsetrees.php";s:4:"197b";s:23:"lib/class.tx_dam_db.php";s:4:"6fdc";s:24:"lib/class.tx_dam_div.php";s:4:"03ce";s:29:"lib/class.tx_dam_filelist.php";s:4:"d48f";s:29:"lib/class.tx_dam_indexing.php";s:4:"edeb";s:34:"lib/class.tx_dam_indexrulebase.php";s:4:"ddb8";s:29:"lib/class.tx_dam_querygen.php";s:4:"749d";s:27:"lib/class.tx_dam_scbase.php";s:4:"f809";s:30:"lib/class.tx_dam_selection.php";s:4:"aecd";s:32:"lib/class.tx_dam_selprocbase.php";s:4:"af87";s:32:"lib/class.tx_dam_simpleforms.php";s:4:"34f2";s:33:"lib/class.tx_dam_stdselection.php";s:4:"e416";s:27:"lib/class.tx_dam_svlist.php";s:4:"2cc1";s:29:"lib/class.tx_dam_tce_file.php";s:4:"2a27";s:28:"lib/class.tx_dam_tcefunc.php";s:4:"f71a";s:26:"lib/class.tx_dam_types.php";s:4:"c7d9";s:17:"lib/locallang.php";s:4:"bfa1";s:48:"modfunc_list_thumbs/class.tx_dam_list_thumbs.php";s:4:"c366";s:33:"modfunc_list_thumbs/locallang.php";s:4:"5a74";s:21:"i/button_deselect.gif";s:4:"7903";s:19:"i/button_remove.gif";s:4:"0414";s:21:"i/button_reselect.gif";s:4:"f06a";s:9:"i/cat.gif";s:4:"b356";s:15:"i/catfolder.gif";s:4:"4e71";s:21:"i/cm_replace_file.gif";s:4:"bdcc";s:12:"i/equals.gif";s:4:"fcb9";s:15:"i/equals_16.gif";s:4:"54a3";s:23:"i/media-application.png";s:4:"b316";s:17:"i/media-audio.png";s:4:"ec87";s:22:"i/media-collection.png";s:4:"3089";s:19:"i/media-dataset.png";s:4:"05eb";s:16:"i/media-font.png";s:4:"5651";s:17:"i/media-image.png";s:4:"5342";s:23:"i/media-interactive.png";s:4:"3b67";s:17:"i/media-model.png";s:4:"b6e0";s:19:"i/media-service.png";s:4:"13cd";s:20:"i/media-software.png";s:4:"3d09";s:16:"i/media-text.png";s:4:"c844";s:21:"i/media-undefined.png";s:4:"bc98";s:18:"i/media2-video.png";s:4:"cc04";s:17:"i/mediafolder.gif";s:4:"f898";s:15:"i/mediatype.gif";s:4:"769b";s:16:"i/mimefolder.gif";s:4:"16bd";s:14:"i/mimetype.gif";s:4:"379f";s:11:"i/minus.gif";s:4:"5f98";s:14:"i/minus_16.gif";s:4:"cfa1";s:10:"i/plus.gif";s:4:"80dd";s:13:"i/plus_16.gif";s:4:"ab3d";s:18:"i/progress_ani.gif";s:4:"ae43";s:18:"i/statusfolder.gif";s:4:"efdc";s:16:"i/statustype.gif";s:4:"062f";s:23:"i/_unused/catfolder.xcf";s:4:"62f7";s:26:"i/_unused/emblem-sound.png";s:4:"d162";s:26:"i/_unused/generic-help.png";s:4:"c084";s:35:"i/_unused/gnome-application-rtf.png";s:4:"493e";s:36:"i/_unused/gnome-application-smil.png";s:4:"03a6";s:28:"i/_unused/gnome-gnumeric.png";s:4:"697d";s:30:"i/_unused/gnome-image-jpeg.png";s:4:"b246";s:31:"i/_unused/gnome-image-x-3ds.png";s:4:"f4ab";s:30:"i/_unused/gnome-video-mpeg.png";s:4:"980a";s:23:"i/_unused/gqview-48.png";s:4:"3217";s:19:"i/_unused/hand2.gif";s:4:"4966";s:19:"i/_unused/image.xcf";s:4:"5768";s:25:"i/_unused/interactive.gif";s:4:"c40b";s:21:"i/_unused/konsole.png";s:4:"b27e";s:20:"i/_unused/locked.gif";s:4:"c212";s:31:"i/_unused/media-application.png";s:4:"854c";s:25:"i/_unused/media-audio.png";s:4:"5312";s:30:"i/_unused/media-collection.png";s:4:"ce12";s:27:"i/_unused/media-dataset.png";s:4:"db2a";s:24:"i/_unused/media-font.png";s:4:"fd6d";s:28:"i/_unused/media-icons-29.xcf";s:4:"0a65";s:25:"i/_unused/media-image.png";s:4:"000b";s:25:"i/_unused/media-image.xcf";s:4:"5aa3";s:31:"i/_unused/media-interactive.png";s:4:"2f85";s:25:"i/_unused/media-model.png";s:4:"6eff";s:27:"i/_unused/media-service.png";s:4:"df4f";s:28:"i/_unused/media-software.png";s:4:"6c5a";s:24:"i/_unused/media-text.png";s:4:"cb7c";s:29:"i/_unused/media-undefined.png";s:4:"e222";s:25:"i/_unused/media-video.png";s:4:"0301";s:26:"i/_unused/media-video2.png";s:4:"0368";s:19:"i/_unused/pages.gif";s:4:"1923";s:23:"i/_unused/player-48.png";s:4:"6bee";s:22:"i/_unused/software.gif";s:4:"5aa1";s:27:"i/_unused/stock_book-16.png";s:4:"c3f6";s:24:"i/_unused/stock_book.png";s:4:"4420";s:23:"i/_unused/undefined.gif";s:4:"bf41";s:19:"i/_unused/video.gif";s:4:"9b60";s:11:"i/18/au.gif";s:4:"042a";s:12:"i/18/doc.gif";s:4:"0975";s:12:"i/18/dot.gif";s:4:"0975";s:19:"i/18/file-icons.xcf";s:4:"d654";s:13:"i/18/html.gif";s:4:"5647";s:12:"i/18/jpg.gif";s:4:"dee4";s:26:"i/18/mtype_application.gif";s:4:"1552";s:20:"i/18/mtype_audio.gif";s:4:"042a";s:25:"i/18/mtype_collection.gif";s:4:"9a2f";s:22:"i/18/mtype_dataset.gif";s:4:"4285";s:19:"i/18/mtype_font.gif";s:4:"7afa";s:20:"i/18/mtype_image.gif";s:4:"1d47";s:26:"i/18/mtype_interactive.gif";s:4:"c40b";s:20:"i/18/mtype_model.gif";s:4:"bf41";s:22:"i/18/mtype_service.gif";s:4:"bf41";s:23:"i/18/mtype_software.gif";s:4:"3e9f";s:19:"i/18/mtype_text.gif";s:4:"6c86";s:24:"i/18/mtype_undefined.gif";s:4:"bf41";s:20:"i/18/mtype_video.gif";s:4:"9b60";s:12:"i/18/pdf.gif";s:4:"49a4";s:13:"i/18/pdf2.gif";s:4:"9451";s:12:"i/18/swf.gif";s:4:"c584";s:12:"i/18/ttf.gif";s:4:"9af9";s:12:"i/18/txt.gif";s:4:"f09a";s:12:"i/18/zip.gif";s:4:"9a2f";s:18:"mod_list/clear.gif";s:4:"cc11";s:17:"mod_list/conf.php";s:4:"3d1d";s:18:"mod_list/index.php";s:4:"1d42";s:22:"mod_list/locallang.php";s:4:"a91a";s:26:"mod_list/locallang_mod.php";s:4:"4536";s:23:"mod_list/moduleicon.gif";s:4:"adc5";s:50:"modfunc_list_editsel/class.tx_dam_list_editsel.php";s:4:"c2a1";s:34:"modfunc_list_editsel/locallang.php";s:4:"d032";s:12:"doc/TODO.txt";s:4:"b2fc";s:17:"doc/changelog.txt";s:4:"b1d2";s:16:"doc/ext_icon.xcf";s:4:"68c0";s:14:"doc/manual.sxw";s:4:"41cb";}',
);

?>