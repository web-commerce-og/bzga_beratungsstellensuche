<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"exportPublic"})
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
     * @Groups({"exportPublic"})
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
