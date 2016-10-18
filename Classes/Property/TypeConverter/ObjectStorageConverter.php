<?php


namespace BZga\BzgaBeratungsstellensuche\Property\TypeConverter;

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

use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use InvalidArgumentException;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
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