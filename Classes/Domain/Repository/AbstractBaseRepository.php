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

use Doctrine\DBAL\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractBaseRepository extends Repository
{

    /**
     * @var Dispatcher
     *
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


    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * @param string $table
     * @param array $entries
     *
     * @return array|null
     */
    public function findOldEntriesByExternalUidsDiffForTable(string $table, array $entries)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        return $queryBuilder
            ->select('*')
            ->from(self::ENTRY_TABLE)
            ->where($queryBuilder->expr()->notIn('external_id', $queryBuilder->createNamedParameter($entries, Connection::PARAM_INT_ARRAY)))
            ->execute()
            ->fetchAll();
    }

    /**
     * @param int $externalId
     * @param string $hash
     *
     * @return int
     */
    public function countByExternalIdAndHash($externalId, $hash): int
    {
        $query = $this->createQuery();
        $constraints = [];
        $constraints[] = $query->equals('externalId', $externalId);
        $constraints[] = $query->equals('hash', $hash);

        return $query->matching($query->logicalAnd($constraints))->execute()->count();
    }

    /**
     * @param int $externalId
     *
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
     * @return mixed|string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param string $table
     *
     * @return \TYPO3\CMS\Core\Database\Connection
     */
    protected function getDatabaseConnectionForTable(string $table): \TYPO3\CMS\Core\Database\Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
                      ->getConnectionForTable($table);
    }
}
