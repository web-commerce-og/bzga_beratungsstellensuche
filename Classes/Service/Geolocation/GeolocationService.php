<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;

class GeolocationService extends AbstractGeolocationService
{

    /**
     * @param Demand $demand
     */
    public function findAddressByDemand(Demand $demand)
    {
        if ($demand->getLocation()) {
            $addressCollection = $this->geocoder->geocode($demand->getLocation());
            $address = $addressCollection->first();

            return $address;
        }
    }


}