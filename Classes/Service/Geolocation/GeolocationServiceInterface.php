<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;

interface GeolocationServiceInterface
{

    /**
     * @param Demand $demand
     * @return mixed
     */
    public function findAddressByDemand(Demand $demand);

    /**
     * @param float $latitude
     * @param float $longitude
     * @param integer $radius
     * @param string $alias
     * @return mixed
     */
    public function getDistanceSqlField($latitude, $longitude, $radius, $alias = 'distance');


    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition);


}