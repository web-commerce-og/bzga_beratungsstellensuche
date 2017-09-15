<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\ExternalIdTrait;
use Bzga\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap;
use Bzga\BzgaBeratungsstellensuche\Property\PropertyMapper;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use Countable;
use IteratorAggregate;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @author Sebastian Schreiber
 */
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
    protected $dataMap = [];

    /**
     * @var \SplObjectStorage
     */
    private $entries;

    /**
     * @var array
     */
    private $externalUids = [];

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap
     */
    private $dataMapFactory;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Property\PropertyMapper
     */
    private $propertyMapper;

    /**
     * AbstractManager constructor.
     * @param Dispatcher $signalSlotDispatcher
     * @param DataHandler $dataHandler
     * @param DataMap $dataMapFactory
     * @param PropertyMapper $propertyMapper
     */
    public function __construct(
        Dispatcher $signalSlotDispatcher,
        DataHandler $dataHandler,
        DataMap $dataMapFactory,
        PropertyMapper $propertyMapper
    ) {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
        $this->dataHandler = $dataHandler;
        $this->dataHandler->bypassAccessCheckForRecords = true;
        $this->dataHandler->admin = true;
        $this->dataMapFactory = $dataMapFactory;
        $this->propertyMapper = $propertyMapper;
        $this->entries = new \SplObjectStorage();
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

        $data = [
            'pid' => $entity->getPid()
        ];
        $properties = ObjectAccess::getGettablePropertyNames($entity);
        foreach ($properties as $propertyName) {
            $propertyNameLowercase = GeneralUtility::camelCaseToLowerCaseUnderscored($propertyName);
            if (isset($GLOBALS['TCA'][$tableName]['columns'][$propertyNameLowercase])) {
                $propertyValue = ObjectAccess::getProperty($entity, $propertyName);
                if ($typeConverter = $this->propertyMapper->supports($propertyValue,
                    TypeConverterInterface::CONVERT_BEFORE)
                ) {
                    $propertyValue = $typeConverter->convert($propertyValue,
                        [
                            'manager' => $this,
                            'tableUid' => $tableUid,
                            'tableName' => $tableName,
                            'entity' => $entity,
                        ]);
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
            $this->dataHandler->start($this->dataMap, []);
            $this->dataHandler->process_datamap();
            $this->dataMap = [];
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
     * @see \Bzga\BzgaBeratungsstellensuche\Hooks\DataHandlerProcessor
     */
    private function cleanUp()
    {
        $repository = $this->getRepository();
        $table = $this->dataMapFactory->getTableNameByClassName($repository->getObjectType());
        $oldEntries = $repository->findOldEntriesByExternalUidsDiffForTable($table, $this->externalUids);

        # Now we delete then entries via the datahandler, the actual deletion is done by a HOOK
        $cmd = [];
        foreach ($oldEntries as $oldEntry) {
            $cmd[$table][$oldEntry['uid']] = ['delete' => ''];
        }

        $this->dataHandler->start(null, $cmd);
        $this->dataHandler->process_cmdmap();
    }

    /**
     * @return \SplObjectStorage
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
     * @return \Bzga\BzgaBeratungsstellensuche\Domain\Repository\AbstractBaseRepository
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
            return uniqid('NEW_', false);
        }

        return $entity->getUid();
    }
}
