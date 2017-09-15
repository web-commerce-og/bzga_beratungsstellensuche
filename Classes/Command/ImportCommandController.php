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
use Bzga\BzgaBeratungsstellensuche\Console\NullConsoleOutput;
use Bzga\BzgaBeratungsstellensuche\Console\ProgressBarInterface;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
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
     * @var \TYPO3\CMS\Extbase\Mvc\Cli\ConsoleOutput|ProgressbarInterface
     */
    protected $outputDecorator;

    /**
     * ImportCommandController constructor.
     */
    public function __construct()
    {
        if(!property_exists($this, 'output')) {
            $this->outputDecorator = $this->objectManager->get(NullConsoleOutput::class);
        } else {
            $this->outputDecorator = $this->output;
        }
    }


    /**
     * Import from file
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
        $this->outputDecorator->progressStart($this->xmlImporter->count());
        for ($i = 0; $i < $this->xmlImporter->count(); $i ++) {
            $this->xmlImporter->next();
            $this->outputDecorator->progressAdvance();
        }
        $this->outputDecorator->progressFinish();
    }
}
