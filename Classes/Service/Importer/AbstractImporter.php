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
use Bzga\BzgaBeratungsstellensuche\Service\Importer\Exception\ContentCouldNotBeFetchedException;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

/**
 * @author Sebastian Schreiber
 */
abstract class AbstractImporter implements ImporterInterface
{

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Serializer
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
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @param string $file
     * @param int $pid
     *
     * @throws FileDoesNotExistException
     * @throws ContentCouldNotBeFetchedException
     * @return void
     */
    public function importFromFile($file, $pid = 0)
    {
        $file = GeneralUtility::getFileAbsFileName($file);

        if (! file_exists($file)) {
            throw new FileDoesNotExistException(sprintf('The file %s does not exists', $file));
        }

        $this->importFromSource($file, $pid);
    }

    /**
     * @param string $url
     * @param int $pid
     *
     * @throws UnexpectedValueException
     * @throws ContentCouldNotBeFetchedException
     * @return void
     */
    public function importFromUrl($url, $pid = 0)
    {
        if (! GeneralUtility::isValidUrl($url)) {
            throw new UnexpectedValueException(sprintf('This is not a valid url: %s', $url));
        }

        $this->importFromSource($url, $pid);
    }

    /**
     * @param string $source
     * @param int $pid
     *
     * @throws ContentCouldNotBeFetchedException
     */
    private function importFromSource($source, $pid)
    {
        $content = GeneralUtility::getUrl($source);
        if (false === $content) {
            throw new ContentCouldNotBeFetchedException('The content could not be fetched');
        }

        $this->import($content, $pid);
    }
}
