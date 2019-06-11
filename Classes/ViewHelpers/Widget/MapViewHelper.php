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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\MapController;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * @author Sebastian Schreiber
 */
class MapViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var MapController
     */
    protected $controller;

    public function injectController(MapController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param Demand $demand
     * @param array $styleSheetOptions
     * @param Entry $entry
     * @param array $settings
     *
     * @return ResponseInterface
     */
    public function render(Demand $demand = null, array $styleSheetOptions = ['width' => '100%', 'height' => '300px'], Entry $entry = null, array $settings = []): ResponseInterface
    {
        return $this->initiateSubRequest();
    }
}
