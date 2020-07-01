<?php


namespace Bzga\BzgaBeratungsstellensuche\Cache;

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
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @author Sebastian Schreiber
 */
class CachedClassLoader
{

    /**
     * @var string
     */
    protected static $extensionKey = 'bzga_beratungsstellensuche';

    /**
     * @var string
     */
    protected static $className = __CLASS__;

    /**
     * @var string
     */
    protected static $namespace = 'Bzga\\BzgaBeratungsstellensuche\\Domain\\Model\\';

    private function __construct()
    {
    }

    public static function registerAutoloader(): bool
    {
        return spl_autoload_register(static::$className . '::autoload', true, true);
    }

    public static function autoload(string $className): void
    {
        $className = ltrim($className, '\\');
        if (strpos($className, static::$namespace) !== false) {
            // Lookup the class in the array of the entities defined in ext_localconf.php and check its presence in the class cache
            $entities = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][static::$extensionKey]['entities'];
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $cacheManager = $objectManager->get(CacheManager::class);
            /* @var $cacheManager CacheManager */
            $classCacheManager = $objectManager->get(ClassCacheManager::class);
            /* @var $classCacheManager ClassCacheManager */
            $classCache = $cacheManager->getCache(static::$extensionKey);
            /* @var $classCache PhpFrontend */
            foreach ($entities as $entity) {
                $entityClassName = static::$namespace . str_replace('/', '\\', $entity);
                if ($className === $entityClassName) {
                    $entryIdentifier = 'DomainModel' . str_replace('/', '', $entity);
                    if (!$classCache->has($entryIdentifier)) {
                        // The class cache needs to be rebuilt
                        $classCacheManager->reBuild();
                    }
                    $classCache->requireOnce($entryIdentifier);
                    break;
                }
            }
        }
    }
}
