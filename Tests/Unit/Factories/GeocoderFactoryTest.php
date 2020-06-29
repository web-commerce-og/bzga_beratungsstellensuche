<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Factories;

/*
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

use Bzga\BzgaBeratungsstellensuche\Factories\GeocoderFactory;
use Bzga\BzgaBeratungsstellensuche\Factories\HttpClientFactory;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\Provider;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class GeocoderFactoryTest extends UnitTestCase
{

    /**
     * @test
     */
    public function googleMapsGeocoderReturned()
    {
        $this->assertInstanceOf(
            GoogleMaps::class,
            GeocoderFactory::createInstance(GeocoderFactory::TYPE_GOOGLE, HttpClientFactory::createInstance())
        );
    }

    /**
     * @test
     */
    public function openStreetMapGeocoderReturned()
    {
        $this->assertInstanceOf(
            Nominatim::class,
            GeocoderFactory::createInstance(GeocoderFactory::TYPE_OPEN_STREET_MAP, HttpClientFactory::createInstance())
        );
    }

    /**
     * @test
     */
    public function wrongTypeFallbackToGoogleMaps()
    {
        $this->assertInstanceOf(
            GoogleMaps::class,
            GeocoderFactory::createInstance('something', HttpClientFactory::createInstance())
        );
    }

    /**
     * @test
     */
    public function customProviderReturned()
    {
        $customProvider = $this->getMockBuilder(Provider::class)->getMock();
        $this->assertInstanceOf(get_class($customProvider), GeocoderFactory::createInstance(get_class($customProvider), HttpClientFactory::createInstance()));
    }
}
