<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add Gridelements Content Elements to newContentElement Wizard
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/PageTS/pagets.txt">');
