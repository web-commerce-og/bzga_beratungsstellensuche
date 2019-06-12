<?php

defined('TYPO3_MODE') or die();

$extKey = 'bzga_beratungsstellensuche';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $extKey,
    'Pi1',
    'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_be.xlf:pi1_title'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['bzgaberatungsstellensuche_pi1'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['bzgaberatungsstellensuche_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'bzgaberatungsstellensuche_pi1',
    'FILE:EXT:' . $extKey . '/Configuration/FlexForms/flexform_beratungsstellensuche.xml'
);
