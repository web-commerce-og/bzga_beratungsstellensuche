<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Religion extends AbstractEntity
{
    use DummyTrait;
    use ExternalIdTrait;

    /**
     * Konfession.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * Returns the title.
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }
}
