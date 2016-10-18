<?php

namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller;

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

use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Ivory\GoogleMap\Service\Direction\DirectionService;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Base\Location\AddressLocation;
use Ivory\GoogleMap\Service\Direction\Request\DirectionRequest;


/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class RouteController extends AbstractWidgetController
{

    /**
     * @var Entry
     */
    protected $entry = null;

    /**
     * @return void
     */
    public function initializeAction()
    {
        $this->entry = $this->widgetConfiguration['entry'];
    }

    /**
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * @return void
     */
    public function routeAction()
    {
        $direction = new DirectionService(new Client(), new GuzzleMessageFactory());
        $response = $direction->route(new DirectionRequest(
            new AddressLocation('New York'),
            new AddressLocation('Washington')
        ));
    }

}