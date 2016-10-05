<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}


return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry',
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
            ).'Resources/Public/Icons/tx_bzgaberatungsstellensuche_domain_model_entry.gif',
        'searchFields' => 'title,subtitle,city,zip,teaser,description,contact_person,institution,association,keywords',
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, subtitle, categories, image, link, website, teaser, zip, city, street, state, longitude, latitude, map, description, religious_denomination, area_of_consulting, contact_person, contact_email, telephone, telefax, email, institution, association, hotline, notice, mother_and_child, mother_and_child_notice, consulting_agreement, keywords, pnd_consultants, pnd_consulting, pnd_languages, pnd_other_language',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, subtitle, categories, image, link, teaser, zip, city, street, state, longitude, latitude, map, description;;;richtext[]:rte_transform[mode=ts], religious_denomination, area_of_consulting, contact_person, contact_email, telephone, telefax, email, website, institution, association, hotline, notice, mother_and_child, mother_and_child_notice, consulting_agreement, pnd_consultants, pnd_consulting, pnd_languages, pnd_other_language, keywords, --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
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
                'foreign_table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                'foreign_table_where' => 'AND tx_bzgaberatungsstellensuche_domain_model_entry.pid=###CURRENT_PID### AND tx_bzgaberatungsstellensuche_domain_model_entry.sys_language_uid IN (-1,0)',
            ),
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough',
            ),
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

        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ),
        ),
        'subtitle' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.subtitle',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'hash' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.hash',
            'config' => array(
                'type' => 'input',
                'size' => 32,
                'eval' => 'trim',
            ),
        ),
        'contact_person' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.contact_person',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'contact_email' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.contact_email',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'telephone' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.telephone',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'telefax' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.telefax',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'email' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.email',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'institution' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.institution',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'association' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.association',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'hotline' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.hotline',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'notice' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.notice',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ),
        ),
        'keywords' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.keywords',
            'config' => array(
                'type' => 'text',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'mother_and_child_notice' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.mother_and_child_notice',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'mother_and_child' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.mother_and_child',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'consulting_agreement' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.consulting_agreement',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'link' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.link',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'website' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.link',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'teaser' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.teaser',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ),
        ),
        'zip' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.zip',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'city' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.city',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'street' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.street',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'state' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.state',
            'displayCond' => 'EXT:static_info_tables:LOADED:true',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'static_country_zones',
                'foreign_table_where' => 'AND static_country_zones.pid=0 AND static_country_zones.zn_country_iso_3 = "DEU" ORDER BY static_country_zones.zn_name_local',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'longitude' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.longitude',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'latitude' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.latitude',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'distance' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.distance',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'map' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.map',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'description' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.description',
            'config' => array(
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'wizards' => array(
                    '_PADDING' => 2,
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        'icon' => 'wizard_rte2.gif',
                        'script' => 'wizard_rte.php',
                    ),
                ),
            ),
        ),
        'religious_denomination' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.religious_denomination',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_bzgaberatungsstellensuche_domain_model_religion',
                'minitems' => 0,
                'maxitems' => 1,
            ),
        ),
        'image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image',
                array('maxitems' => 1)),
        ),
        'is_dummy_record' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.is_dummy_record',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'categories' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.categories',
            "config" => Array(
                "type" => "select",
                "internal_type" => "db",
                "allowed" => "tx_bzgaberatungsstellensuche_domain_model_category",
                "foreign_table" => "tx_bzgaberatungsstellensuche_domain_model_category",
                "foreign_table_where" => "ORDER BY tx_bzgaberatungsstellensuche_domain_model_category.title",
                "size" => 10,
                "minitems" => 0,
                "maxitems" => 100,
                "MM" => "tx_bzgaberatungsstellensuche_domain_model_entry_category_mm",
                "wizards" => Array(
                    "_PADDING" => 0,
                    "_VERTICAL" => 1,
                ),
            ),
        ),
        'pnd_consultants' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.pnd_consultants',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'pnd_consulting' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.pnd_consulting',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'pnd_other_language' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.pnd_other_language',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ),
        ),
        'pnd_languages' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.pnd_languages',
            "config" => Array(
                "type" => "select",
                "internal_type" => "db",
                "allowed" => "static_languages",
                "foreign_table" => "static_languages",
                "size" => 10,
                "minitems" => 0,
                "maxitems" => 100,
                "MM" => "tx_bzgaberatungsstellensuche_domain_model_entry_language_mm",
                "wizards" => Array(
                    "_PADDING" => 0,
                    "_VERTICAL" => 1,
                ),
            ),
        ),
    ),
);