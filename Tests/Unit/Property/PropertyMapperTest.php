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
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
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

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(PropertyMapper::class, ['getRegisteredTypeConverters']);
    }

    /**
     * @test
     */
    public function supportsReturnsTypeConverter()
    {
        $typeConverter = $this->setUpTypeConverter();
        $this->assertSame($typeConverter, $this->subject->supports('array'));
    }

    /**
     * @test
     */
    public function convertSuccessfully()
    {
        /** @var TypeConverterBeforeInterface|\PHPUnit_Framework_MockObject_MockObject  $typeConverter */
        $typeConverter = $this->setUpTypeConverter();
        $typeConverter->expects($this->once())->method('convert')->willReturn(true);
        $this->assertTrue($this->subject->convert('array'));
    }

    /**
     * @return TypeConverterBeforeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpTypeConverter()
    {
        /** @var TypeConverterBeforeInterface|\PHPUnit_Framework_MockObject_MockObject  $typeConverter */
        $typeConverter  = $this->getMockBuilder(TypeConverterBeforeInterface::class)->getMock();
        $typeConverter->expects($this->once())->method('supports')->willReturn(true);
        $this->subject->expects($this->once())->method('getRegisteredTypeConverters')->willReturn([get_class($typeConverter)]);


        $this->injectObjectManager($typeConverter);
        return $typeConverter;
    }

    /**
     * @param $typeConverter TypeConverterInterface
     * @return void
     * @internal param array $typeConverters
     */
    private function injectObjectManager($typeConverter)
    {
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->objectManager->expects($this->once())->method('get')->willReturn($typeConverter);
        $this->inject($this->subject, 'objectManager', $this->objectManager);
    }
}
