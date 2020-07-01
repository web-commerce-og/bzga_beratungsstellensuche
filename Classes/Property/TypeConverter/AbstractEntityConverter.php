<?php declare(strict_types = 1);

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

/**
 * @author Sebastian Schreiber
 */
class AbstractEntityConverter implements TypeConverterBeforeInterface
{
    /**
     * @inheritDoc
     */
    public function supports($source, string $type = TypeConverterInterface::CONVERT_BEFORE)
    {
        if (!$source instanceof AbstractEntity) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $source
     */
    public function convert($source, array $configuration = null): int
    {
        if (!$source instanceof AbstractEntity) {
            throw new InvalidArgumentException('The type is not allowed');
        }

        return $source->getUid();
    }
}
