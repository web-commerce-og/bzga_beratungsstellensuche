<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity as CoreAbstractEntity;

abstract class AbstractEntity extends CoreAbstractEntity
{

    use DummyTrait;
    use ExternalIdTrait;

    /**
     * The title of the category.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

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