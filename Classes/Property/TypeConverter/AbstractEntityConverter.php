<?php


namespace BZga\BzgaBeratungsstellensuche\Property\TypeConverter;

use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use InvalidArgumentException;

class AbstractEntityConverter implements TypeConverterInterface
{
    /**
     * @param $source
     * @return bool
     */
    public function supports($source)
    {
        if (!$source instanceof AbstractEntity) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     * @return int
     */
    public function convert($source)
    {
        if (!$source instanceof AbstractEntity) {
            throw new InvalidArgumentException('The type is not allowed');
        }

        return $source->getUid();
    }


}