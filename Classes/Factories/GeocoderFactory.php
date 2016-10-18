<?php

namespace BZgA\BzgaBeratungsstellensuche\Factories;

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

use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\HttpAdapterInterface;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
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
        // @TODO: Implement other types for flexibility
        switch ($type) {
            default:
                return new GoogleMaps($adapter);
                break;
        }
    }

}