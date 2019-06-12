<?php


namespace Bzga\BzgaBeratungsstellensuche\Hooks;

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
use Exception;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @author Sebastian Schreiber
 */
class SitemapGenerator extends AbstractSitemapGenerator
{
    /**
     * List of storage pages where beratungsstellen items are located
     *
     * @var array
     */
    protected $pidList = [];

    /**
     * Single view page
     *
     * @var int
     */
    protected $singlePid;

    /**
     * Creates an instance of this class
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $singlePid = (int)GeneralUtility::_GP('singlePid');
        $this->singlePid = $singlePid && $this->isInRootline($singlePid) ? $singlePid : $this->getTypoScriptFrontendController()->id;

        $this->validateAndCreatePageList();
    }

    /**
     * Generates site map.
     */
    protected function generateSitemapContent()
    {
        if (count($this->pidList) > 0) {

            $typoScriptFrontendController = $this->getTypoScriptFrontendController();
            $typoScriptFrontendController->sys_language_content = (int)$GLOBALS['TSFE']->config['config']['sys_language_uid'];

            $rows = $this->cObj->getRecords('tx_bzgaberatungsstellensuche_domain_model_entry', [
                'selectFields' => '*',
                'pidInList' => implode(',',$this->pidList),
                'orderBy' => 'title ASC',
                'begin' => $this->offset,
                'max' => $this->limit
            ]);

            foreach ($rows as $row) {
                if ($url = $this->getItemUrl($row)) {
                    echo $this->renderer->renderEntry(
                        $url,
                        $row['title'],
                        $row['tstamp'],
                        '',
                        $row['keywords']
                    );
                }
            }


            if (empty($rows)) {
                echo '<!-- It appears that there are no tx_bzgaberatungsstellensuche_domain_model_entry entries. If your ' .
                     'storage sysfolder is outside of the rootline, you may ' .
                     'want to use the dd_googlesitemap.skipRootlineCheck=1 TS ' .
                     'setup option. Beware: it is insecure and may cause certain ' .
                     'undesired effects! Better move your entries sysfolder ' .
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
    private function getItemUrl($row, $forceSinglePid = null): string
    {
        $skipControllerAndAction = isset($this->getTypoScriptFrontendController()->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.'])
                                   && is_array($this->getTypoScriptFrontendController()->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.'])
                                   && (int)$this->getTypoScriptFrontendController()->tmpl->setup['tx_ddgooglesitemap.']['tx_bzgaberatungsstellen.']['skipControllerAndAction'] === 1;

        $conf = [
            'additionalParams' => (!$skipControllerAndAction ? '&tx_bzgaberatungsstellensuche_pi1[controller]=Entry&tx_bzgaberatungsstellensuche_pi1[action]=show' : '') . '&tx_bzgaberatungsstellensuche_pi1[entry]=' . $row['uid'],
            'forceAbsoluteUrl' => 1,
            'parameter' => $forceSinglePid ?: $this->singlePid,
            'returnLast' => 'url',
            'useCacheHash' => true,
        ];
        return htmlspecialchars($this->cObj->typoLink('', $conf));
    }

    /**
     * Checks that page list is in the rootline of the current page and excludes
     * pages that are outside of the rootline.
     * @throws Exception
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
     *
     * @return bool true if page is in the root line
     * @throws Exception
     */
    private function isInRootline($pid): bool
    {
        if (isset($this->getTypoScriptFrontendController()->config['config']['tx_ddgooglesitemap_skipRootlineCheck'])) {
            $skipRootlineCheck = $this->getTypoScriptFrontendController()->config['config']['tx_ddgooglesitemap_skipRootlineCheck'];
        } else {
            $skipRootlineCheck = $this->getTypoScriptFrontendController()->tmpl->setup['tx_ddgooglesitemap.']['skipRootlineCheck'];
        }
        if ($skipRootlineCheck) {
            $result = true;
        } else {
            $result = false;
            $rootPid = (int)$this->getTypoScriptFrontendController()->tmpl->setup['tx_ddgooglesitemap.']['forceStartPid'];
            if ($rootPid === 0) {
                $rootPid = $this->getTypoScriptFrontendController()->id;
            }
            $rootline = $this->getTypoScriptFrontendController()->sys_page->getRootLine($pid);
            foreach ($rootline as $row) {
                if ($row['uid'] === $rootPid) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return TypoScriptFrontendController
     */
    private function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
