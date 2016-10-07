<?php

namespace BZgA\BzgaBeratungsstellensuche\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TemplateLayout implements SingletonInterface
{

    /**
     * Get available template layouts for a certain page
     *
     * @param int $pageUid
     * @return array
     */
    public function getAvailableTemplateLayouts($pageUid)
    {
        $templateLayouts = array();

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'];
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid) as $templateKey => $title) {
            if (GeneralUtility::isFirstPartOfStr($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }
            $templateLayouts[] = array($title, $templateKey);
        }

        return $templateLayouts;
    }

    /**
     * Get template layouts defined in TsConfig
     *
     * @param $pageUid
     * @return array
     */
    protected function getTemplateLayoutsFromTsConfig($pageUid)
    {
        $templateLayouts = array();
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.']) && is_array($pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.'];
        }

        return $templateLayouts;
    }
}