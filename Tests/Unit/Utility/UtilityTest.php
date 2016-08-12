<?php

namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Utility;

use BZgA\BzgaBeratungsstellensuche\Utility\Utility;

class UtilityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider mappingIdsToStaticCountryZonesProvider
     * @param $input integer
     * @param $expected string
     */
    public function mapToStaticCountryZoneTable($input, $expected)
    {
        self::assertSame($expected, Utility::mapToStaticCountryZoneTable($input));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function mapToStaticCountryZoneTableThrowsException()
    {
        Utility::mapToStaticCountryZoneTable(PHP_INT_MAX);
    }

    /**
     * @test
     */
    public function mapToStaticCountryZoneTableReturnsNull()
    {
        self::assertNull(Utility::mapToStaticCountryZoneTable(0));
    }

    /**
     * @test
     * @dataProvider mappingIdsToStaticLanguagesProvider
     * @param $input integer
     * @param $expected string
     */
    public function mapToStaticLanguagesTable($input, $expected)
    {
        self::assertSame($expected, Utility::mapToStaticLanguagesTable($input));
    }

    /**
     * @test
     * @dataProvider mappingIdsToStaticLanguagesProvider
     * @param $expected
     * @param $input
     */
    public function mapToIndexFromStaticLanguagesTable($expected, $input)
    {
        self::assertSame($expected, Utility::mapToIndexFromStaticLanguagesTable($input));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function mapToStaticLanguagesTableThrowsException()
    {
        Utility::mapToStaticLanguagesTable(PHP_INT_MAX);
    }

    /**
     * @test
     */
    public function mapToStaticLanguagesTableReturnsNull()
    {
        self::assertNull(Utility::mapToStaticLanguagesTable(0));
    }

    /**
     * @return array
     */
    public function mappingIdsToStaticLanguagesProvider()
    {
        return array(
            # Deutsch
            array(1, 'DE'),
            # Englisch
            array(2, 'EN'),
            # Französisch
            array(3, 'FR'),
            # Russisch
            array(4, 'RU'),
            # Türkisch
            array(5, 'TR'),
            # Arabisch
            array(6, 'AR'),
            # Spanisch
            array(7, 'ES'),
            # Polnisch
            array(8, 'PL'),
        );
    }

    /**
     * @return array
     */
    public function mappingIdsToStaticCountryZonesProvider()
    {
        return array(
            # Baden-Württemberg
            array(1, 'BW'),
            # Bayern
            array(2, 'BY'),
            # Berlin
            array(3, 'BE'),
            # Brandenburg
            array(4, 'BB'),
            # Bremen
            array(5, 'HB'),
            # Hamburg
            array(6, 'HH'),
            # Hessen
            array(7, 'HE'),
            # Mecklenburg-Vorpommern
            array(8, 'MV'),
            # Niedersachsen
            array(9, 'NI'),
            # Nordrhein-Westfalen
            array(10, 'NW'),
            # Rheinland-Pfalz
            array(11, 'RP'),
            # Saarland
            array(12, 'SL'),
            # Sachsen
            array(13, 'SN'),
            # Sachsen-Anhalt
            array(14, 'ST'),
            # Schleswig-Holstein
            array(15, 'SH'),
            # Thüringen
            array(16, 'TH'),
        );
    }
}
