<?php


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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Category;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Events;
use Countable;
use Iterator;
use SimpleXMLIterator;
use Traversable;

/**
 * @author Sebastian Schreiber
 */
class XmlImporter extends AbstractImporter implements Countable, Iterator
{

    /**
     * @var string
     */
    const FORMAT = 'xml';

    /**
     * @var int
     */
    private $pid;

    /**
     * @var SimpleXMLIterator
     */
    private $entries;

    /**
     * @param string $content
     * @param int $pid
     * @return void
     */
    public function import($content, $pid = 0)
    {
        $this->pid = $pid;

        $sxe = new SimpleXMLIterator($content);

        $signalArguments = [$this, $sxe, $pid, $this->serializer];

        $this->emitImportSignal($signalArguments, Events::PRE_IMPORT_SIGNAL);

        # Import beratungsarten
        $this->convertRelations($sxe->beratungsarten->beratungsart, $this->categoryManager, Category::class, $pid);
        $this->categoryManager->persist();

        $this->entries = $sxe->entries;

        # In the end we are calling all the managers to persist, this saves a lot of memory
        $this->emitImportSignal($signalArguments, Events::POST_IMPORT_SIGNAL);
        $this->entryManager->persist();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->entries->current();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->convertRelation($this->entryManager, Entry::class, $this->pid, $this->entries->current());
        $this->entries->next();
    }

    /**
     * @return bool
     */
    public function key()
    {
        return $this->entries->valid();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->entries->valid();
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->entries->rewind();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->entries->entry);
    }

    /**
     * @param \Traversable $relations
     * @param AbstractManager $manager
     * @param $relationClassName
     * @param int $pid
     */
    public function convertRelations(Traversable $relations = null, AbstractManager $manager, $relationClassName, $pid)
    {
        if ($relations instanceof Traversable) {
            foreach ($relations as $relationData) {
                $this->convertRelation($manager, $relationClassName, $pid, $relationData);
            }
        }
    }

    /**
     * @param array $signalArguments
     * @param string $signal
     */
    private function emitImportSignal(array $signalArguments, $signal)
    {
        $this->signalSlotDispatcher->dispatch(static::class, $signal, $signalArguments);
    }

    /**
     * @param AbstractManager $manager
     * @param string $relationClassName
     * @param int $pid
     * @param SimpleXMLIterator $relationData
     */
    private function convertRelation(AbstractManager $manager, $relationClassName, $pid, $relationData)
    {
        $externalId       = (integer)$relationData->index;
        $objectToPopulate = $manager->getRepository()->findOneByExternalId($externalId);
        $relationObject   = $this->serializer->deserialize($relationData->asXml(), $relationClassName,
            self::FORMAT,
            ['object_to_populate' => $objectToPopulate]);
        $relationObject->setPid($pid);
        $manager->create($relationObject);
    }

    /**
     * @return SimpleXMLIterator
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
