<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget;

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

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
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
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry $entry
     * @param array $settings
     * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface
     */
    public function render(
        Demand $demand = null,
        array $styleSheetOptions = array('width' => '100%', 'height' => '300px'),
        Entry $entry = null,
        array $settings = array()
    ) {
        return $this->initiateSubRequest();
    }
}