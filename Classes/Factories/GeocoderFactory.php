<?php

namespace Bzga\BzgaBeratungsstellensuche\Factories;

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
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use RuntimeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
class GeocoderFactory
{

    /**
     * @var string
     */
    public const TYPE_GOOGLE = 'GoogleMaps';

    /**
     * @var string
     */
    public const TYPE_OPEN_STREET_MAP = 'OpenStreetMap';

    public static function createInstance(
        string $type,
        HttpClient $client,
        ?string $region = null,
        ?string $apiKey = null
    ): Provider {
        if ($type === self::TYPE_OPEN_STREET_MAP) {
            return Nominatim::withOpenStreetMapServer($client, 'User-Agent');
        }

        if (! class_exists($type)) {
            return new GoogleMaps($client, $region, $apiKey);
        }

        $customProvider = GeneralUtility::makeInstance($type);

        if (! $customProvider instanceof Provider) {
            throw new RuntimeException(sprintf('The %s must implement the %s interface', $type, Provider::class));
        }

        return $customProvider;
    }
}
