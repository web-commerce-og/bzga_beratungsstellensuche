<?php

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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
class TemplateLayout implements SingletonInterface
{
    public function getAvailableTemplateLayouts(int $pageUid): array
    {
        $templateLayouts = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts'];
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid) as $templateKey => $title) {
            if (GeneralUtility::isFirstPartOfStr($title, '--div--')) {
                list($templateKey, $title) = GeneralUtility::trimExplode(',', $title, true, 2);
            }
            $templateLayouts[] = [$title, $templateKey];
        }

        return $templateLayouts;
    }

    private function getTemplateLayoutsFromTsConfig(int $pageUid): array
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.']) && is_array($pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig['tx_bzgaberatungsstellensuche.']['templateLayouts.'];
        }

        return $templateLayouts;
    }
}
