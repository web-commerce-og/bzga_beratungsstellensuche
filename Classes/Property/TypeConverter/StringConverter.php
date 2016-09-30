<?php


namespace BZga\BzgaBeratungsstellensuche\Property\TypeConverter;

use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use HTMLPurifier_Config;
use HTMLPurifier;

class StringConverter implements TypeConverterInterface
{
    /**
     * @var HTMLPurifier
     */
    private $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
    }


    /**
     * @param $source
     * @return bool
     */
    public function supports($source)
    {
        if (!is_string($source)) {
            return false;
        }

        if ($source === strip_tags($source)) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     */
    public function convert($source)
    {
        return $this->purifier->purify($source);
    }


}