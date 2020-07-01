<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Service\Importer;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\AbstractManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\AbstractEntity;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Category;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\ExternalIdTrait;
use Bzga\BzgaBeratungsstellensuche\Events;
use Countable;
use IteratorAggregate;
use SimpleXMLIterator;
use Traversable;

/**
 * @author Sebastian Schreiber
 */
class XmlImporter extends AbstractImporter implements Countable, IteratorAggregate
{

    /**
     * @var string
     */
    public const FORMAT = 'xml';

    /**
     * @var int
     */
    private $pid;

    /**
     * @var SimpleXMLIterator
     */
    private $entries;

    /**
     * @var SimpleXMLIterator
     */
    private $sxe;

    public function import(string $content, int $pid = 0): void
    {
        $this->pid = $pid;

        $this->sxe = new SimpleXMLIterator($content);

        $this->emitImportSignal(Events::PRE_IMPORT_SIGNAL);

        // Import beratungsarten
        $this->convertRelations($this->sxe->beratungsarten->beratungsart, $this->categoryManager, Category::class, $pid);
        $this->categoryManager->persist();

        $this->entries = $this->sxe->entrys->entry;
    }

    public function importEntry(SimpleXMLIterator $entry): void
    {
        $this->convertRelation($this->entryManager, Entry::class, $this->pid, $entry);
    }

    public function getIterator(): \SimpleXMLIterator
    {
        return $this->entries;
    }

    public function count(): int
    {
        return count($this->entries);
    }

    public function persist(): void
    {
        // In the end we are calling all the managers to persist, this saves a lot of memory
        $this->emitImportSignal(Events::POST_IMPORT_SIGNAL);
        $this->entryManager->persist();
    }

    public function convertRelations(Traversable $relations = null, AbstractManager $manager, string $relationClassName, int $pid): void
    {
        if ($relations instanceof Traversable) {
            foreach ($relations as $relationData) {
                $this->convertRelation($manager, $relationClassName, $pid, $relationData);
            }
        }
    }

    private function emitImportSignal(string $signal): void
    {
        $this->signalSlotDispatcher->dispatch(static::class, $signal, [$this, $this->sxe, $this->pid, $this->serializer]);
    }

    private function convertRelation(AbstractManager $manager, string $relationClassName, int $pid, \SimpleXMLIterator $relationData): void
    {
        $externalId = (integer)$relationData->index;
        $objectToPopulate = $manager->getRepository()->findOneByExternalId($externalId);
        /** @var AbstractEntity|ExternalIdTrait $relationObject */
        $relationObject = $this->serializer->deserialize(
            $relationData->asXml(),
            $relationClassName,
            self::FORMAT,
            ['object_to_populate' => $objectToPopulate]
        );
        $relationObject->setPid($pid);
        $manager->create($relationObject);
    }

    public function cleanUp(): void
    {
        $this->entryManager->cleanUp();
    }

    public function __toString(): string
    {
        return __CLASS__;
    }
}
