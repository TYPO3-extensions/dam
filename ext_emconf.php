<?php

########################################################################
# Extension Manager/Repository config file for ext: "dam"
#
# Auto generated 21-08-2006 01:09
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
	'version' => '1.1.0-dev',
	'dependencies' => 'cms,static_info_tables',
	'conflicts' => 'dam_file',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod_main,mod_file,mod_list,mod_cmd,mod_edit,mod_info,mod_tools,mod_treebrowser',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'be_groups,be_users,tt_content',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'The DAM development team',
	'author_email' => 'typo3-project-dam@lists.netfielders.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'static_info_tables' => '',
			'php' => '4.0.0-',
			'typo3' => '4.2.4-',
		),
		'conflicts' => array(
			'dam_file' => '',
			'mmforeign' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
	'suggests' => array(
	),
);

?>
