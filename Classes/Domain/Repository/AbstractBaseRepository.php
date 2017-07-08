<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Repository;

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
use Bzga\BzgaBeratungsstellensuche\Events;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractBaseRepository extends Repository
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @var array
     */
    protected $defaultOrderings = ['title' => QueryInterface::ORDER_ASCENDING];

    /**
     * @var string
     */
    const ENTRY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_entry';

    /**
     * @var string
     */
    const CATEGORY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_category';

    /**
     * @var string
     */
    const ENTRY_CATEGORY_MM_TABLE = 'tx_bzgaberatungsstellensuche_entry_category_mm';

    /**
     * @var string
     */
    const SYS_FILE_REFERENCE = 'sys_file_reference';

    /**
     * Debugs a SQL query from a QueryResult
     *
     * @param QueryResultInterface $queryResult
     * @param bool $explainOutput
     * @return void
     */
    public function debugQuery(QueryResultInterface $queryResult, $explainOutput = false)
    {
        $databaseConnection = $this->getDatabaseConnection();
        $databaseConnection->debugOutput = 2;
        if ($explainOutput) {
            $databaseConnection->explainOutput = true;
        }
        $databaseConnection->store_lastBuiltQuery = true;
        $queryResult->toArray();
        DebugUtility::debug($databaseConnection->debug_lastBuiltQuery);

        $databaseConnection->store_lastBuiltQuery = false;
        $databaseConnection->explainOutput = false;
        $databaseConnection->debugOutput = false;
    }

    /**
     * @param string $table
     * @param array $entries
     * @return array|NULL
     */
    public function findOldEntriesByExternalUidsDiffForTable($table, $entries)
    {
        $databaseConnection = $this->getDatabaseConnection();
        # We fetch all entries in database which has not been imported
        $importedExternalUids = implode(',', $databaseConnection->cleanIntArray($entries));
        if ($importedExternalUids) {
            $oldEntries = $databaseConnection->exec_SELECTgetRows('uid', $table,
                'deleted = 0 AND external_id NOT IN(' . $importedExternalUids . ')');

            return $oldEntries;
        }

        return [];
    }

    /**
     * @param int $externalId
     * @param string $hash
     * @return int
     */
    public function countByExternalIdAndHash($externalId, $hash)
    {
        $query = $this->createQuery();
        $constraints = [];
        $constraints[] = $query->equals('externalId', $externalId);
        $constraints[] = $query->equals('hash', $hash);

        return $query->matching($query->logicalAnd($constraints))->execute()->count();
    }

    /**
     * @param $externalId
     * @return object
     */
    public function findOneByExternalId($externalId)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $object = $query->matching($query->equals('externalId', $externalId))->execute()->getFirst();

        return $object;
    }

    /**
     * @return mixed|QueryInterface
     */
    public function createQuery()
    {
        $query = parent::createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query;
    }

    /**
     * @return void
     */
    public function truncateAll()
    {
        $databaseConnection = $this->getDatabaseConnection();
        $databaseConnection->exec_TRUNCATEquery(self::CATEGORY_TABLE);
        $databaseConnection->exec_TRUNCATEquery(self::ENTRY_TABLE);
        $databaseConnection->exec_TRUNCATEquery(self::ENTRY_CATEGORY_MM_TABLE);
        $this->signalSlotDispatcher->dispatch(static::class, Events::TABLE_TRUNCATE_ALL_SIGNAL,
            ['databaseConnection' => $databaseConnection]);
    }

    /**
     * @return mixed|string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
