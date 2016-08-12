<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

abstract class AbstractManager
{

    /**
     * @var Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @var PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * @param AbstractEntity $entity
     * @return mixed
     */
    abstract public function create(AbstractEntity $entity);

    /**
     * @param AbstractEntity $entity
     * @return mixed
     */
    abstract public function remove(AbstractEntity $entity);

}