<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget;

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;

class PaginateViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\PaginateController
     * @inject
     */
    protected $controller;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
     * @param string $as
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @param array $configuration
     * @return string
     */
    public function render(
        QueryResultInterface $objects,
        $as,
        Demand $demand,
        array $configuration = array(
            'itemsPerPage' => 10,
            'insertAbove' => false,
            'insertBelow' => true,
            'maximumNumberOfLinks' => 99,
        )
    ) {
        return $this->initiateSubRequest();
    }

}