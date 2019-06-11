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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * @author Sebastian Schreiber
 */
class StringConverter implements TypeConverterBeforeInterface
{
    /**
     * @param mixed $source
     * @param string $type
     * @return bool
     */
    public function supports($source, $type = TypeConverterInterface::CONVERT_BEFORE)
    {
        if (!is_string($source)) {
            return false;
        }

        // We just want to convert only values that has HTML-Tags due to performance reason.
        if ($source === strip_tags($source)) {
            return false;
        }

        return true;
    }

    /**
     * @param $source
     * @param AbstractEntity|array|null $configuration
     * @return string
     */
    public function convert($source, array $configuration = null)
    {
        return (string)$source;
    }
}
