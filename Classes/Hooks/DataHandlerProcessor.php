<?php


namespace BZgA\BzgaBeratungsstellensuche\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;

class DataHandlerProcessor
{

    /**
     * @param string $table
     * @param integer $id
     * @param integer $recordToDelete
     * @param boolean $recordWasDeleted
     * @param DataHandler $tceMain
     */
    public function processCmdmap_deleteAction($table, $id, $recordToDelete, &$recordWasDeleted, DataHandler $tceMain)
    {
        if ($table === EntryRepository::ENTRY_TABLE) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /* @var $objectManager ObjectManager */
            $entryRepository = $objectManager->get(EntryRepository::class);
            /* @var $entryRepository EntryRepository */
            $entryRepository->deleteByUid($id);
            $recordWasDeleted = true;
        }
    }


}