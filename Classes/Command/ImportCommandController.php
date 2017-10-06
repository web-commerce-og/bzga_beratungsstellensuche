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
use Bzga\BzgaBeratungsstellensuche\Console\ProgressBarInterface;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\Exception\ContentCouldNotBeFetchedException;
use InvalidArgumentException;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Cli\ConsoleOutput;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use UnexpectedValueException;

/**
 * @author Sebastian Schreiber
 */
class ImportCommandController extends CommandController implements ProgressBarInterface
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
     * @var \TYPO3\CMS\Extbase\Mvc\Cli\ConsoleOutput|ProgressbarInterface
     */
    protected $output;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * Delete all entries, files and relations from database
     * @throws \InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    public function importFromFileCommand($file, $pid = 0, $forceReImport = false)
    {
        try {
            $this->xmlImporter->importFromFile($file, $pid);
            $this->import($forceReImport);
        } catch (FileDoesNotExistException $e) {
            // @TODO: How to handle the exception in practice?
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
     * @throws InvalidArgumentException
     * @throws ContentCouldNotBeFetchedException
     */
    public function importFromUrlCommand($url, $pid = 0, $forceReImport = false)
    {
        try {
            $this->xmlImporter->importFromUrl($url, $pid);
            $this->import($forceReImport);
        } catch (UnexpectedValueException $e) {
            // @TODO: How to handle the exception in practice?
            throw new $e;
        }
    }

    /**
     * @param bool $forceReImport
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function import($forceReImport = false)
    {
        if ($forceReImport) {
            $this->entryRepository->truncateAll();
        }

        $this->progressStart($this->xmlImporter->count());
        foreach ($this->xmlImporter as $value) {
            $this->xmlImporter->importEntry($value);
            $this->progressAdvance();
        }
        $this->xmlImporter->persist();
        $this->xmlImporter->cleanUp();
        $this->progressFinish();
    }

    /**
     * @param int $count
     */
    public function progressStart($count)
    {
        if ($this->output instanceof ConsoleOutput) {
            $this->output->progressStart($count);
        }
    }

    /**
     * @return void
     */
    public function progressAdvance()
    {
        if ($this->output instanceof ConsoleOutput) {
            $this->output->progressAdvance();
        }
    }

    /**
     * @return void
     */
    public function progressFinish()
    {
        if ($this->output instanceof ConsoleOutput) {
            $this->output->progressFinish();
        }
    }
}
