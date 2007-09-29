<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (!defined ('PATH_txdam')) {
	define('PATH_txdam', t3lib_extMgm::extPath('dam'));
}

if (!defined ('PATH_txdam_rel')) {
	define('PATH_txdam_rel', t3lib_extMgm::extRelPath('dam'));
}


$TYPO3_CONF_VARS['EXTCONF']['dam']['setup'] = unserialize($_EXTCONF);


require_once(PATH_txdam.'lib/class.tx_dam.php');

	// field templates for usage in other tables to link media records
require_once(PATH_txdam.'tca_media_field.inc');


$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/browse_links.php']=PATH_txdam.'class.ux_SC_browse_links.php';
$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/show_item.php']=PATH_txdam.'class.ux_SC_show_item.php';

if(t3lib_div::int_from_ver(TYPO3_version) < t3lib_div::int_from_ver('3.8.1')) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/class.alt_menu_functions.inc']=PATH_txdam.'compat/class.ux_alt_menu_functions.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/template.php']=PATH_txdam.'compat/class.ux_template.php';
}

if ($TYPO3_CONF_VARS['EXTCONF']['dam']['setup']['mmref']) {
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tcemain.php']=PATH_txdam.'compat/class.ux_t3lib_tcemain.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_transferdata.php']=PATH_txdam.'compat/class.ux_t3lib_transferdata.php';
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_loaddbgroup.php']=PATH_txdam.'compat/class.ux_t3lib_loaddbgroup.php';
}


?>