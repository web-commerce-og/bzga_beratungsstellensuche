<?php


namespace BZgA\BzgaBeratungsstellensuche\Hooks;

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

use DmitryDulepov\DdGooglesitemap\Generator\AbstractSitemapGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class SitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * List of storage pages where beratungsstellen items are located
     *
     * @var array
     */
    protected $pidList = array();

    /**
     * Single view page
     *
     * @var int
     */
    protected $singlePid;

    /**
     * Creates an instance of this class
     */
    public function __construct()
    {
        parent::__construct();

        $singlePid = intval(GeneralUtility::_GP('singlePid'));
        $this->singlePid = $singlePid && $this->isInRootline($singlePid) ? $singlePid : $GLOBALS['TSFE']->id;

        $this->validateAndCreatePageList();
    }

    /**
     * Generates site map.
     *
     * @return void
     */
    protected function generateSitemapContent()
    {
        if (count($this->pidList) > 0) {
            $languageCondition = '';

            $language = GeneralUtility::_GP('L');
            if (MathUtility::canBeInterpretedAsInteger($language)) {
                $languageCondition = ' AND sys_language_uid='.$language;
            }

            $res = $this->getDatabaseConnection()->exec_SELECTquery('*',
                'tx_bzgaberatungsstellensuche_domain_model_entry', 'pid IN ('.implode(',', $this->pidList).')'.
                $languageCondition.
                $this->cObj->enableFields('tx_bzgaberatungsstellensuche_domain_model_entry'), '', 'title ASC',
                $this->offset.','.$this->limit
            );
            $rowCount = $this->getDatabaseConnection()->sql_num_rows($res);
            while (false !== ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res))) {
                $forceSinglePid = null;
                if (($url = $this->getItemUrl($row, $forceSinglePid))) {
                    echo $this->renderer->renderEntry($url, $row['title'], $row['tstamp'],
                        '', $row['keywords']);
                }
            }
            $this->getDatabaseConnection()->sql_free_result($res);

            if ($rowCount === 0) {
                echo '<!-- It appears that there are no tx_bzgaberatungsstellensuche_domain_model_entry entries. If your '.
                    'storage sysfolder is outside of the rootline, you may '.
                    'want to use the dd_googlesitemap.skipRootlineCheck=1 TS '.
                    'setup option. Beware: it is insecure and may cause certain '.
                    'undesired effects! Better move your entries sysfolder '.
                    'inside the rootline! -->';
            }
        }
    }

    /**
     * Creates a link to the news item
     *
     * @param array $row News item
     * @param  int $forceSinglePid Single View page for this news item
     * @return string
     */
    private function getItemUrl($row, $forceSinglePid = null)
    {
        $link = '';
        $skipControllerAndAction = isset($GLOBALS['TSFE']->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.'])
            && is_array($GLOBALS['TSFE']->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.'])
            && $GLOBALS['TSFE']->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.']['skipControllerAndAction'] == 1;

        if ($link == '') {
            $conf = array(
                'additionalParams' => (!$skipControllerAndAction ? '&tx_bzgaberatungsstellensuche_pi1[controller]=Entry&tx_bzgaberatungsstellensuche_pi1[action]=show' : '').'&tx_bzgaberatungsstellensuche_pi1[entry]='.$row['uid'],
                'forceAbsoluteUrl' => 1,
                'parameter' => $forceSinglePid ?: $this->singlePid,
                'returnLast' => 'url',
                'useCacheHash' => true,
            );
            $link = htmlspecialchars($this->cObj->typoLink('', $conf));
        }

        return $link;
    }

    /**
     * Checks that page list is in the rootline of the current page and excludes
     * pages that are outside of the rootline.
     *
     * @return    void
     */
    protected function validateAndCreatePageList()
    {
        // Get pages
        $pidList = GeneralUtility::intExplode(',', GeneralUtility::_GP('pidList'));
        // Check pages
        foreach ($pidList as $pid) {
            if ($pid && $this->isInRootline($pid)) {
                $this->pidList[$pid] = $pid;
            }
        }
    }

    /**
     * Check if supplied page id and current page are in the same root line
     *
     * @param int $pid Page id to check
     * @return boolean true if page is in the root line
     */
    private function isInRootline($pid)
    {
        if (isset($GLOBALS['TSFE']->config['config']['tx_ddgooglesitemap_skipRootlineCheck'])) {
            $skipRootlineCheck = $GLOBALS['TSFE']->config['config']['tx_ddgooglesitemap_skipRootlineCheck'];
        } else {
            $skipRootlineCheck = $GLOBALS['TSFE']->tmpl->setup['tx_ddgooglesitemap.']['skipRootlineCheck'];
        }
        if ($skipRootlineCheck) {
            $result = true;
        } else {
            $result = false;
            $rootPid = intval($GLOBALS['TSFE']->tmpl->setup['tx_ddgooglesitemap.']['forceStartPid']);
            if ($rootPid == 0) {
                $rootPid = $GLOBALS['TSFE']->id;
            }
            $rootline = $GLOBALS['TSFE']->sys_page->getRootLine($pid);
            foreach ($rootline as $row) {
                if ($row['uid'] == $rootPid) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }


}