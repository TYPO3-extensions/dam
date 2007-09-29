<?php

/**
 * Predefined TCA entries for usage in own extension.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */




//				'type' => 'group',
//				'internal_type' => 'db',
//
//				'allowed' => 'tx_cctestmm_local',
//				'prepend_tname' => 1,
//				'MM' => 'tx_cctestmm_local_mm',
//				'MM_foreign_select' => 1,
//				'MM_ident' => 'relation_field_or_other_ident',



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'] =
		array (
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',

				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_dam',
				'prepend_tname' => 1,
				'MM' => 'tx_dam_mm_ref',
				'MM_foreign_select' => 1,
				'MM_ident' => 'relation_field_or_other_ident', #### has to be changed in table

				'allowed_types' => '',	// Must be empty for disallowed to work.
				'disallowed_types' => 'php,php3',
				'max_size' => '10000',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '200',
				'minitems' => '0',
				'autoSizeMax' => 30,
		);

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_field'] =
		array (
			'label' => 'LLL:EXT:cms/locallang_ttc.php:media',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'],
		);



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'] =
		array (
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',

				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_dam',
				'prepend_tname' => 1,
				'MM' => 'tx_dam_mm_ref',
				'MM_foreign_select' => 1,
				'MM_ident' => 'relation_field_or_other_ident', #### has to be changed in table

				'allowed_types' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '1000',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '200',
				'minitems' => '0',
				'autoSizeMax' => 30,
		);

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_field'] =
		array (
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'],
		);



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'] =
		array (
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_selectTree',

				'treeView' => 1,
				'treeViewClass' => 'EXT:dam/lib/class.tx_dam_stdselection.php:&tx_dam_stdselectionCategory',
				'foreign_table' => 'tx_dam_cat',
				'size' => 1,
				'autoSizeMax' => 30,
				'minitems' => 0,
				'maxitems' => 2,
		);

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_field'] =
		array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'],
		);



	// mountpoints field in be_groups, be_users
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['mountpoints_config'] =
		array (
				'type' => 'passthrough',
				'form_type' => 'user',
				'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_selectMounts',

				'size' => 20,
				'autoSizeMax' => 30,
				'minitems' => 0,
				'maxitems' => 20,
		);


	// for tx_dam allowed only
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'] = $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'];
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['size'] = 10;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['maxitems'] = 25;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['MM'] = 'tx_dam_mm_cat';

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_field'] =
		array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'],
		);


/**
 * get TCA array for media fields/config
 * 
 * @param string $type 		Name of the predefined TCA definition.
 * @param string $MM_ident 	Ident string for MM relations. Has to be set for field definitions that uses MM relations.
 */
function txdam_getMediaTCA($type, $MM_ident='') {
	$tcaDef = $GLOBALS['T3_VAR']['ext']['dam']['TCA'][$type];

	if($MM_ident AND is_array($tcaDef)) {
		if(is_array($tcaDef['config'])) {
			$tcaDef['config']['MM_ident'] = $MM_ident;
		} else {
			$tcaDef['MM_ident'] = $MM_ident;
		}
	}
	return $tcaDef;
}


?>