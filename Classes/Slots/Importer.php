<?php

namespace Bzga\BzgaBeratungsstellensuche\Slots;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Serializer as BaseSerializer;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter;
use SimpleXMLIterator;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * @author Sebastian Schreiber
 */
class Importer
{

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    public function injectEntryRepository(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param XmlImporter $importer
     * @param SimpleXMLIterator $sxe
     * @param int $pid
     * @param BaseSerializer $serializer
     *
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function truncateAll(XmlImporter $importer, SimpleXMLIterator $sxe, $pid, BaseSerializer $serializer)
    {
        $this->entryRepository->truncateAll();
    }


}
