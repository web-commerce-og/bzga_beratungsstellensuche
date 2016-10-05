<?php


namespace BZgA\BzgaBeratungsstellensuche\Factories;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheFactory
{

    /**
     * @var string
     */
    const CACHE_KEY = 'bzgaberatungsstellensuche_cache_coordinates';

    /**
     * @return \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    public static function createInstance()
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        /* @var $cacheManager CacheManager */
        return $cacheManager->getCache(self::CACHE_KEY);
    }

}