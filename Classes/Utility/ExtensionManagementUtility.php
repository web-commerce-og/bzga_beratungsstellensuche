<?php


namespace BZgA\BzgaBeratungsstellensuche\Utility;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility AS CoreExtensionManagementUtility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class ExtensionManagementUtility
{

    /**
     * @param array $additionalFields
     * @param $table
     */
    public static function addAdditionalFieldsToTable(array $additionalFields, $table)
    {
        foreach ($additionalFields as $sourceField => $destField) {
            $additionalColumns = array();
            $additionalColumns[$destField] = $GLOBALS['TCA'][$table]['columns'][$sourceField];
            $additionalColumns[$destField]['label'] = $destField;
            CoreExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);
            CoreExtensionManagementUtility::addToAllTCAtypes($table, $destField, '', 'after:'.$sourceField);
            // Add as search field
            $GLOBALS['TCA'][$table]['ctrl']['searchFields'] .= ','.$destField;
        }
    }

    /**
     * @param $fields
     */
    public static function addAdditionalFormFields(array $fields)
    {
        foreach ($fields as $field) {
            self::addAdditionalFormField($field);
        }
    }

    /**
     * @param array $field
     */
    public static function addAdditionalFormField(array $field)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'] = array();
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'][] = $field;
    }

    /**
     * @param string $extensionKey
     * @param integer $priority
     */
    public static function registerExtensionKey($extensionKey, $priority)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'] = array();
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'][$priority] = $extensionKey;
    }

    /**
     * @return array
     */
    public static function getRegisteredExtensionKeys()
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys'])) {
            return array();
        }

        return array_reverse($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['extensionKeys']);
    }

    /**
     * Register a type converter by class name.
     *
     * @param string $typeConverterClassName
     * @return void
     * @api
     */
    public static function registerTypeConverter($typeConverterClassName)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'] = array();
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'][] = $typeConverterClassName;
    }

    /**
     * @return array
     */
    public static function getRegisteredTypeConverters()
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'])) {
            return array();
        }

        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['bzga_beratungsstellensuche']['typeConverters'];
    }

}