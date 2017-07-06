<?php


namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractNameConverterTest extends UnitTestCase
{
    /**
     * @var NameConverterInterface
     */
    protected $subject;

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function denormalize($input, $expected)
    {
        $expected = GeneralUtility::underscoredToLowerCamelCase($expected);
        $propertyName = $this->subject->denormalize($input);
        self::assertSame($expected, $propertyName);
    }

    abstract public function dataProvider();
}
