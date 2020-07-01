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
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PropertyMapperTest extends UnitTestCase
{

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyMapper
     */
    protected $subject;

    /**
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
        $this->assertTrue($this->subject->supports($typeConverter));
    }

    /**
     * @test
     */
    public function convertSuccessfully()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|TypeConverterBeforeInterface  $typeConverter */
        $typeConverter = $this->setUpTypeConverter();
        $typeConverter->expects($this->once())->method('convert')->willReturn(true);
        $this->assertTrue($this->subject->convert('array'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TypeConverterBeforeInterface
     */
    private function setUpTypeConverter()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|TypeConverterBeforeInterface  $typeConverter */
        $typeConverter  = $this->getMockBuilder(TypeConverterBeforeInterface::class)->getMock();
        $typeConverter->expects($this->once())->method('supports')->willReturn(true);
        $this->subject->expects($this->once())->method('getRegisteredTypeConverters')->willReturn([get_class($typeConverter)]);

        $this->injectObjectManager($typeConverter);
        return $typeConverter;
    }

    /**
     * @param $typeConverter TypeConverterInterface
     * @internal param array $typeConverters
     */
    private function injectObjectManager($typeConverter)
    {
        $this->objectManager = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->objectManager->expects($this->once())->method('get')->willReturn($typeConverter);
        $this->inject($this->subject, 'objectManager', $this->objectManager);
    }
}
