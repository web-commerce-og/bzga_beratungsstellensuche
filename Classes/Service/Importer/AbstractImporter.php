<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Importer;

use UnexpectedValueException;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;


abstract class AbstractImporter implements ImporterInterface
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer
     * @inject
     */
    protected $serializer;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Manager\EntryManager
     * @inject
     */
    protected $entryManager;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Manager\CategoryManager
     * @inject
     */
    protected $categoryManager;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Manager\ReligionManager
     * @inject
     */
    protected $religionManager;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Manager\PndConsultingManager
     * @inject
     */
    protected $pndConsultingManager;

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;


    /**
     * @param $file
     * @param int $pid
     * @return mixed|void
     * @throws FileDoesNotExistException
     */
    public function importFromFile($file, $pid = 0)
    {
        $file = GeneralUtility::getFileAbsFileName($file);
        if (!file_exists($file)) {
            throw new FileDoesNotExistException();
        }
        $content = GeneralUtility::getUrl($file);
        $this->import($content, $pid);
    }

    /**
     * @param $url
     * @param int $pid
     * @return mixed|void
     */
    public function importFromUrl($url, $pid = 0)
    {
        if (!GeneralUtility::isValidUrl($url)) {
            throw new UnexpectedValueException(sprintf('This is not a valid url: %s', $url));
        }
        $content = GeneralUtility::getUrl($url);
        $this->import($content, $pid);
    }

    /**
     * @return void
     */
    protected function persist()
    {
        $this->pndConsultingManager->persist();
        $this->religionManager->persist();
        $this->categoryManager->persist();
        $this->entryManager->persist();
    }

}