<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractBaseRepository extends Repository
{

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}