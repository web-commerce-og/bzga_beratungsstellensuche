<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;


trait GeopositionTrait
{
    /**
     * Längengrad.
     *
     * @var float
     */
    protected $longitude = null;

    /**
     * Breitengrad.
     *
     * @var float
     */
    protected $latitude = null;

    /**
     * Returns the longitude.
     * @return float $longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude.
     *
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Returns the latitude.
     * @return float $latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Sets the latitude.
     *
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }
}
