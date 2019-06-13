<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Beratungsstellensuche',
    'description' => 'Beratungsstellensuche der BZgA',
    'category' => 'plugin',
    'author' => 'Sebastian Schreiber',
    'author_email' => 'ssch@hauptweg-nebenwege.de',
    'author_company' => 'Hauptweg Nebenwege GmbH',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '8.7.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.13-8.7.99',
            'static_info_tables' => '6.0.0-',
            'static_info_tables_de' => '6.0.0-',
            'typoscript_rendering' => '2.0-2.1.0',
            'scheduler' => '',
        ],
        'conflicts' => [],
    ],
    'autoload' => [
        'psr-4' => ['Bzga\\BzgaBeratungsstellensuche\\' => 'Classes']
    ],
    'autoload-dev' => [
        'psr-4' => ['Bzga\\BzgaBeratungsstellensuche\\Tests\\' => 'Tests']
    ],
];
