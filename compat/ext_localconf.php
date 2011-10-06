<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (t3lib_extMgm::isLoaded('rtehtmlarea')) {
		// Hooks for images and links
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/rtehtmlarea/mod4/class.tx_rtehtmlarea_select_image.php']['browseLinksHook'][] =  PATH_txdam.'compat/class.tx_dam_rtehtmlarea_browse_media.php:&tx_dam_rtehtmlarea_browse_media';
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook'][] =  PATH_txdam.'compat/class.tx_dam_rtehtmlarea_browse_links.php:&tx_dam_rtehtmlarea_browse_links';
		// Configure additional attributes on links
		// htmlArea RTE MUST be installed before DAM for this to work...
	if ($TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes']) {
		$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'] .= ',txdam,usedamcolumn';
	} else {
		$TYPO3_CONF_VARS['EXTCONF']['rtehtmlarea']['plugins']['TYPO3Link']['additionalAttributes'] = 'txdam,usedamcolumn';
	}
}

?>