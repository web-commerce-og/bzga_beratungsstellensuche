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
     * @var string
     */
    private $identifier = '';

    public function __construct(string $externalUrl)
    {
        $this->externalUrl = $externalUrl;
        $this->setIdentifier($externalUrl);
    }

    public function getExternalUrl(): string
    {
        return $this->externalUrl;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    private function setIdentifier(string $externalUrl): void
    {
        $urlSegments = parse_url($externalUrl);

        if (array_key_exists('query', $urlSegments)) {
            parse_str($urlSegments['query'], $querySegments);
            $this->identifier = $querySegments['id'];
        }
    }
}
