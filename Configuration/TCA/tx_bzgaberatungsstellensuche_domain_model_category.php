<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_category',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath(
                'bzga_beratungsstellensuche'
            ).'Resources/Public/Icons/tx_bzgaberatungsstellensuche_domain_model_category.gif',
        'searchFields' => 'title',
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, entries, external_id',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, entries, external_id,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
    ),
    'palettes' => array(
        '1' => array('showitem' => ''),
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0),
                ),
            ),
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_bzgaberatungsstellensuche_domain_model_category',
                'foreign_table_where' => 'AND tx_bzgaberatungsstellensuche_domain_model_category.pid=###CURRENT_PID### AND tx_bzgaberatungsstellensuche_domain_model_category.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            extr
        ),
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ),
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ),
            ),
        ),
        'endtime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ),
            ),
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_category.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ),
        ),
        'external_id' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'hash' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
        ),
        'is_dummy_record' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.is_dummy_record',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'entries' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_category.entries',
            'config' => Array(
                'type' => 'select',
                'internal_type' => 'db',
                'foreign_table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                'foreign_table_where' => 'ORDER BY tx_bzgaberatungsstellensuche_domain_model_entry.title asc',
                'foreign_sortby' => 'tx_bzgaberatungsstellensuche_domain_model_entry.title',
                'size' => 20,
                'autoSizeMax' => 20,
                'minitems' => 0,
                'maxitems' => 500,
                'allowed' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                'prepend_tname' => 1,
                'MM' => 'tx_bzgaberatungsstellensuche_domain_model_entry_category_mm',
                'MM_match_fields' => Array(
                    'tablenames' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                ),
                'wizards' => array(
                    'suggest' => array(
                        'type' => 'suggest',
                    ),
                ),
            ),
        ),
    ),
);