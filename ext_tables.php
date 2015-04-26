<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


/***************
 * Let ext:themes the inclusion of the needed static files handle if loaded
 */
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('themes')) {

	/***************
	 * Default TypoScript
	 */
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Bootstrap3 responsive grid');

}


// Add backend css

$TBE_STYLES['skins'][$_EXTKEY]['name'] = $_EXTKEY;
$TBE_STYLES['skins'][$_EXTKEY]['stylesheetDirectories']['structure'] = 'EXT:' . ($_EXTKEY) . '/Resources/Public/Backend/Css/Skin/';
