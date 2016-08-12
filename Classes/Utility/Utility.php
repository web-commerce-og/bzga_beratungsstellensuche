<?php


namespace BZgA\BzgaBeratungsstellensuche\Utility;


use Doctrine\Instantiator\Exception\InvalidArgumentException;

class Utility
{

    /**
     * In the XML we reveice, we just get some ids of languages, which we have to map to the field lg_iso_2 of table static_languages
     * @TODO: It would be better if we could get ISO-Codes
     *
     * @var array
     */
    public static $staticLanguagesMapping = array(
        # Deutsch
        1 => 'DE',
        # Englisch
        2 => 'EN',
        # Französisch
        3 => 'FR',
        # Russisch
        4 => 'RU',
        # Türkisch
        5 => 'TR',
        # Arabisch
        6 => 'AR',
        # Spanisch
        7 => 'ES',
        # Polnisch
        8 => 'PL',
    );

    /**
     * In the XML we reveice, we just get some ids of country zones, which we have to map to codes of static_country_zones
     * @TODO: It would be better if we could get ISO-Codes
     *
     * @var array
     */
    public static $staticCountryZonesGermanyMapping = array(
        # Baden-Württemberg
        1 => 'BW',
        # Bayern
        2 => 'BY',
        # Berlin
        3 => 'BE',
        # Brandenburg
        4 => 'BB',
        # Bremen
        5 => 'HB',
        # Hamburg
        6 => 'HH',
        # Hessen
        7 => 'HE',
        # Mecklenburg-Vorpommern
        8 => 'MV',
        # Niedersachsen
        9 => 'NI',
        # Nordrhein-Westfalen
        10 => 'NW',
        # Rheinland-Pfalz
        11 => 'RP',
        # Saarland
        12 => 'SL',
        # Sachsen
        13 => 'SN',
        # Sachsen-Anhalt
        14 => 'ST',
        # Schleswig-Holstein
        15 => 'SH',
        # Thüringen
        16 => 'TH',
    );

    /**
     * @param $id
     *
     * @return int
     * @throw InvalidArgumentException
     */
    public static function mapToStaticCountryZoneTable($id)
    {
        if (empty($id)) {
            return;
        }
        if (isset(self::$staticCountryZonesGermanyMapping[$id])) {
            return self::$staticCountryZonesGermanyMapping[$id];
        }
        throw new \InvalidArgumentException(sprintf('The given country zone id %d is unknown', $id));
    }

    /**
     * @param $id
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public static function mapToStaticLanguagesTable($id)
    {
        if (empty($id)) {
            return;
        }
        if (isset(self::$staticLanguagesMapping[$id])) {
            return self::$staticLanguagesMapping[$id];
        }
        throw new \InvalidArgumentException(sprintf('The given language id %d is unknown', $id));
    }

    /**
     * @param $isoCodeA2
     * @return int
     */
    public static function mapToIndexFromStaticLanguagesTable($isoCodeA2)
    {
        if (false !== $pos = array_search($isoCodeA2, self::$staticLanguagesMapping)) {
            return $pos;
        }
        throw new \InvalidArgumentException(sprintf('There is no value defined for isocode %s', $isoCodeA2));
    }

}