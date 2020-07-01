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

/**
 * @author Sebastian Schreiber
 */
class Importer
{

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function truncateAll(XmlImporter $importer, SimpleXMLIterator $sxe, int $pid, BaseSerializer $serializer): void
    {
        $this->entryRepository->truncateAll();
    }
}
