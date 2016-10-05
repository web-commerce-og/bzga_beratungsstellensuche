<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use BZgA\BzgaBeratungsstellensuche\Factories\GeocoderFactory;
use BZgA\BzgaBeratungsstellensuche\Factories\HttpAdapterFactory;

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
     * @var \BZgA\BzgaBeratungsstellensuche\Service\SettingsService
     */
    protected $settingsService;

    /**
     * @var \Geocoder\Geocoder
     */
    protected $geocoder;

    /**
     * AbstractGeolocationService constructor.
     * @param \BZgA\BzgaBeratungsstellensuche\Service\SettingsService $settingsService
     */
    public function __construct(\BZgA\BzgaBeratungsstellensuche\Service\SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        $adapter = HttpAdapterFactory::createInstance($this->settingsService->getByPath('adapeter'));
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
     * @param float $latitude
     * @param float $longitude
     * @param int $radius
     * @param string $alias
     */
    public function getDistanceSqlField($latitude, $longitude, $radius, $alias = 'distance')
    {
        return sprintf(self::DISTANCE_SQL_FIELD, $latitude, $longitude, $radius).' AS '.$alias;
    }

}