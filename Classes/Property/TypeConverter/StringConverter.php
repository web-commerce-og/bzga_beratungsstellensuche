<?php

namespace Bzga\BzgaBeratungsstellensuche\Property\TypeConverter;

/*
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

final class StringConverter implements TypeConverterBeforeInterface
{
    /**
     * @var string
     */
    private $allowedTags = '<p><ul><li><em><i><b><br>';

    /**
     * @inheritDoc
     */
    public function supports($source, string $type = self::CONVERT_BEFORE)
    {
        return is_string($source) && $source !== strip_tags($source, $this->allowedTags);
    }

    /**
     * @param mixed $source
     */
    public function convert($source, array $configuration = null): string
    {
        return strip_tags($source, $this->allowedTags);
    }
}
