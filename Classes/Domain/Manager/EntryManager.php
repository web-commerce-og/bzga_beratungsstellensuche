<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


use BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class EntryManager extends AbstractManager
{

    /**
     * @var EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @param AbstractEntity $entity
     */
    public function create(AbstractEntity $entity)
    {
    }

    /**
     * @param AbstractEntity $entity
     */
    public function remove(AbstractEntity $entity)
    {
    }


}