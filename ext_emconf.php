<?php

########################################################################
# Extension Manager/Repository config file for ext "dam".
#
# Auto generated 28-09-2012 11:41
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Media (DAM)',
	'description' => 'The Digital Asset Management (DAM) is simply a tool for organizing digital media assets for storage and retrieval. Metadata can be used to search and organize image, text, audio, video (...) files.',
	'category' => 'module',
	'shy' => 0,
	'version' => '1.3.3',
	'dependencies' => 'cms,static_info_tables',
	'conflicts' => 'dam_file,dam_info,mmforeign',
	'suggests' => '',
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
	'author_email' => 'typo3-project-dam@lists.typo3.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'static_info_tables' => '',
			'php' => '5.2.6-0.0.0',
			'typo3' => '4.5.0-4.7.99',
		),
		'conflicts' => array(
			'dam_file' => '',
			'dam_info' => '',
			'mmforeign' => '',
		),
		'suggests' => array(
		),
	),
);

?>