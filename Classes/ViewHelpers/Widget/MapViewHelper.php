<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget;

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;

class MapViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\MapController
     * @inject
     */
    protected $controller;

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @param array $styleSheetOptions
     * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface
     */
    public function render(
        Demand $demand = null,
        array $styleSheetOptions = array('width' => '100%', 'height' => '300px')
    ) {
        return $this->initiateSubRequest();
    }
}