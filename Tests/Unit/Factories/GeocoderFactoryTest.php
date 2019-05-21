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
use Bzga\BzgaBeratungsstellensuche\Factories\HttpAdapterFactory;
use Geocoder\Provider\GoogleMaps;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class GeocoderFactoryTest extends UnitTestCase
{

    /**
     * @test
     */
    public function googleMapsGeocoderReturned()
    {
        $this->assertInstanceOf(
            GoogleMaps::class,
            GeocoderFactory::createInstance(GeocoderFactory::TYPE_GOOGLE, HttpAdapterFactory::createInstance())
        );
    }
}
