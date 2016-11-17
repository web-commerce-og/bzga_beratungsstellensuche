<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class DistanceViewHelper extends AbstractViewHelper
{

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
     * @inject
     */
    protected $geolocationService;

    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $location
     * @return mixed
     */
    public function render(GeopositionInterface $demandPosition, GeopositionInterface $location)
    {
        return $this->geolocationService->calculateDistance($demandPosition, $location);
    }
}
