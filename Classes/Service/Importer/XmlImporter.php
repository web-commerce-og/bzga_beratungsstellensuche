<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Importer;


use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\PndConsulting;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Events;


class XmlImporter extends AbstractImporter
{

    /**
     * @var string
     */
    const FORMAT = 'xml';


    /**
     * @param $content
     * @param int $pid
     * @return mixed|void
     */
    public function import($content, $pid = 0)
    {
        $sxe = new \SimpleXMLIterator($content);

        $this->emitPreImportSignal($sxe);

        # Import religions
        foreach ($sxe->konfessionen as $religions) {
            foreach ($religions as $religionData) {
                $externalId = (integer)$religionData->index;
                $objectToPopulate = $this->religionManager->getRepository()->findOneByExternalId($externalId);
                $religion = $this->serializer->deserialize($religionData->asXml(), Religion::class, self::FORMAT,
                    array('object_to_populate' => $objectToPopulate));
                /* @var $religion Religion */
                $religion->setPid($pid);
                $this->religionManager->create($religion);
            }
        }

        # Import beratungsarten
        foreach ($sxe->beratungsarten as $categories) {
            foreach ($categories as $categoryData) {
                $externalId = (integer)$categoryData->index;
                $objectToPopulate = $this->categoryManager->getRepository()->findOneByExternalId($externalId);
                $category = $this->serializer->deserialize($categoryData->asXml(), Category::class, self::FORMAT,
                    array('object_to_populate' => $objectToPopulate));
                /* @var $category Category */
                $category->setPid($pid);
                $this->categoryManager->create($category);
            }
        }

        # Import pnd beratungen
        foreach ($sxe->pndberatungen as $pndConsultings) {
            foreach ($pndConsultings as $pndConsultingData) {
                $externalId = (integer)$pndConsultingData->index;
                $objectToPopulate = $this->pndConsultingManager->getRepository()->findOneByExternalId($externalId);
                $pndConsulting = $this->serializer->deserialize($pndConsultingData->asXml(), PndConsulting::class,
                    self::FORMAT, array('object_to_populate' => $objectToPopulate));
                /* @var $pndConsulting PndConsulting */
                $pndConsulting->setPid($pid);
                $this->pndConsultingManager->create($pndConsulting);
            }
        }

        foreach ($sxe->entrys as $entries) {
            foreach ($entries as $entryData) {
                $externalId = (integer)$entryData->index;
                $objectToPopulate = $this->entryManager->getRepository()->findOneByExternalId($externalId);
                $entry = $this->serializer->deserialize($entryData->asXml(), Entry::class, self::FORMAT,
                    array('object_to_populate' => $objectToPopulate));
                /* @var $entry Entry */
                $entry->setPid($pid);
                $this->entryManager->create($entry);
            }
        }
        # In the end we are calling all the managers to persist, this saves a lot of memory
        $this->persist();
        $this->emitPostImportSignal($sxe);
    }

    /**
     * @param \SimpleXMLIterator $sxe
     */
    private function emitPreImportSignal(\SimpleXMLIterator $sxe)
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, Events::PRE_IMPORT_SIGNAL, array($this, $sxe));
    }

    /**
     * @param \SimpleXMLIterator $sxe
     */
    private function emitPostImportSignal(\SimpleXMLIterator $sxe)
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, Events::POST_IMPORT_SIGNAL, array($this, $sxe));
    }

}