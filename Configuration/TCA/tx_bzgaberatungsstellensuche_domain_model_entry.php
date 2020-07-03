<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:bzga_beratungsstellensuche/Resources/Public/Icons/tx_bzgaberatungsstellensuche_domain_model_category.svg',
        'searchFields' => 'title,subtitle,city,zip,teaser,description,contact_person,institution,association,keywords',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, subtitle, categories, image, website, teaser, zip, city, street, state, longitude, latitude, description, contact_person, contact_email, telephone, telefax, email, hotline, notice, keywords, institution, association',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, slug, subtitle, image, teaser, notice, description, keywords, --div--;LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tabs.relations, categories, --div--;LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tabs.address, street, zip, city, state, longitude, latitude, --div--;LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tabs.contact, contact_person, contact_email, telephone, telefax, email, website, hotline, institution, association, --div--;LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_be.xlf:tabs.access,starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 16,
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 16,
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ]
        ],
        'external_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hash' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'title' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'subtitle' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.subtitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'contact_person' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.contact_person',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'contact_email' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.contact_email',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required',
                'softref' => 'email[subst]',
            ]
        ],
        'telephone' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'telefax' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.telefax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.email',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required',
                'softref' => 'email[subst]',
            ]
        ],
        'hotline' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.hotline',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'notice' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.notice',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'keywords' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.keywords',
            'config' => [
                'type' => 'text',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'website' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim,required',
                'softref' => 'typolink_tag,email[subst],url',
            ]
        ],
        'teaser' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'state' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.state',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'static_country_zones',
                'foreign_table_where' => 'AND static_country_zones.pid=0 AND static_country_zones.zn_country_iso_3 = "DEU" ORDER BY static_country_zones.zn_name_local',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'longitude' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.longitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'latitude' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.latitude',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'slug' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title', 'uid'],
                    'fieldSeparator' => '-',
                    'replacements' => [
                        '/' => '-',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
        'description' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
                'enableRichtext' => true,
            ],
        ],
        'image' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                ['maxitems' => 1]
            ),
        ],
        'is_dummy_record' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.is_dummy_record',
            'config' => [
                'type' => 'check',
            ],
        ],
        'categories' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'internal_type' => 'db',
                'allowed' => 'tx_bzgaberatungsstellensuche_domain_model_category',
                'foreign_table' => 'tx_bzgaberatungsstellensuche_domain_model_category',
                'foreign_table_where' => 'ORDER BY tx_bzgaberatungsstellensuche_domain_model_category.title',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 100,
                'MM' => 'tx_bzgaberatungsstellensuche_entry_category_mm',
                'wizards' => [
                    '_PADDING' => 0,
                    '_VERTICAL' => 1,
                ],
            ],
        ],
        'institution' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.institution',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'association' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_db.xlf:tx_bzgaberatungsstellensuche_domain_model_entry.association',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];
