<?php


namespace BZgA\BzgaBeratungsstellensuche\Cache;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class CachedClassLoader
{

    /**
     * Extension key
     *
     * @var string
     */
    static protected $extensionKey = 'bzga_beratungsstellensuche';

    /**
     * Cached class loader class name
     *
     * @var string
     */
    static protected $className = __CLASS__;

    /**
     * Namespace of the Domain Model of Beratungsstellensuche
     *
     * @var string
     */
    static protected $namespace = 'BZgA\\BzgaBeratungsstellensuche\\Domain\\Model\\';

    /**
     * The class loader is static, thus we do not allow instances of this class.
     */
    private function __construct()
    {

    }


    /**
     * Registers the cached class loader
     *
     * @return boolean TRUE in case of success
     */
    static public function registerAutoloader()
    {
        return spl_autoload_register(static::$className.'::autoload', true, true);
    }

    /**
     * Autoload function for cached classes
     *
     * @param string $className Class name
     * @return void
     */
    static public function autoload($className)
    {
        $className = ltrim($className, '\\');
        if (strpos($className, static::$namespace) !== false) {
            // Lookup the class in the array of the entities defined in ext_localconf.php and check its presence in the class cache
            $entities = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][static::$extensionKey]['entities'];
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $cacheManager = $objectManager->get(CacheManager::class);
            // ClassCacheManager instantiation creates the class cache if not already available
            $classCacheManager = $objectManager->get(ClassCacheManager::class);
            $classCache = $cacheManager->getCache(static::$extensionKey);
            foreach ($entities as $entity) {
                $entityClassName = static::$namespace.str_replace('/', '\\', $entity);
                if ($className === $entityClassName) {
                    $entryIdentifier = 'DomainModel'.str_replace('/', '', $entity);
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