<?php


namespace Bzga\BzgaBeratungsstellensuche\Factories;

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
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @author Sebastian Schreiber
 */
class CacheFactory
{

    /**
     * @var string
     */
    public const CACHE_KEY = 'bzgaberatungsstellensuche_cache_coordinates';

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    public function createInstance(): FrontendInterface
    {
        return $this->cacheManager->getCache(self::CACHE_KEY);
    }
}
