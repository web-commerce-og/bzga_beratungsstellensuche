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

/**
 * @author Sebastian Schreiber
 */
class XmlImporter extends AbstractImporter
{

    /**
     * @var string
     */
    const FORMAT = 'xml';

    /**
     * @param $content
     * @param int $pid
     * @return void
     */
    public function import($content, $pid = 0)
    {
        $sxe = new \SimpleXMLIterator($content);

        $signalArguments = [$this, $sxe, $pid, $this->serializer];

        $this->emitImportSignal($signalArguments, Events::PRE_IMPORT_SIGNAL);

        # Import beratungsarten
        $this->convertRelations($sxe->beratungsarten->beratungsart, $this->categoryManager, Category::class, $pid);

        # Import entries
        $this->convertRelations($sxe->entrys->entry, $this->entryManager, Entry::class, $pid);

        # In the end we are calling all the managers to persist, this saves a lot of memory
        $this->emitImportSignal($signalArguments, Events::POST_IMPORT_SIGNAL);
        $this->persist();
    }

    /**
     * @param \Traversable $relations
     * @param AbstractManager $manager
     * @param $relationClassName
     * @param int $pid
     */
    public function convertRelations(\Traversable $relations = null, AbstractManager $manager, $relationClassName, $pid)
    {
        if ($relations instanceof \Traversable) {
            foreach ($relations as $relationData) {
                $externalId = (integer)$relationData->index;
                $objectToPopulate = $manager->getRepository()->findOneByExternalId($externalId);
                $relationObject = $this->serializer->deserialize($relationData->asXml(), $relationClassName,
                    self::FORMAT,
                    ['object_to_populate' => $objectToPopulate]);
                $relationObject->setPid($pid);
                $manager->create($relationObject);
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
}
