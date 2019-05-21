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

use Geocoder\Provider\GoogleMaps;
use Geocoder\Provider\Nominatim;
use Geocoder\Provider\OpenStreetMap;
use Geocoder\Provider\Provider;
use Ivory\HttpAdapter\HttpAdapterInterface;
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
    const TYPE_GOOGLE = 'GoogleMaps';
    const TYPE_OPEN_STREET_MAP = 'OpenStreetMap';

    /**
     * @param string $type
     * @param HttpAdapterInterface $adapter
     * @param string|null $locale
     * @param string|null $region
     * @param bool $useSsl
     * @param string|null $apiKey
     *
     * @return Provider
     */
    public static function createInstance(
        $type,
        HttpAdapterInterface $adapter,
        $locale = null,
        $region = null,
        $useSsl = false,
        $apiKey = null
    ) {
        switch ($type) {
            case self::TYPE_OPEN_STREET_MAP:
                return new OpenStreetMap($adapter, $locale);
                break;
            case self::TYPE_GOOGLE:
                return new GoogleMaps($adapter, $locale, $region, $useSsl, $apiKey);
                break;
            default:

                if(!class_exists($type)) {
                    throw new RuntimeException(sprintf('The %s class does not exist', $type));
                }

                $customProvider = GeneralUtility::makeInstance($type);

                if (!$customProvider instanceof Provider) {
                    throw new RuntimeException(sprintf('The %s must implement the %s interface', $type, Provider::class));
                }

                return $customProvider;
                break;
        }
    }
}
