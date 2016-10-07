<?php

namespace BZgA\BzgaBeratungsstellensuche\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FormFields implements SingletonInterface
{

    /**
     * @return array
     */
    public function getAvailableFormFields()
    {
        $templateLayouts = array();

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['formFields'];
        }


        return $templateLayouts;
    }
}