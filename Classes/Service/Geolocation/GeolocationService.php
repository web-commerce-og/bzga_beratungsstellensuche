<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Service\Geolocation;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Geocoder\Exception\CollectionIsEmpty;
use Geocoder\Location;
use Geocoder\Query\GeocodeQuery;

/**
 * @author Sebastian Schreiber
 */
class GeolocationService extends AbstractGeolocationService
{
    public function findAddressByDemand(Demand $demand): ?Location
    {
        if ($demand->getLocation()) {
            try {
                return $this->geocoder->geocodeQuery(GeocodeQuery::create($demand->getAddressToGeocode()))->first();
            } catch (CollectionIsEmpty $e) {
                return null;
            }
        }

        return null;
    }
}
