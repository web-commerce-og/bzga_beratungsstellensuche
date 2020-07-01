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
     */
    protected $signalSlotDispatcher;

    /**
     * @var array
     */
    protected $defaultOrderings = ['title' => QueryInterface::ORDER_ASCENDING];

    /**
     * @var string
     */
    public const ENTRY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_entry';

    /**
     * @var string
     */
    public const CATEGORY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_category';

    /**
     * @var string
     */
    public const ENTRY_CATEGORY_MM_TABLE = 'tx_bzgaberatungsstellensuche_entry_category_mm';

    /**
     * @var string
     */
    public const SYS_FILE_REFERENCE = 'sys_file_reference';

    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher): void
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    public function findOldEntriesByExternalUidsDiffForTable(string $table, array $entries): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        return $queryBuilder
            ->select('uid')
            ->from(self::ENTRY_TABLE)
            ->where($queryBuilder->expr()->notIn('external_id', $queryBuilder->createNamedParameter($entries, Connection::PARAM_INT_ARRAY)))
            ->execute()
            ->fetchAll();
    }

    public function countByExternalIdAndHash(int $externalId, string $hash): int
    {
        $query = $this->createQuery();
        $constraints = [];
        $constraints[] = $query->equals('externalId', $externalId);
        $constraints[] = $query->equals('hash', $hash);

        return $query->matching($query->logicalAnd($constraints))->execute()->count();
    }

    public function findOneByExternalId(int $externalId): object
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $object = $query->matching($query->equals('externalId', $externalId))->execute()->getFirst();

        return $object;
    }

    public function createQuery(): QueryInterface
    {
        $query = parent::createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    protected function getDatabaseConnectionForTable(string $table): \TYPO3\CMS\Core\Database\Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
                      ->getConnectionForTable($table);
    }
}
