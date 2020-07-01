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
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class DistanceViewHelper extends AbstractViewHelper
{

    /**
     * @var GeolocationServiceCacheDecorator
     */
    protected $geolocationService;

    public function injectGeolocationService(GeolocationServiceCacheDecorator $geolocationService): void
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $demandPosition = $this->arguments['demandPosition'];
        $location = $this->arguments['location'];
        return $this->geolocationService->calculateDistance($demandPosition, $location);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('demandPosition', GeopositionInterface::class, '', true);
        $this->registerArgument('location', GeopositionInterface::class, '', true);
    }
}
