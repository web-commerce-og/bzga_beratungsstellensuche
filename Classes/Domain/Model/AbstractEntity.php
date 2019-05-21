<?php


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
     * The title of the category.
     *
     * @var string
     */
    protected $title;

    /**
     * AbstractEntity constructor.
     * @param string $title
     */
    public function __construct($title = '')
    {
        $this->title = $title;
    }

    /**
     * Returns the category title.
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the category title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    /**
     * Is called if object is transformed to string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }
}
