<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

interface GeopositionInterface
{
    /**
     * Returns latitude of item.
     *
     * @return float
     */
    public function getLatitude();

    /**
     * Returns longitude of item.
     *
     * @return float
     */
    public function getLongitude();
}
