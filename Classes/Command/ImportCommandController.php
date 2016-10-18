<?php

namespace BZgA\BzgaBeratungsstellensuche\Command;

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

use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use UnexpectedValueException;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
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
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

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
