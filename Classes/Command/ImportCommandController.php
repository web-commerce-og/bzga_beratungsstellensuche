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
     * Import from file
     *
     * @param string $file Path to xml file
     * @param int $pid Storage folder uid
     */
    public function importFromFileCommand($file, $pid = 0)
    {
        try {
            $this->xmlImporter->importFromFile($file, $pid);
            $this->import();
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
     */
    public function importFromUrlCommand($url, $pid = 0)
    {
        try {
            $this->xmlImporter->importFromUrl($url, $pid);
            $this->import();
        } catch (UnexpectedValueException $e) {
            // @TODO: How to handle the exception in practice?
            throw new $e;
        }
    }

    /**
     * @return void
     */
    private function import()
    {
        $this->progressStart($this->xmlImporter->count());
        foreach ($this->xmlImporter as $value) {
            $this->xmlImporter->importEntry($value);
            $this->xmlImporter->persist();
            $this->progressAdvance();
        }
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
