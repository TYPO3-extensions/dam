<?php
/*
 * Register necessary class names with autoloader
 *
 */
$extensionPath = t3lib_extMgm::extPath('dam');
return array(
	'tx_dam_mediawizarddamprovider' => $extensionPath . 'binding/mediatag/class.tx_dam_mediawizarddamprovider.php',
);
?>