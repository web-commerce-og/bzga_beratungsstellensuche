<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Geolocation;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use Bzga\BzgaBeratungsstellensuche\Factories\GeocoderFactory;
use Bzga\BzgaBeratungsstellensuche\Factories\HttpAdapterFactory;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractGeolocationService implements GeolocationServiceInterface
{

    /**
     * @var string
     */
    const DISTANCE_SQL_FIELD = '(6371.01 * acos(cos(radians(%1$f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%2$f)) + sin(radians(%1$f) ) * sin(radians(latitude))))';

    /**
     * @var float
     */
    const EARTH_RADIUS = 6371.01;

    /**
     * @var int
     */
    const DEFAULT_RADIUS = 10;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\SettingsService
     */
    protected $settingsService;

    /**
     * @var \Geocoder\Geocoder
     */
    protected $geocoder;

    /**
     * AbstractGeolocationService constructor.
     * @param \Bzga\BzgaBeratungsstellensuche\Service\SettingsService $settingsService
     */
    public function __construct(\Bzga\BzgaBeratungsstellensuche\Service\SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        $adapter = HttpAdapterFactory::createInstance($this->settingsService->getByPath('adapter'));
        $this->geocoder = GeocoderFactory::createInstance($this->settingsService->getByPath('geocoder'), $adapter);
    }

    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     * @return float
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition)
    {
        return self::EARTH_RADIUS * acos(
                cos(deg2rad($demandPosition->getLatitude())) * cos(deg2rad($locationPosition->getLatitude())) * cos(
                    deg2rad($locationPosition->getLongitude()) - deg2rad($demandPosition->getLongitude())
                ) + sin(deg2rad($demandPosition->getLatitude())) * sin(deg2rad($locationPosition->getLatitude()))
            );
    }

    /**
     * @param GeoPositionDemandInterface $demandPosition
     * @param $table
     * @param string $alias
     * @return mixed
     */
    public function getDistanceSqlField(GeopositionDemandInterface $demandPosition, $table, $alias = 'distance')
    {
        return sprintf(self::DISTANCE_SQL_FIELD, $demandPosition->getLatitude(), $demandPosition->getLongitude(),
                $demandPosition->getKilometers()) . ' AS ' . $alias;
    }
}
