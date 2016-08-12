<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'/Libraries/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'/Libraries/symfony/serializer/Annotation/Groups.php');

// Register cache static_info_tables
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY] = array();
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['groups'] = array('all');
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['frontend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class;
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['backend'] = \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;
}
// Configure clear cache post processing for extended domain model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$_EXTKEY] = 'BZgA\\BzgaBeratungsstellensuche\\Cache\\ClassCacheManager->reBuild';

// Register cached domain model classes autoloader
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Cache/CachedClassLoader.php');
\BZgA\BzgaBeratungsstellensuche\Cache\CachedClassLoader::registerAutoloader();

// Names of entities
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['entities'] = array(
    'Entry',
    'Category',
    'Religion',
);