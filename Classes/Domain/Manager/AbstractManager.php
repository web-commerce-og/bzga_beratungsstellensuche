<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\ExternalIdTrait;
use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use BZgA\BzgaBeratungsstellensuche\Persistence\ExternalIdObjectStorage;
use IteratorAggregate;
use Countable;
use TYPO3\CMS\Core\SingletonInterface;

abstract class AbstractManager implements ManagerInterface, Countable, IteratorAggregate
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    private $signalSlotDispatcher;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandler;

    /**
     * @var array
     */
    protected $dataMap = array();

    /**
     * @var ExternalIdObjectStorage
     */
    private $entries;

    /**
     * @var array
     */
    private $externalUids = array();

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap
     */
    private $dataMapFactory;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Property\PropertyMapper
     */
    private $propertyMapper;


    /**
     * AbstractManager constructor.
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     * @param \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap $dataMapFactory
     * @param \BZgA\BzgaBeratungsstellensuche\Property\PropertyMapper $propertyMapper
     */
    public function __construct(
        \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher,
        \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler,
        \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap $dataMapFactory,
        \BZgA\BzgaBeratungsstellensuche\Property\PropertyMapper $propertyMapper
    ) {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->dataHandler = $dataHandler;
        $this->dataHandler->bypassAccessCheckForRecords = true;
        $this->dataHandler->admin = true;
        $this->dataMapFactory = $dataMapFactory;
        $this->propertyMapper = $propertyMapper;
        $this->entries = new ExternalIdObjectStorage();
    }

    /**
     * @param AbstractEntity|ExternalIdTrait $entity
     * @return void
     */
    public function create(AbstractEntity $entity)
    {
        $tableName = $this->dataMapFactory->getTableNameByClassName(get_class($entity));
        $tableUid = $this->getUid($entity);

        # Add external uid to stack of updated, or inserted entries, we need this for the clean up
        $this->entries->attach($entity);
        $this->externalUids[] = $entity->getExternalId();


        $data = array();
        $data['pid'] = $entity->getPid();
        $properties = ObjectAccess::getGettablePropertyNames($entity);
        foreach ($properties as $propertyName) {
            $propertyNameLowercase = GeneralUtility::camelCaseToLowerCaseUnderscored($propertyName);
            if (isset($GLOBALS['TCA'][$tableName]['columns'][$propertyNameLowercase])) {
                $propertyValue = ObjectAccess::getProperty($entity, $propertyName);
                if ($typeConverter = $this->propertyMapper->supports($propertyValue,
                    TypeConverterInterface::CONVERT_BEFORE)
                ) {
                    $propertyValue = $typeConverter->convert($propertyValue,
                        array(
                            'manager' => $this,
                            'tableUid' => $tableUid,
                            'tableName' => $tableName,
                            'entity' => $entity,
                        ));
                }
                $data[$propertyNameLowercase] = $propertyValue;
            }
        }


        // We only update the entry if something has really changed. Speeding up import drastically
        $entryHash = md5(serialize($data));
        if (0 === $this->getRepository()->countByExternalIdAndHash($entity->getExternalId(), $entryHash)) {
            $data['hash'] = $entryHash;
            $this->dataMap[$tableName][$tableUid] = $data;
        }
    }

    /**
     * @return void
     */
    public function persist()
    {
        if (!empty($this->dataMap)) {
            $this->dataHandler->start($this->dataMap, array());
            $this->dataHandler->process_datamap();
            $this->dataMap = array();
        }
        $this->cleanUp();
    }

    /**
     * @param $tableName
     * @param $tableUid
     * @param array $data
     */
    public function addDataMap($tableName, $tableUid, array $data)
    {
        $this->dataMap[$tableName][$tableUid] = $data;
    }

    /**
     * @return void
     * @see \BZgA\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor
     */
    private function cleanUp()
    {
        $repository = $this->getRepository();
        $table = $this->dataMapFactory->getTableNameByClassName($repository->getObjectType());
        $oldEntries = $repository->findOldEntriesByExternalUidsDiffForTable($table, $this->externalUids);

        # Now we delete then entries via the datahandler, the actual deletion is done by a HOOK
        $cmd = array();
        foreach ($oldEntries as $oldEntry) {
            $cmd[$table][$oldEntry['uid']] = array('delete' => '');
        }

        $this->dataHandler->start(null, $cmd);
        $this->dataHandler->process_cmdmap();
    }


    /**
     * @return ExternalIdObjectStorage
     */
    public function getIterator()
    {
        return $this->entries;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->entries);
    }


    /**
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Repository\AbstractBaseRepository
     */
    abstract public function getRepository();


    /**
     * @param AbstractEntity $entity
     * @return int|string
     */
    private function getUid(AbstractEntity $entity)
    {
        # @TODO: Is there a better solution to check? Can we bind it directly to the object? At the moment i am getting an error
        if ($entity->_isNew()) {
            return uniqid('NEW_');
        }

        return $entity->getUid();
    }


}