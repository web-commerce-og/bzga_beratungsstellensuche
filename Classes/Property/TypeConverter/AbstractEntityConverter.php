<?php


namespace BZga\BzgaBeratungsstellensuche\Property\TypeConverter;

use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use InvalidArgumentException;

class AbstractEntityConverter implements TypeConverterBeforeInterface
{
    /**
     * @param mixed $source
     * @param string $type
     * @return bool
     */
    public function supports($source, $type = TypeConverterInterface::CONVERT_BEFORE)
    {
        if (!$source instanceof AbstractEntity) {
            return false;
        }


        return true;
    }

    /**
     * @param $source
     * @param array|null|AbstractEntity $configuration
     * @return int
     */
    public function convert($source, array $configuration = null)
    {
        if (!$source instanceof AbstractEntity) {
            throw new InvalidArgumentException('The type is not allowed');
        }

        return $source->getUid();
    }


}