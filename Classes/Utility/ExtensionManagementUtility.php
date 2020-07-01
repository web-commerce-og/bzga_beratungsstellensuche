<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility as CoreExtensionManagementUtility;

/**
 * @author Sebastian Schreiber
 * @codeCoverageIgnore
 */
class ExtensionManagementUtility
{
    public static function addAdditionalFieldsToTable(array $additionalFields, string $table): void
    {
        foreach ($additionalFields as $sourceField => $destField) {
            $additionalColumns = [];
            $additionalColumns[$destField] = $GLOBALS['TCA'][$table]['columns'][$sourceField];
            $additionalColumns[$destField]['label'] = $destField;
            CoreExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);
            CoreExtensionManagementUtility::addToAllTCAtypes($table, $destField, '', 'after:' . $sourceField);
            // Add as search field
            $GLOBALS['TCA'][$table]['ctrl']['searchFields'] .= ',' . $destField;
        }
    }

    public static function addAdditionalFormFields(array $fields): void
    {
        foreach ($fields as $field) {
            self::addAdditionalFormField($field);
        }
    }

    public static function addAdditionalFormField(array $field): void
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'][] = $field;
    }

    public static function registerExtensionKey(string $extensionKey, int $priority): void
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'][$priority] = $extensionKey;
    }

    public static function getRegisteredExtensionKeys(): array
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'])) {
            return [];
        }

        return array_reverse($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys']);
    }

    public static function registerTypeConverter(string $typeConverterClassName): void
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'][] = $typeConverterClassName;
    }

    public static function getRegisteredTypeConverters(): array
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'])) {
            return [];
        }

        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'];
    }
}
