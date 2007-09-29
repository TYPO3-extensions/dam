<?php

/**
 * Predefined TCA entries for usage in own extension.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
 
require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_tcefunc.php');


$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'] =
		Array (
				'type' => 'group',
				'form_type' => 'user',
				'userFunc' => 'tx_dam_tceFunc->getSingleField_typeMedia',
				
				'internal_type' => 'db',
				'allowed' => 'tx_dam',      
				'MM' => 'tx_dam_mm_ref',
#TODO				'prepend_tname' => '_LOCAL',
				
			'allowed_types' => '',	// Must be empty for disallowed to work.
			'disallowed_types' => 'php,php3',
				'max_size' => '10000',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '200',
				'minitems' => '0',
				'autoSizeMax' => 40,
		);
		
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_field'] =
		Array (
			'label' => 'LLL:EXT:cms/locallang_ttc.php:media',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['media_config'],
		);



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'] =
		Array (
				'type' => 'group',
				'form_type' => 'user',
				'userFunc' => 'tx_dam_tceFunc->getSingleField_typeMedia',
				
				'internal_type' => 'db',
				'allowed' => 'tx_dam', 
				'MM' => 'tx_dam_mm_ref',
#TODO				'prepend_tname' => '_LOCAL',
				
			'allowed_types' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '1000',
				'show_thumbs' => '1',
				'size' => '3',
				'maxitems' => '200',
				'minitems' => '0',
				'autoSizeMax' => 40,
		);
		
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_field'] =
		Array (
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_config'],
		);



$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'] =
		Array (
				'type' => 'select',	
				'form_type' => 'user',	
				'userFunc' => 'tx_dam_tceFunc->getSingleField_selectTree',
				
				'treeView' => 1,
				'foreign_table' => 'tx_dam_cat',	
				'size' => 1,
				'autoSizeMax' => 40,
				'minitems' => 0,
				'maxitems' => 2,
		);
		
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_field'] =
		Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'],
		);
		
		
		
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'] = $GLOBALS['T3_VAR']['ext']['dam']['TCA']['category_config'];
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['size'] = 10;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['maxitems'] = 25;
$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config']['MM'] = 'tx_dam_mm_cat';

$GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_field'] =
		Array (
			'label' => 'LLL:EXT:dam/locallang_db.php:tx_dam_item.category',
			'config' => $GLOBALS['T3_VAR']['ext']['dam']['TCA']['categories_mm_config'],
		);
?>