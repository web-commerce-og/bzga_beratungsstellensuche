<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller;

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

use Ivory\GoogleMap\Map;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Ivory\GoogleMap\Overlays\Marker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ivory\GoogleMap\Helper\MapHelper;
use Ivory\GoogleMap\MapTypeId;

class MapController extends AbstractWidgetController
{

    /**
     * @var array
     */
    protected $styleSheetOptions = array(
        'width' => '100%',
        'height' => '300px',
    );

    /**
     * @var Demand
     */
    protected $demand;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @return void
     */
    public function initializeAction()
    {
        $this->demand = $this->widgetConfiguration['demand'];
        ArrayUtility::mergeRecursiveWithOverrule($this->styleSheetOptions,
            $this->widgetConfiguration['styleSheetOptions'], false);

    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $map = new Map();

        $map->setStylesheetOptions($this->styleSheetOptions);
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setAutoZoom(true);
        $map->setLanguage($this->getLanguage());

        $entries = array();
        if ($this->demand instanceof Demand) {
            $entries = $this->entryRepository->findDemanded($this->demand);
        }

        foreach ($entries as $entry) {
            /* @var $entry GeopositionInterface */
            $marker = new Marker();
            $marker->setPosition($entry->getLatitude(), $entry->getLongitude(), true);

            // Call hook functions for modify the marker
            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMarker'])) {
                $params = array(
                    'marker' => &$marker,
                );
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMarker'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }

            $map->addMarker($marker);
        }


        // Call hook functions for modify the map
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMap'])) {
            $params = array(
                'map' => &$map,
            );
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['ViewHelpers/Widget/Controller/MapController.php']['modifyMap'] as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }


        $mapHelper = new MapHelper();

        $this->view->assign('map', $mapHelper->render($map));
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