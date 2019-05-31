<?php

namespace Bzga\BzgaBeratungsstellensuche\Command;

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
use Bzga\BzgaBeratungsstellensuche\Service\Importer\Exception\ContentCouldNotBeFetchedException;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use UnexpectedValueException;

/**
 * @author Sebastian Schreiber
 */
class ImportCommandController extends CommandController
{

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter
     * @inject
     */
    protected $xmlImporter;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * Delete all entries, files and relations from database
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function truncateAllCommand()
    {
        $this->entryRepository->truncateAll();
    }

    /**
     * Import from file
     *
     * @param string $file Path to xml file
     * @param int $pid Storage folder uid
     * @param bool $forceReImport
     *
     * @throws ContentCouldNotBeFetchedException
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function importFromFileCommand($file, $pid = 0, $forceReImport = false)
    {
        try {
            $this->xmlImporter->importFromFile($file, $pid);
            $this->import($forceReImport);
        } catch (FileDoesNotExistException $e) {
            throw new $e;
        }
    }

    /**
     * Import from url
     *
     * @param string $url Url to import the data
     * @param int $pid Storage folder uid
     * @param bool $forceReImport
     *
     * @throws ContentCouldNotBeFetchedException
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function importFromUrlCommand($url, $pid = 0, $forceReImport = false)
    {
        try {
            $this->xmlImporter->importFromUrl($url, $pid);
            $this->import($forceReImport);
        } catch (UnexpectedValueException $e) {
            throw new $e;
        }
    }

    /**
     * @param bool $forceReImport
     *
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    private function import($forceReImport = false)
    {
        if ($forceReImport) {
            $this->entryRepository->truncateAll();
        }

        foreach ($this->xmlImporter as $value) {
            $this->xmlImporter->importEntry($value);
        }
        $this->xmlImporter->persist();
        $this->xmlImporter->cleanUp();
    }
}
