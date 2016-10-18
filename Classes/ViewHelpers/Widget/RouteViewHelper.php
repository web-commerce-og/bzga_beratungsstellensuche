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
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class RouteViewHelper extends AbstractWidgetViewHelper
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller\RouteController
     * @inject
     */
    protected $controller;

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry $entry
     * @return \TYPO3\CMS\Extbase\Mvc\ResponseInterface
     */
    public function render(Entry $entry)
    {
        return $this->initiateSubRequest();
    }


}