<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget;

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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * @author Sebastian Schreiber
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\PaginateController
     * @inject
     */
    protected $controller;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
     * @param string $as
     * @param \Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @param array $configuration
     * @return string
     */
    public function render(
        QueryResultInterface $objects,
        $as,
        Demand $demand,
        array $configuration = [
            'itemsPerPage' => 10,
            'insertAbove' => false,
            'insertBelow' => true,
            'maximumNumberOfLinks' => 99,
        ]
    ) {
        return $this->initiateSubRequest();
    }
}
