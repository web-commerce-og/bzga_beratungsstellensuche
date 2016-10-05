<?php


namespace BZga\BzgaBeratungsstellensuche\Property\TypeConverter;

use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use InvalidArgumentException;

class ObjectStorageConverter implements TypeConverterBeforeInterface
{
    /**
     * @param mixed $source
     * @param string $type
     * @return bool
     */
    public function supports($source, $type = TypeConverterInterface::CONVERT_BEFORE)
    {
        if (!$source instanceof ObjectStorage) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     * @param array|null|AbstractEntity $configuration
     * @return string
     */
    public function convert($source, array $configuration = null)
    {
        if (!$source instanceof ObjectStorage) {
            throw new InvalidArgumentException('The type is not allowed');
        }

        $arrayOfUids = array();
        foreach ($source as $item) {
            if (!$item instanceof AbstractEntity) {
                throw new InvalidArgumentException('The type is not allowed');
            }
            $arrayOfUids[] = $item->getUid();
        }

        return implode(',', $arrayOfUids);
    }


}