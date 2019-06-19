<?php


namespace Bzga\BzgaBeratungsstellensuche\Property\TypeConverter;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use InvalidArgumentException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @author Sebastian Schreiber
 */
class ObjectStorageConverter implements TypeConverterBeforeInterface
{
    /**
     * @param mixed $source
     * @param string $type
     * @return bool
     */
    public function supports($source, $type = TypeConverterInterface::CONVERT_BEFORE): bool
    {
        if (!$source instanceof ObjectStorage) {
            return false;
        }

        return true;
    }

    /**
     * @param DomainObjectInterface[]|ObjectStorage $source
     * @param AbstractEntity|array|null $configuration
     * @return string
     */
    public function convert($source, array $configuration = null): string
    {
        if (!$source instanceof ObjectStorage) {
            throw new InvalidArgumentException(sprintf('The %s type is not allowed', gettype($source)));
        }

        $items = array_filter($source->toArray(), static function ($item) {
            return $item instanceof DomainObjectInterface;
        });

        if (count($items) !== $source->count()) {
            throw new InvalidArgumentException('The storage contains values not of type AbstractEntity');
        }

        return implode(',', array_map(static function (DomainObjectInterface $item) {
            return $item->getUid();
        }, $items));
    }
}
