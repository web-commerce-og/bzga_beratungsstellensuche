<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Property\TypeConverter;

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

use Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\ObjectStorageConverter;
use InvalidArgumentException;
use stdClass;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ObjectStorageConverterTest extends UnitTestCase
{
    /**
     * @var ObjectStorageConverter
     */
    protected $subject;

    /**
     * @return array
     */
    public function unsupportedSources(): array
    {
        return [
            [1],
            ['string'],
            [new stdClass()]
        ];
    }

    protected function setUp()
    {
        $this->subject = new ObjectStorageConverter();
    }

    /**
     * @param mixed $unsupportedSource
     * @test
     * @dataProvider unsupportedSources
     */
    public function isNotSupported($unsupportedSource)
    {
        $this->assertFalse($this->subject->supports($unsupportedSource));
    }

    /**
     * @param mixed $unsupportedSource
     * @test
     * @dataProvider unsupportedSources
     */
    public function convertThrowsException($unsupportedSource)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->subject->convert($unsupportedSource);
    }

    /**
     * @test
     */
    public function convertThrowsExceptionBecauseObjectStorageContainsUnsupportedItem()
    {
        $this->expectException(InvalidArgumentException::class);
        $storage = new ObjectStorage();
        $storage->attach(new stdClass());
        $this->subject->convert($storage);
    }

    /**
     * @test
     */
    public function convertSuccessfully()
    {
        $entity1 = $this->getMockBuilder(DomainObjectInterface::class)->getMock();
        $entity1->method('getUid')->willReturn(1);
        $entity2 = $this->getMockBuilder(DomainObjectInterface::class)->getMock();
        $entity2->method('getUid')->willReturn(2);
        $storage = new ObjectStorage();
        $storage->attach($entity1);
        $storage->attach($entity2);
        $this->assertEquals('1,2', $this->subject->convert($storage));
    }
}
