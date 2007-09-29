<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/browse_links.php']=t3lib_extMgm::extPath($_EXTKEY).'class.tx_dam_elbrowser.php';
$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/class.alt_menu_functions.inc']=t3lib_extMgm::extPath($_EXTKEY).'class.ux_alt_menu_functions.php';

?>
