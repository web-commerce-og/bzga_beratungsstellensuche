<?php


namespace Bzga\BzgaBeratungsstellensuche\Property;

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

/**
 * @author Sebastian Schreiber
 */
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
     * @param mixed $source
     * @param string $type
     * @return bool
     */
    public function supports($source, $type = self::CONVERT_BEFORE);

    /**
     * @param mixed $source
     * @param array $configuration
     * @return mixed
     */
    public function convert($source, array $configuration = null);
}
