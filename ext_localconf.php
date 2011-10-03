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

	// register special TCE tx_media processing
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:media/Classes/Hooks/TCE.php:&Tx_Media_Hooks_TCE';

$PATH_media = t3lib_extMgm::extPath($_EXTKEY);

t3lib_extMgm::addService($_EXTKEY, 'metaExtract', 'Tx_Media_PdfService', array(
	'title'       => 'PDF meta data extraction',
	'description' => 'Uses Zend PDF to extract meta data',

	'subtype'     => 'application/pdf',

	'available'   => TRUE,
	'priority'    => 70,
	'quality'     => 50,

	'os'          => '',
	'exec'        => '',

	'classFile'   => $PATH_media . 'Classes/Service/PdfService.php',
	'className'   => 'Tx_Media_PdfService',
));


?>
