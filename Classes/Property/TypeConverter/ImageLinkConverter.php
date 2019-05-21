<?php


namespace Bzga\BzgaBeratungsstellensuche\Property\TypeConverter;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\ExternalIdInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\ValueObject\ImageLink;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\Exception\DownloadException;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterBeforeInterface;
use Bzga\BzgaBeratungsstellensuche\Property\TypeConverterInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\File as FalFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;

/**
 * @author Sebastian Schreiber
 */
class ImageLinkConverter implements TypeConverterBeforeInterface
{

    /**
     * Folder where the file upload should go to (including storage).
     */
    const CONFIGURATION_UPLOAD_FOLDER = 1;

    /**
     * How to handle a upload when the name of the uploaded file conflicts.
     */
    const CONFIGURATION_UPLOAD_CONFLICT_MODE = 2;

    /**
     * Whether to replace an already present resource.
     * Useful for "maxitems = 1" fields and properties
     * with no ObjectStorage annotation.
     */
    const CONFIGURATION_ALLOWED_FILE_EXTENSIONS = 4;

    /**
     * @var string
     */
    private $defaultUploadFolder = '1:/user_upload/tx_bzgaberatungsstellensuche';

    /**
     * @var string
     */
    private $tempFolder = PATH_site . 'typo3temp/tx_bzgaberatungsstellensuche/';

    /**
     * One of 'cancel', 'replace', 'changeName'
     *
     * @var string
     */
    private $defaultConflictMode = 'replace';

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    private $resourceFactory;

    /**
     * @var array
     */
    private static $imageMimeTypes = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'svg'  => 'image/svg+xml',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
    ];

    /**
     * @var DataHandler|null
     */
    private $dataHandler;

    /**
     * ImageLinkConverter constructor.
     *
     * @param DataHandler|null $dataHandler
     */
    public function __construct(DataHandler $dataHandler = null)
    {
        if (null === $dataHandler) {
            $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        }
        $this->dataHandler                              = $dataHandler;
        $this->dataHandler->bypassAccessCheckForRecords = true;
        $this->dataHandler->admin                       = true;
    }

    /**
     * @param ImageLink|mixed $source
     * @param string $type
     *
     * @return bool
     */
    public function supports($source, $type = TypeConverterInterface::CONVERT_BEFORE)
    {
        if (! $source instanceof ImageLink) {
            return false;
        }

        return true;
    }

    /**
     * @param ImageLink $source
     * @param AbstractEntity|array $configuration
     *
     * @return int
     * @throws TypeConverterException
     */
    public function convert($source, array $configuration = null)
    {
        // Check if we have no image url, return 0 if not
        if ('' === $source->getExternalUrl()) {
            return 0;
        }

        // First of all we delete the old references
        /** @var $entity ExternalIdInterface|AbstractEntity */
        $entity = $configuration['entity'];

        $fileReferenceData = [
            'table_local' => 'sys_file',
            'tablenames'  => $configuration['tableName'],
            'uid_foreign' => $configuration['tableUid'],
            'fieldname'   => $configuration['tableField'],
            'pid'         => $entity->getPid(),
        ];

        if (! $entity->_isNew()) {
            $this->deleteOldFileReferences($fileReferenceData);
        }

        try {
            $fileReferenceData['uid_local'] = $this->getFileUid($source, $entity);

            $fileReferenceUid = uniqid('NEW_', false);
            $dataMap          = [];
            if ($this->dataHandler instanceof DataHandler) {
                $dataMap['sys_file_reference'][$fileReferenceUid] = $fileReferenceData;
                $this->dataHandler->start($dataMap, []);
                $this->dataHandler->process_datamap();
                return $this->dataHandler->substNEWwithIDs[$fileReferenceUid];
            }

            return $fileReferenceUid;
        } catch (TypeConverterException $e) {
        } catch (DownloadException $e) {
        }

        // We fail gracefully here by intention
        return 0;
    }

    /**
     * @param ImageLink $source
     * @param ExternalIdInterface $entity
     *
     * @return string
     * @throws DownloadException
     * @throws TypeConverterException
     */
    private function downloadFile(ImageLink $source, ExternalIdInterface $entity)
    {
        $imageContent = GeneralUtility::getUrl($source->getExternalUrl());

        // FIXME: This is a dirty hack, to get it working in TYPO3 8. Use the guzzle library instead
        if (false === $imageContent) {
            $imageContent = file_get_contents($source->getExternalUrl());
        }

        if (false === $imageContent) {
            throw new DownloadException(sprintf('The file %s could not be downloaded', $source->getExternalUrl()));
        }

        $imageInfo        = getimagesizefromstring($imageContent);
        $extension        = $this->getExtensionFromMimeType($imageInfo['mime']);
        $pathToUploadFile = $this->tempFolder . GeneralUtility::stdAuthCode($entity->getExternalId()) . '.' . $extension;

        if ($error = GeneralUtility::writeFileToTypo3tempDir($pathToUploadFile, $imageContent)) {
            throw new TypeConverterException($error, 1399312443);
        }

        return $pathToUploadFile;
    }

    /**
     * @param string $tempFilePath
     *
     * @throws TypeConverterException
     * @return FalFile
     */
    private function importResource($tempFilePath)
    {
        if (! GeneralUtility::verifyFilenameAgainstDenyPattern($tempFilePath)) {
            throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1399312430);
        }

        $uploadFolder = $this->resourceFactory->retrieveFileOrFolderObject($this->defaultUploadFolder);

        return $uploadFolder->addFile($tempFilePath, null, $this->defaultConflictMode);
    }

    /**
     * @param string $mimeType
     *
     * @return mixed
     */
    private function getExtensionFromMimeType($mimeType)
    {
        return array_search($mimeType, self::$imageMimeTypes, false);
    }

    /**
     * @param array $fileReferenceData
     */
    private function deleteOldFileReferences(array $fileReferenceData)
    {
        if (isset($fileReferenceData['uid_local'])) {
            unset($fileReferenceData['uid_local']);
        }

        $databaseConnection = $this->getDatabaseConnection();

        $where = [];
        foreach ($fileReferenceData as $key => $value) {
            $where[] = $key . '=' . $databaseConnection->fullQuoteStr($value, 'sys_file_reference');
        }
        $databaseConnection->exec_DELETEquery('sys_file_reference', implode(' AND ', $where));
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @param ImageLink $source
     * @param AbstractEntity|ExternalIdInterface $entity
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TypeConverterException
     * @throws \Bzga\BzgaBeratungsstellensuche\Property\TypeConverter\Exception\DownloadException
     */
    private function getFileUid(ImageLink $source, $entity)
    {
        // First we check if we already have a file with the identifier in the database
        $where = 'external_identifier = ' . $this->getDatabaseConnection()->fullQuoteStr(
            $source->getIdentifier(),
                'sys_file'
        );

        if ($row = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid', 'sys_file', $where)) {
            return $row['uid'];
        }

        $pathToUploadFile = $this->downloadFile($source, $entity);
        $falFile          = $this->importResource($pathToUploadFile);

        $this->getDatabaseConnection()->exec_UPDATEquery(
            'sys_file',
            'uid = ' . $falFile->getUid(),
            ['external_identifier' => $source->getIdentifier()]
        );

        return $falFile->getUid();
    }
}
