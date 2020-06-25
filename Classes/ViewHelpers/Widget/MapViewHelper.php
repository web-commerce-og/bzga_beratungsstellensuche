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
     * @return ResponseInterface
     */
    public function render(): ResponseInterface
    {
        $demand = $this->arguments['demand'];
        $styleSheetOptions = $this->arguments['styleSheetOptions'];
        $entry = $this->arguments['entry'];
        $settings = $this->arguments['settings'];
        return $this->initiateSubRequest();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('demand', Demand::class, '', false, null);
        $this->registerArgument('styleSheetOptions', 'array', '', false, null);
        $this->registerArgument('entry', Entry::class, '', false, null);
        $this->registerArgument('settings', 'array', '', false, null);
    }
}
