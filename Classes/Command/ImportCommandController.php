<?php

namespace BZgA\BzgaBeratungsstellensuche\Command;

use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use UnexpectedValueException;

class ImportCommandController extends CommandController
{

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter
     * @inject
     */
    protected $xmlImporter;

    /**
     * Import from file
     * @param string $file
     * @param int $pid
     */
    public function importFromFileCommand($file, $pid = 0)
    {
        try {
            $this->xmlImporter->importFromFile($file, $pid);
        } catch (FileDoesNotExistException $e) {

        }
    }

    /**
     * Import from url
     * @param string $url
     * @param int $pid
     */
    public function importFromUrlCommand($url, $pid = 0)
    {
        try {
            $this->xmlImporter->importFromUrl($url, $pid);
        } catch (UnexpectedValueException $e) {

        }
    }

}
