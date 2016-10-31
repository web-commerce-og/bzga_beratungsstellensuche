<?php


if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerExtensionKey($_EXTKEY, 100);

# Composer autoloader for vendors
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'/Libraries/autoload.php';

# Plugin configuration
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'BZgA.bzga_beratungsstellensuche',
    'Pi1',
    array('Entry' => 'list,show,form'),
    array('Entry' => 'list,form')
);

# Wizard configuration
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/ContentElementWizard.txt">');

// Modify flexform values
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass']['bzga_beratungsstellensuche'] = \BZgA\BzgaBeratungsstellensuche\Hooks\BackendUtility::class;

// Page module hook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['bzgaberatungsstellensuche_pi1']['bzga_beratungsstellensuche'] =
    'BZgA\\BzgaBeratungsstellensuche\\Hooks\\PageLayoutView->getExtensionSummary';

# Command controllers for scheduler
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \BZgA\BzgaBeratungsstellensuche\Command\ImportCommandController::class;
    // hooking into TCE Main to monitor record updates that may require deleting documents from the index
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = \BZgA\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \BZgA\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor::class;
}

# Register cache to extend the models of this extension
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
# Configure clear cache post processing for extended domain models
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$_EXTKEY] = 'BZgA\\BzgaBeratungsstellensuche\\Cache\\ClassCacheManager->reBuild';

# Register cached domain model classes autoloader
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Classes/Cache/CachedClassLoader.php');
\BZgA\BzgaBeratungsstellensuche\Cache\CachedClassLoader::registerAutoloader();

# Names of entities which can be overriden
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['entities'] = array(
    'Entry',
    'Category',
    'Dto/Demand',
);

# Caching of user requests
if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][\BZgA\BzgaBeratungsstellensuche\Factories\CacheFactory::CACHE_KEY])
) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][\BZgA\BzgaBeratungsstellensuche\Factories\CacheFactory::CACHE_KEY] = array(
        'frontend' => '\TYPO3\CMS\Core\Cache\Frontend\VariableFrontend',
        'backend' => '\TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend',
        'options' => array(),
    );
}


# Register some type converters so we can prepare everything for the data handler to import the xml
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\ImageLinkConverter::class);
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\ObjectStorageConverter::class);
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\AbstractEntityConverter::class);
\BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\BZga\BzgaBeratungsstellensuche\Property\TypeConverter\StringConverter::class);

# Google Sitemap based on dd_googlesitemap
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dd_googlesitemap')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dd_googlesitemap']['sitemap']['bzga_beratungsstellensuche']
        = 'BZgA\\BzgaBeratungsstellensuche\\Hooks\\SitemapGenerator->main';
}

# Auto RealUrl Configuration
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['bzga_beratungsstellensuche'] =
        'BZgA\\BzgaBeratungsstellensuche\\Hooks\\RealUrlAutoConfiguration->addConfig';
}

# Linkvalidator
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkvalidator')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/Page/mod.linkvalidator.txt">');
}

# Linkhandler
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkhandler')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/Page/mod.linkhandler.txt">');
}