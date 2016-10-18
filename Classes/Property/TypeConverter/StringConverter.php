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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use HTMLPurifier_Config;
use HTMLPurifier;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class StringConverter implements TypeConverterBeforeInterface
{
    /**
     * @var HTMLPurifier
     */
    private $purifier;

    public function __construct()
    {
        # TODO: This is currently not really testable. We have to make DI concept here.
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
    }


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

        if ($source === strip_tags($source)) {
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
        return $this->purifier->purify($source);
    }


}