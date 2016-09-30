<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;

use TYPO3\CMS\Core\Utility\MathUtility;

class EntryRepository extends AbstractBaseRepository
{

    /**
     * @param $uid
     */
    public function deleteByUid($uid)
    {
        if (MathUtility::canBeInterpretedAsInteger($uid)) {
            $databaseConnection = $this->getDatabaseConnection();
            $databaseConnection->exec_DELETEquery(self::ENTRY_TABLE, 'uid = '.$uid);
            $databaseConnection->exec_DELETEquery(
                self::ENTRY_CATEGORY_MM_TABLE,
                'uid_local ='.$uid
            );
            $databaseConnection->exec_DELETEquery(
                self::LANGUAGE_ENTRY_MM_TABLE,
                'uid_local ='.$uid
            );
        }
    }
}
