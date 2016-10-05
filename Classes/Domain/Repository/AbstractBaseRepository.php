<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractBaseRepository extends Repository
{

    /**
     * @var string
     */
    const ENTRY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_entry';

    /**
     * @var string
     */
    const RELIGION_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_religion';

    /**
     * @var string
     */
    const CATEGORY_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_category';

    /**
     * @var string
     */
    const LANGUAGE_ENTRY_MM_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_entry_language_mm';

    /**
     * @var string
     */
    const ENTRY_CATEGORY_MM_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_entry_category_mm';

    /**
     * @var string
     */
    const PNDCONSULTING_TABLE = 'tx_bzgaberatungsstellensuche_domain_model_pndconsulting';

    /**
     * @var string
     */
    const SYS_FILE_REFERENCE = 'sys_file_reference';


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
        $oldEntries = $databaseConnection->exec_SELECTgetRows('uid', $table,
            'deleted = 0 AND external_id NOT IN('.$importedExternalUids.')');

        return $oldEntries;
    }

    /**
     * @param $externalId
     * @param $hash
     * @return integer
     */
    public function countByExternalIdAndHash($externalId, $hash)
    {
        $query = $this->createQuery();
        $constraints = array();
        $constraints[] = $query->equals('externalId', $externalId);
        $constraints[] = $query->equals('hash', $hash);

        return $query->matching($query->logicalAnd($constraints))->execute()->count();
    }

    /**
     * @return mixed|\TYPO3\CMS\Extbase\Persistence\QueryInterface
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
        $databaseConnection->exec_TRUNCATEquery(self::LANGUAGE_ENTRY_MM_TABLE);
        $databaseConnection->exec_TRUNCATEquery(self::PNDCONSULTING_TABLE);
        $databaseConnection->exec_TRUNCATEquery(self::RELIGION_TABLE);
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