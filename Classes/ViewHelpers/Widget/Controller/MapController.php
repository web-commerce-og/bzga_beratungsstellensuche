<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MapBuilderFactory;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\MapWindowInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use Bzga\BzgaBeratungsstellensuche\Utility\Utility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @author Sebastian Schreiber
 */
class MapController extends AbstractWidgetController
{

    /**
     * @var array
     */
    protected $styleSheetOptions = [
        'width' => '100%',
        'height' => '300px',
    ];

    /**
     * @var Demand
     */
    protected $demand;

    /**
     * @var Entry
     */
    protected $entry;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var MapBuilderFactory
     *
     */
    protected $mapBuilderFactory;

    public function injectEntryRepository(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectMapBuilderFactory(MapBuilderFactory $mapBuilderFactory)
    {
        $this->mapBuilderFactory = $mapBuilderFactory;
    }

    public function initializeAction()
    {
        $this->settings = $this->widgetConfiguration['settings'];
        $this->entry = $this->widgetConfiguration['entry'];
        $this->demand = $this->widgetConfiguration['demand'];
        ArrayUtility::mergeRecursiveWithOverrule(
            $this->styleSheetOptions,
            $this->widgetConfiguration['styleSheetOptions'],
            false
        );
    }

    public function indexAction()
    {
        $mapBuilder = $this->mapBuilderFactory->createMapBuilder();

        $mapId = sprintf('map_%s', StringUtility::getUniqueId());

        $this->view->assign('mapId', $mapId);

        // These are only some defaults and can be overridden via a hook method
        $map = $mapBuilder->createMap($mapId);

        // Set map options configurable via TypoScript, option:value => maxZoom:17
        $mapOptions = isset($this->settings['map']['options']) ? GeneralUtility::trimExplode(',', $this->settings['map']['options']) : [];

        if (is_array($mapOptions) && ! empty($mapOptions)) {
            foreach ($mapOptions as $mapOption) {
                list($mapOptionKey, $mapOptionValue) = GeneralUtility::trimExplode(':', $mapOption, true, 2);
                $map->setOption($mapOptionKey, $mapOptionValue);
            }
        }

        $entries = new ObjectStorage();
        if ($this->demand instanceof Demand) {
            try {
                $queryResult = $this->entryRepository->findDemanded($this->demand);
                $entries = Utility::transformQueryResultToObjectStorage($queryResult);
            } catch (InvalidQueryException $e) {
            }
        }

        if ($this->entry instanceof Entry) {
            $entries->attach($this->entry);
        }

        foreach ($entries as $entry) {
            /* @var $entry GeopositionInterface|MapWindowInterface */
            $coordinate = $mapBuilder->createCoordinate($entry->getLatitude(), $entry->getLongitude());
            $marker = $mapBuilder->createMarker(sprintf('marker_%d', $entry->getUid()), $coordinate);

            $iconFile = $this->settings['map']['pathToDefaultMarker'] ?? '';
            $isCurrentMarker = false;
            if ($this->entry === $entry) {
                $isCurrentMarker = true;
                $iconFile = $this->settings['map']['pathToActiveMarker'] ?? '';
                $map->setCenter($coordinate);
            }

            if (! empty($iconFile)) {
                $marker->addIconFromPath(Utility::stripPathSite(GeneralUtility::getFileAbsFileName($iconFile)));
            }

            $infoWindowParameters = [];

            // Current marker does not need detail link
            if (false === $isCurrentMarker) {
                $detailsPid = $this->settings['singlePid'] ?? $this->getTyposcriptFrontendController()->id;
                $uriBuilder = $this->controllerContext->getUriBuilder();
                $infoWindowParameters['detailLink'] = $uriBuilder->reset()->setTargetPageUid($detailsPid)->uriFor(
                    'show',
                    ['entry' => $entry],
                    'Entry'
                );
            }

            // Create Info Window
            $popUp = $mapBuilder->createPopUp('popUp');

            // Call hook functions for modify the info window
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyInfoWindow'])) {
                $params = [
                    'popUp' => &$popUp,
                    'isCurrentMarker' => $isCurrentMarker,
                    'demand' => $this->demand,
                ];
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyInfoWindow'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }

            $marker->addPopUp($popUp, $entry->getInfoWindow($infoWindowParameters), $isCurrentMarker);

            // Call hook functions for modify the marker
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMarker'])) {
                $params = [
                    'marker' => &$marker,
                    'isCurrentMarker' => $isCurrentMarker,
                ];
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMarker'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }

            $map->addMarker($marker);
        }

        // Call hook functions for modify the map
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMap'])) {
            $params = [
                'map' => &$map,
            ];
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMap'] as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }

        $this->view->assign('map', $mapBuilder->build($map));
    }

    /**
     * @return TypoScriptFrontendController
     */
    private function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
