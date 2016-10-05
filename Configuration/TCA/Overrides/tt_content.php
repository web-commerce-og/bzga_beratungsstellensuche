<?php

defined('TYPO3_MODE') or die();

$extKey = 'bzga_beratungsstellensuche';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin($extKey, 'Pi1',
    'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_be.xlf:pi1_title');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', 'Beratungsstellensuche');

