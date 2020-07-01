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
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use UnexpectedValueException;

/**
 * @author Sebastian Schreiber
 */
class ImportCommandController extends CommandController
{

    /**
     * @var XmlImporter
     */
    protected $xmlImporter;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    public function truncateAllCommand(): void
    {
        $this->entryRepository->truncateAll();
    }

    public function importFromFileCommand(string $file, int $pid = 0, bool $forceReImport = false): void
    {
        try {
            $this->xmlImporter->importFromFile($file, $pid);
            $this->import($forceReImport);
        } catch (FileDoesNotExistException $e) {
            throw new $e;
        }
    }

    public function importFromUrlCommand(string $url, int $pid = 0, bool $forceReImport = false): void
    {
        try {
            $this->xmlImporter->importFromUrl($url, $pid);
            $this->import($forceReImport);
        } catch (UnexpectedValueException $e) {
            throw new $e;
        }
    }

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectSignalSlotDispatcher(Dispatcher $signalSlotDispatcher): void
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    public function injectXmlImporter(XmlImporter $xmlImporter): void
    {
        $this->xmlImporter = $xmlImporter;
    }

    private function import(bool $forceReImport = false): void
    {
        if ($forceReImport) {
            $this->entryRepository->truncateAll();
        }

        $persistBatch = 200;
        $i = 0;

        $this->output->progressStart($this->xmlImporter->count());
        foreach ($this->xmlImporter as $value) {
            $this->xmlImporter->importEntry($value);
            $this->output->progressAdvance();

            if ($i === $persistBatch) {
                $this->xmlImporter->persist();
                $i = 0;
            } else {
                $i++;
            }
        }
        $this->xmlImporter->persist();
        $this->output->progressFinish();
        $this->xmlImporter->cleanUp();
    }
}
