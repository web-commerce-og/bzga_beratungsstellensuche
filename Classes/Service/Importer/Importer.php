<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Importer;


use Bzga\BzgaBeratungsstellensuche\Domain\Manager\CategoryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\EntryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\PndConsultingManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\ReligionManager;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\PndConsulting;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Service\CacheService;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use IteratorAggregate;
use Countable;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class Importer implements IteratorAggregate, Countable
{

    const PRE_IMPORT_SIGNAL = 'preImport';

    const POST_IMPORT_SIGNAL = 'postImport';

    /**
     * @var Serializer
     * @inject
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $entries = array();

    /**
     * @var EntryManager
     * @inject
     */
    protected $entryManager;

    /**
     * @var CategoryManager
     * @inject
     */
    protected $categoryManager;

    /**
     * @var ReligionManager
     * @inject
     */
    protected $religionManager;

    /**
     * @var PndConsultingManager
     * @inject
     */
    protected $pndConsultingManager;

    /**
     * @var CacheService
     * @inject
     */
    protected $cacheService;

    /**
     * @var Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @param $file
     */
    public function importFromFile($file)
    {
        if (file_exists($file)) {
            $sxe = new \SimpleXMLIterator($file, null, true);

            return $this->import($sxe);
        } else {
            throw new FileDoesNotExistException(sprintf('The provided file %s does not exist', $file));
        }
    }

    /**
     * @param $url
     */
    public function importFromUrl($url)
    {
        $xml = GeneralUtility::getUrl($url);
        $sxe = new \SimpleXMLIterator($xml);
        $this->import($sxe);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->entries);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return $this->getIterator()->count();
    }


    /**
     * @param \SimpleXMLIterator $sxe
     * @return array
     */
    private function import(\SimpleXMLIterator $sxe)
    {
        $this->emitPreImportSignal($sxe);
        // Import relations first
        foreach ($sxe->konfessionen as $religions) {
            foreach ($religions as $religionData) {
                $religion = $this->serializer->deserialize($religionData->asXml(), Religion::class, 'xml');
                $this->religionManager->create($religion);
            }
        }
        foreach ($sxe->beratungsarten as $categories) {
            foreach ($categories as $categoryData) {
                $category = $this->serializer->deserialize($categoryData->asXml(), Category::class, 'xml');
                $this->categoryManager->create($category);
            }
        }

        foreach ($sxe->pndberatungen as $pndConsultings) {
            foreach ($pndConsultings as $pndConsultingData) {
                $pndConsulting = $this->serializer->deserialize($pndConsultingData->asXml(), PndConsulting::class,
                    'xml');
                $this->pndConsultingManager->create($pndConsulting);
            }
        }


        foreach ($sxe->entrys as $entries) {
            foreach ($entries as $entryData) {
                $entry = $this->serializer->deserialize($entryData->asXml(), Entry::class, 'xml');
                $this->entryManager->remove($entry);
                $this->entries[] = $entry;
                $this->entryManager->create($entry);
            }
        }

        $this->emitPostImportSignal($sxe);
        $this->cacheService->clearCache();
    }

    /**
     * @param \SimpleXMLIterator $sxe
     */
    private function emitPreImportSignal(\SimpleXMLIterator $sxe)
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::PRE_IMPORT_SIGNAL, array($this, $sxe));
    }

    /**
     * @param \SimpleXMLIterator $sxe
     */
    private function emitPostImportSignal(\SimpleXMLIterator $sxe)
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::POST_IMPORT_SIGNAL, array($this, $sxe));
    }

}