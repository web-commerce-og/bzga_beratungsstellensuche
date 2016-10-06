<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;

interface GeolocationServiceInterface
{

    /**
     * @param Demand $demand
     * @return mixed
     */
    public function findAddressByDemand(Demand $demand);

    /**
     * @param GeoPositionDemandInterface $demandPosition
     * @param string $table
     * @param string $alias
     * @return mixed
     */
    public function getDistanceSqlField(GeopositionDemandInterface $demandPosition, $table, $alias = 'distance');


    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition);


}