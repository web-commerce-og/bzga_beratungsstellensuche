<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'/Libraries/autoload.php';

# Command controllers for scheduler
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \BZgA\BzgaBeratungsstellensuche\Command\ImportCommandController::class;
    // hooking into TCE Main to monitor record updates that may require deleting documents from the index
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \BZgA\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor::class;


}

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
# Configure clear cache post processing for extended domain model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$_EXTKEY] = 'BZgA\\BzgaBeratungsstellensuche\\Cache\\ClassCacheManager->reBuild';

# Register cached domain model classes autoloader
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Cache/CachedClassLoader.php');
\BZgA\BzgaBeratungsstellensuche\Cache\CachedClassLoader::registerAutoloader();

# Names of entities
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['entities'] = array(
    'Entry',
    'Category',
    'Religion',
);


# Register some type converters so we can prepare everthing for the data handler
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\ObjectStorageConverter::class);
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\AbstractEntityConverter::class);
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\StringConverter::class);