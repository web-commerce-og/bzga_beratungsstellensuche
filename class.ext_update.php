<?php


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use SJBR\StaticInfoTables\Utility\DatabaseUpdateUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class for updating the db
 */
class ext_update
{
    /**
     * Main function, returning the HTML content
     *
     * @return string HTML
     */
    public function main()
    {
        $content = '';
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        // Update the database
        $databaseUpdateUtility = $objectManager->get(DatabaseUpdateUtility::class);
        $databaseUpdateUtility->doUpdate('bzga_beratungsstellensuche');

        $content .= '<p>'.LocalizationUtility::translate('updateLanguageLabels',
                'StaticInfoTables').' bzga_beratungsstellensuche.</p>';

        $this->createImageUploadFolder();

        return $content;
    }

    /**
     * @return void
     */
    private function createImageUploadFolder()
    {
        $imageFolder = GeneralUtility::getFileAbsFileName('fileadmin/user_upload/tx_bzgaberatungsstellensuche');
        if (false === is_dir($imageFolder)) {
            GeneralUtility::mkdir($imageFolder);
        }
    }

    /**
     * @return bool
     */
    public function access()
    {
        return true;
    }
}