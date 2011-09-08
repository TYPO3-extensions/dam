<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Frontend',
	array(
		'Asset' => 'show, list, new, create, edit, update, delete',
		'Collection' => 'list, show, new, create, edit, update, delete',
		'Filter' => 'list, show, new, create, edit, update, delete',
		'File' => 'show, list, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Asset' => 'create, update, delete',
		'Collection' => 'create, update, delete',
		'Filter' => 'create, update, delete',
		'File' => 'create, update, delete',
		
	)
);

?>