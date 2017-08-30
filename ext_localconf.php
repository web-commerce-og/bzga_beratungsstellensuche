<?php


if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($packageKey) {
    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerExtensionKey($packageKey, 100);

# Composer autoloader for vendors
    require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($packageKey) . '/Libraries/autoload.php';

# Plugin configuration
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Bzga.bzga_beratungsstellensuche',
        'Pi1',
        ['Entry' => 'list,show,form'],
        ['Entry' => 'list,form']
    );

# Wizard configuration
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/ContentElementWizard.txt">');

// Modify flexform values
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass']['bzga_beratungsstellensuche'] = \Bzga\BzgaBeratungsstellensuche\Hooks\BackendUtility::class;

// Page module hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['bzgaberatungsstellensuche_pi1']['bzga_beratungsstellensuche'] =
        'Bzga\\BzgaBeratungsstellensuche\\Hooks\\PageLayoutView->getExtensionSummary';

# Command controllers for scheduler
    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Bzga\BzgaBeratungsstellensuche\Command\ImportCommandController::class;
        // hooking into TCE Main to monitor record updates that may require deleting documents from the index
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][]  = \Bzga\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Bzga\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor::class;
    }

# Register cache to extend the models of this extension
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]           = [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]['groups'] = ['all'];
    }
    if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class;
    }
    if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]['backend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$packageKey]['backend'] = \TYPO3\CMS\Core\Cache\Backend\FileBackend::class;
    }
    # Configure clear cache post processing for extended domain models
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$packageKey] = 'Bzga\\BzgaBeratungsstellensuche\\Cache\\ClassCacheManager->reBuild';

    # Register cached domain model classes autoloader
    require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($packageKey) . 'Classes/Cache/CachedClassLoader.php';
    \Bzga\BzgaBeratungsstellensuche\Cache\CachedClassLoader::registerAutoloader();

# Names of entities which can be overriden
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$packageKey]['entities'] = [
        'Entry',
        'Category',
        'Dto/Demand',
    ];

# Caching of user requests
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Bzga\BzgaBeratungsstellensuche\Factories\CacheFactory::CACHE_KEY])
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Bzga\BzgaBeratungsstellensuche\Factories\CacheFactory::CACHE_KEY] = [
            'frontend' => '\TYPO3\CMS\Core\Cache\Frontend\VariableFrontend',
            'backend'  => '\TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend',
            'options'  => [],
        ];
    }

    # Register some type converters so we can prepare everything for the data handler to import the xml
    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\ImageLinkConverter::class);
    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\AbstractEntityConverter::class);
    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\StringConverter::class);
    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerTypeConverter(\Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\ObjectStorageConverter::class);

    # Google Sitemap based on dd_googlesitemap
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dd_googlesitemap')) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dd_googlesitemap']['sitemap']['bzga_beratungsstellensuche']
            = 'Bzga\\BzgaBeratungsstellensuche\\Hooks\\SitemapGenerator->main';
    }

    # Auto RealUrl Configuration
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['bzga_beratungsstellensuche'] =
            'Bzga\\BzgaBeratungsstellensuche\\Hooks\\RealUrlAutoConfiguration->addConfig';
    }

    # Linkvalidator
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkvalidator')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/Page/mod.linkvalidator.txt">');
    }

    # Linkhandler
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkhandler')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Configuration/TSconfig/Page/mod.linkhandler.txt">');
    }

}, $_EXTKEY);
