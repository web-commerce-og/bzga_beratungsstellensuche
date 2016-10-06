<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;


use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Tests\UnitTestCase;

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