<?php

namespace BZgA\BzgaBeratungsstellensuche\Factories;

use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\HttpAdapterInterface;

class GeocoderFactory
{

    /**
     * @var string
     */
    const TYPE_GOOGLE = 'google';

    /**
     * @param $type
     * @param HttpAdapterInterface $adapter
     * @return GoogleMaps
     */
    public static function createInstance($type, HttpAdapterInterface $adapter)
    {
        switch ($type) {
            default:
                return new GoogleMaps($adapter);
                break;
        }
    }

}