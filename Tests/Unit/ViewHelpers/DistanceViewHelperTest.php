<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\ViewHelpers;

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

use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator;
use Bzga\BzgaBeratungsstellensuche\ViewHelpers\DistanceViewHelper;
use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;

class DistanceViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var GeolocationServiceCacheDecorator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $geolocationService;

    /**
     * @var DistanceViewHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->geolocationService = $this->getMockBuilder(GeolocationServiceCacheDecorator::class)->disableOriginalConstructor()->getMock();
        $this->subject = $this->getMockBuilder(DistanceViewHelper::class)->setMethods(['renderChildren'])->getMock();
        $this->subject->injectGeolocationService($this->geolocationService);
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     */
    public function render()
    {
        $this->geolocationService->expects($this->once())->method('calculateDistance')->willReturn(1);
        $demandPosition = $this->getMockBuilder(GeopositionInterface::class)->getMock();
        $location = $this->getMockBuilder(GeopositionInterface::class)->getMock();
        $this->subject->setArguments([
            'demandPosition' => $demandPosition,
            'location' => $location
        ]);
        $this->assertEquals(1, $this->subject->render());
    }
}
