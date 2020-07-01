<?php

defined('TYPO3_MODE') or die();

$extKey = 'bzga_beratungsstellensuche';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript',
    'Beratungsstellensuche'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript/leaflet',
    'Beratungsstellensuche - Leaflet Resources'
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
        'Configuration/TypoScript/solr',
        'Beratungsstellensuche - Solr'
    );
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('seo')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        $extKey,
        'Configuration/TypoScript/seo',
        'Beratungsstellensuche - Sitemap'
    );
}
unset($extKey);
