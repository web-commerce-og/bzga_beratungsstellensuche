<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Property;

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

use Bzga\BzgaBeratungsstellensuche\Property\PropertyMapper;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class PropertyMapperTest extends UnitTestCase
{

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var PropertyMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(PropertyMapper::class, ['getRegisteredTypeConverters']);
        $this->subject->method('getRegisteredTypeConverters')->willReturn([]);
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->inject($this->subject, 'objectManager', $this->objectManager);
    }

    /**
     * @test
     */
    public function supports()
    {
    }
}
