<?php

namespace Bzga\BzgaBeratungsstellensuche\Domain\Model\ValueObject;

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
class ImageLink
{

    /**
     * @var string
     */
    private $externalUrl;

    /**
     * ImageLink constructor.
     * @param string $externalUrl
     */
    public function __construct($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }
}
