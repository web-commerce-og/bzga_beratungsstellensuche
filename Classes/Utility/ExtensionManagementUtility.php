<?php


namespace BZgA\BzgaBeratungsstellensuche\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility AS CoreExtensionManagementUtility;

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