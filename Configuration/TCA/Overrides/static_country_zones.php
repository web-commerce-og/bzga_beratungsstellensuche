<?php
defined('TYPO3_MODE') or die();

$additionalFields = [
    'zn_name_en' => 'external_id'
];

\Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::addAdditionalFieldsToTable($additionalFields, 'static_country_zones');

unset($additionalFields);
