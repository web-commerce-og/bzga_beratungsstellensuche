<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Hooks;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @author Sebastian Schreiber
 */
class DataHandlerProcessor
{
    public function processCmdmap_deleteAction(string $table, int $id, int $recordToDelete, bool &$recordWasDeleted, DataHandler $tceMain): void
    {
        if ($table === EntryRepository::ENTRY_TABLE) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $entryRepository = $objectManager->get(EntryRepository::class);
            $entryRepository->deleteByUid($id);
            $recordWasDeleted = true;
        }
    }

    public function processDatamap_postProcessFieldArray(string $status, string $table, string $id, array &$fieldArray, DataHandler &$reference): void
    {
        if ($table === 'tt_content' && $status === 'update' && isset($fieldArray['pi_flexform'])) {
            $checkFields = [
                'additional' => [
                    'settings.singlePid',
                    'settings.listPid',
                    'settings.backPid',
                    'settings.list.itemsPerPage',
                    'settings.formFields'
                ],
            ];

            $flexformData = GeneralUtility::xml2array($fieldArray['pi_flexform']);

            foreach ($checkFields as $sheet => $fields) {
                foreach ($fields as $field) {
                    if (isset($flexformData['data'][$sheet]['lDEF'][$field]['vDEF']) &&
                        trim($flexformData['data'][$sheet]['lDEF'][$field]['vDEF']) === ''
                    ) {
                        unset($flexformData['data'][$sheet]['lDEF'][$field]);
                    }
                }

                // If remaining sheet does not contain fields, then remove the sheet
                if (isset($flexformData['data'][$sheet]['lDEF']) && $flexformData['data'][$sheet]['lDEF'] === []) {
                    unset($flexformData['data'][$sheet]);
                }
            }

            $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
            $fieldArray['pi_flexform'] = $flexFormTools->flexArray2Xml($flexformData, true);
        }
    }
}
