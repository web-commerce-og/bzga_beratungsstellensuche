<?php


namespace BZgA\BzgaBeratungsstellensuche\Property;


interface TypeConverterInterface
{

    /**
     * @param $source
     * @return mixed
     */
    public function supports($source);

    /**
     * @param $source
     * @return mixed
     */
    public function convert($source);

}