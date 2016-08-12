<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;


use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractNameConverterTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @test
     * @dataProvider dataProvider
     */
    public function normalize($expected, $input)
    {
        $propertyName = $this->subject->normalize($input);
        self::assertSame($expected, $propertyName);
    }

    abstract public function dataProvider();

}