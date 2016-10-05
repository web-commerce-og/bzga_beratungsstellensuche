<?php


namespace BZgA\BzgaBeratungsstellensuche\Property;


interface TypeConverterInterface
{

    /**
     * @var string
     */
    const CONVERT_BEFORE = 'before.converter';

    /**
     * @var string
     */
    const CONVERT_AFTER = 'after.converter';

    /**
     * @param $source
     * @return mixed
     */
    public function supports($source, $type = self::CONVERT_BEFORE);

    /**
     * @param $source
     * @param array $configuration
     */
    public function convert($source, array $configuration = null);

}