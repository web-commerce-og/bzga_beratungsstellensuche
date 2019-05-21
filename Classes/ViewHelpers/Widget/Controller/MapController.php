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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\MapWindowInterface;
use Bzga\BzgaBeratungsstellensuche\Utility\Utility;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Control\ControlPosition;
use Ivory\GoogleMap\Control\FullscreenControl;
use Ivory\GoogleMap\Event\MouseEvent;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Overlay\InfoWindowType;
use Ivory\GoogleMap\Overlay\Marker;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    protected $demand = null;

    /**
     * @var Entry
     */
    protected $entry = null;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
     * @inject
     */
    protected $geoLocationService;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     */
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

    /**
     */
    public function indexAction()
    {
        // These are only some defaults and can be overriden via a hook method
        $map = new Map();
        $fullscreenControl = new FullscreenControl(ControlPosition::TOP_RIGHT);
        $map->getControlManager()->setFullscreenControl($fullscreenControl);
        $map->setStylesheetOptions($this->styleSheetOptions);
        $map->setAutoZoom(true);

        // Set map options configurable via TypoScript, option:value => maxZoom:17
        $mapOptions = isset($this->settings['map']['options']) ? GeneralUtility::trimExplode(',', $this->settings['map']['options']) : [];

        if (is_array($mapOptions) && !empty($mapOptions)) {
            foreach ($mapOptions as $mapOption) {
                list($mapOptionKey, $mapOptionValue) = GeneralUtility::trimExplode(':', $mapOption, true, 2);
                $map->setMapOption($mapOptionKey, $mapOptionValue);
            }
        }

        $entries = new ObjectStorage();
        if ($this->demand instanceof Demand) {
            $queryResult = $this->entryRepository->findDemanded($this->demand);
            $entries = Utility::transformQueryResultToObjectStorage($queryResult);
        }
        if ($this->entry instanceof Entry) {
            $entries->attach($this->entry);
        }

        foreach ($entries as $entry) {
            /* @var $entry GeopositionInterface|MapWindowInterface */
            $coordinate = new Coordinate($entry->getLatitude(), $entry->getLongitude());
            $marker = new Marker($coordinate);

            $iconFile = isset($this->settings['map']['pathToDefaultMarker']) ? $this->settings['map']['pathToDefaultMarker'] : '';
            $isCurrentMarker = false;
            if ($this->entry === $entry) {
                $isCurrentMarker = true;
                $iconFile = isset($this->settings['map']['pathToActiveMarker']) ? $this->settings['map']['pathToActiveMarker'] : '';
            }

            if (!empty($iconFile)) {
                $iconFileAbsPath = Utility::stripPathSite(GeneralUtility::getFileAbsFileName($iconFile));
                $icon = new Icon($iconFileAbsPath);
                $marker->setIcon($icon);
            }

            $marker->setOptions(
                [
                    'clickable' => true,
                    'flat' => true,
                ]
            );

            $infoWindowParameters = [];

            // Current marker does not need detail link
            if (false === $isCurrentMarker) {
                $detailsPid = isset($this->settings['singlePid']) ? $this->settings['singlePid'] : $GLOBALS['TSFE']->id;
                $uriBuilder = $this->controllerContext->getUriBuilder();
                $infoWindowParameters['detailLink'] = $uriBuilder->reset()->setTargetPageUid($detailsPid)->uriFor(
                    'show',
                    ['entry' => $entry],
                    'Entry',
                    null,
                    null
                );
            }
            // Create Info Window
            $infoWindow = new InfoWindow(
                $entry->getInfoWindow($infoWindowParameters),
                InfoWindowType::DEFAULT_,
                $coordinate
            );
            $infoWindow->setOpenEvent(MouseEvent::MOUSEDOWN);
            $infoWindow->setAutoClose(true);
            $infoWindow->setOptions(
                [
                    'disableAutoPan' => false,
                    'zIndex' => 10,
                    'maxWidth' => 300,
                ]
            );

            // Call hook functions for modify the info window
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyInfoWindow'])) {
                $params = [
                    'infoWindow' => &$infoWindow,
                    'isCurrentMarker' => $isCurrentMarker,
                    'demand' => $this->demand,
                ];
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyInfoWindow'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }

            $marker->setInfoWindow($infoWindow);

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

            $map->getOverlayManager()->addMarker($marker);
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

        $mapHelperBuilder = MapHelperBuilder::create();
        $mapHelper = $mapHelperBuilder->build();

        $apiHelperBuilder = ApiHelperBuilder::create();
        $apiHelperBuilder->setLanguage($this->getLanguage());
        $googleMapsApiKey = isset($this->settings['map']['apiKey']) ? (string)$this->settings['map']['apiKey'] : null;
        if (is_string($googleMapsApiKey)) {
            $apiHelperBuilder->setKey($googleMapsApiKey);
        }
        $apiHelper = $apiHelperBuilder->build();

        $this->view->assign('map', $mapHelper->render($map));
        $this->view->assign('api', $apiHelper->render([$map]));
    }

    /**
     * @return string
     * @throws \UnexpectedValueException
     */
    private function getLanguage()
    {
        if ('FE' !== TYPO3_MODE) {
            throw new \UnexpectedValueException('The context must be in the FE');
        }
        $language = $this->getTyposcriptFrontendController()->config['config']['language'] ?: 'de';

        return $language;
    }

    /**
     * @return TypoScriptFrontendController
     */
    private function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
