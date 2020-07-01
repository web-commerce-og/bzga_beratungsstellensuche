<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget;

use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
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
use Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\PaginateController;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * @author Sebastian Schreiber
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var PaginateController
     */
    protected $controller;

    public function render(): ResponseInterface
    {
        $objects = $this->arguments['objects'];
        $as = $this->arguments['as'];
        $demand = $this->arguments['demand'];
        $configuration = $this->arguments['configuration'];
        return $this->initiateSubRequest();
    }

    public function injectController(PaginateController $controller): void
    {
        $this->controller = $controller;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('objects', QueryResultInterface::class, '', true);
        $this->registerArgument('as', 'string', '', true);
        $this->registerArgument('demand', Demand::class, '', true);
        $this->registerArgument('configuration', 'array', '', false, null);
    }
}
