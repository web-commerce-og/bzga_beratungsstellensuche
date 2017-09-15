<?php


namespace Bzga\BzgaBeratungsstellensuche\Utility;

/*
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

use Bzga\BzgaBeratungsstellensuche\Hooks\PageLayoutView;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\Utility\IconUtility as CoreIconUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IconUtility
{

    /**
     * @var IconFactory|null
     */
    private $iconFactory;

    /**
     * IconUtility constructor.
     */
    public function __construct()
    {
        if (class_exists(IconFactory::class)) {
            $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        }
    }

    /**
     * @param string $table
     * @param array $record
     * @return string
     */
    public function getIconForRecord($table, $record)
    {
        if ($this->iconFactory instanceof IconFactory) {
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                    . $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render()
                    . '</span> ';
            $content = BackendUtilityCore::wrapClickMenuOnIcon($data, $table, $record['uid'], true, '',
                '+info,edit,history');

            $linkTitle = htmlspecialchars(BackendUtilityCore::getRecordTitle($table, $record));

            if ($table === 'pages') {
                $id = $record['uid'];
                $currentPageId = (int)GeneralUtility::_GET('id');
                $link = htmlspecialchars($this->getEditLink($record, $currentPageId));
                $switchLabel = $this->getLanguageService()->sL(PageLayoutView::LLPATH . 'pagemodule.switchToPage');
                $content .= ' <a href="#" data-toggle="tooltip" data-placement="top" data-title="' . $switchLabel . '" onclick=\'top.jump("' . $link . '", "web_layout", "web", ' . $id . ');return false\'>' . $linkTitle . '</a>';
            } else {
                $content .= $linkTitle;
            }
        } else {
            $data = CoreIconUtility::getSpriteIconForRecord($table, $record)
                    . htmlspecialchars(BackendUtilityCore::getRecordTitle($table, $record));
            $content = $this->getDocumentTemplate()->wrapClickMenuOnIcon($data, $table, $record['uid'], true, '',
                '+info,edit');
        }

        return $content;
    }

    /**
     * Build a backend edit link based on given record.
     *
     * @param array $row Current record row from database.
     * @param int $currentPageUid current page uid
     * @return string Link to open an edit window for record.
     * @see \TYPO3\CMS\Backend\Utility\BackendUtilityCore::readPageAccess()
     */
    protected function getEditLink($row, $currentPageUid)
    {
        $editLink = '';
        $localCalcPerms = $GLOBALS['BE_USER']->calcPerms(BackendUtilityCore::getRecord('pages', $row['uid']));
        $permsEdit = $localCalcPerms & Permission::PAGE_EDIT;
        if ($permsEdit) {
            $returnUrl = BackendUtilityCore::getModuleUrl('web_layout', ['id' => $currentPageUid]);
            $editLink = BackendUtilityCore::getModuleUrl('web_layout', [
                'id' => $row['uid'],
                'returnUrl' => $returnUrl
            ]);
        }
        return $editLink;
    }

    /**
     * Return language service instance
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    public function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * Get the DocumentTemplate
     *
     * @return DocumentTemplate
     */
    private function getDocumentTemplate()
    {
        return $GLOBALS['SOBE']->doc;
    }
}
