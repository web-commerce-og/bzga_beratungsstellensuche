<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;


interface GeoPositionDemandInterface extends GeopositionInterface
{

    /**
     * @return mixed
     */
    public function getKilometers();

}