<?php

declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Domain\Model;

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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity as CoreAbstractEntity;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractEntity extends CoreAbstractEntity implements DummyInterface, ExternalIdInterface
{
    use DummyTrait, ExternalIdTrait;

    /**
     * @var string
     */
    protected $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
