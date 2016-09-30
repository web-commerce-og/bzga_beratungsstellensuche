<?php
defined('TYPO3_MODE') or die();

$additionalFields = array(
	'zn_name_en' => 'external_id'
);

\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::addAdditionalFieldsToTable($additionalFields, 'static_country_zones');

unset($additionalFields);