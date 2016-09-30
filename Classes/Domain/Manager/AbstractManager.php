<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility;
use BZgA\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\ExternalIdTrait;
use IteratorAggregate;
use Countable;
use ArrayIterator;

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
    private $typeConverters;

    /**
     * @var array
     */
    private $dataMap;

    /**
     * @var array
     */
    private $entries;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap
     */
    private $dataMapFactory;

    /**
     * AbstractManager constructor.
     * @param \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     * @param \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap $dataMapFactory
     */
    public function __construct(
        \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher,
        \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler,
        \BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap $dataMapFactory
    ) {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->dataHandler = $dataHandler;
        $this->dataHandler->bypassAccessCheckForRecords = true;
        $this->dataHandler->admin = true;
        $this->dataMapFactory = $dataMapFactory;
        $this->initializeTypeConverters();
    }

    /**
     * @param $externalId
     * @return AbstractEntity|null
     */
    public function findOneByExternalId($externalId)
    {
        return $this->getRepository()->findOneByExternalId($externalId);
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
        $this->addEntryUid($entity->getExternalId());


        $data = array();
        $data['pid'] = $entity->getPid();
        $properties = ObjectAccess::getGettablePropertyNames($entity);
        foreach ($properties as $propertyName) {
            $propertyNameLowercase = GeneralUtility::camelCaseToLowerCaseUnderscored($propertyName);
            if (isset($GLOBALS['TCA'][$tableName]['columns'][$propertyNameLowercase])) {
                $propertyValue = ObjectAccess::getProperty($entity, $propertyName);
                foreach ($this->typeConverters as $typeConverter) {
                    /* @var $typeConverter TypeConverterInterface */
                    if (true === $typeConverter->supports($propertyValue)) {
                        $propertyValue = $typeConverter->convert($propertyValue);
                        break;
                    }
                }
                $data[$propertyNameLowercase] = $propertyValue;
            }
        }

        // We only update the entry if something has really changed. Speeding import drastically
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
     * @return void
     * @see \BZgA\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor
     */
    private function cleanUp()
    {
        $repository = $this->getRepository();
        $table = $this->dataMapFactory->getTableNameByClassName($repository->getObjectType());
        $oldEntries = $repository->findOldEntriesByExternalUidsDiffForTable($table, $this->entries);

        # Now we delete then entries via the datahandler, the actual deletion is done by a HOOK
        $cmd = array();
        foreach ($oldEntries as $oldEntry) {
            $cmd[$table][$oldEntry['uid']] = array('delete' => '');
        }

        $this->dataHandler->start(null, $cmd);
        $this->dataHandler->process_cmdmap();
    }


    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entries);
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
     * @param $uid
     */
    private function addEntryUid($uid)
    {
        $this->entries[] = (integer)$uid;
    }

    /**
     * @param AbstractEntity $entity
     * @return int|string
     */
    private function getUid(AbstractEntity $entity)
    {
        # @TODO: Is there a better solution to check?
        if ($entity->_isNew()) {
            return 'NEW'.uniqid();
        }

        return $entity->getUid();
    }

    /**
     * @return void
     */
    private function initializeTypeConverters()
    {
        # @TODO: Move this to a dedicated class
        $registeredTypeConverters = ExtensionManagementUtility::getRegisteredTypeConverters();
        foreach ($registeredTypeConverters as $typeConverterClassName) {
            // @TODO Maybe we have to use better the objectmanager for the DI-Graph
            $this->typeConverters[] = GeneralUtility::makeInstance($typeConverterClassName);
        }
    }


}