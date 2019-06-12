<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript',
    'Beratungsstellensuche'
);

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkhandler')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/linkhandler',
        'Beratungsstellensuche - Linkhandler'
    );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('solr')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/linkhandler',
        'Beratungsstellensuche - Solr'
    );
}
