<?php declare(strict_types = 1);

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

    public function __construct()
    {
        if (class_exists(IconFactory::class)) {
            $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        }
    }

    public function getIconForRecord(string $table, array $record): string
    {
        if ($this->iconFactory instanceof IconFactory) {
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                    . $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render()
                    . '</span> ';
            $content = BackendUtilityCore::wrapClickMenuOnIcon(
                $data,
                $table,
                $record['uid'],
                true,
                '',
                '+info,edit,history'
            );

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
            $content = $this->getDocumentTemplate()->wrapClickMenuOnIcon(
                $data,
                $table,
                $record['uid'],
                true,
                '',
                '+info,edit'
            );
        }

        return $content;
    }

    protected function getEditLink(array $row, int $currentPageUid): string
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

    public function getLanguageService(): \TYPO3\CMS\Core\Localization\LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getDocumentTemplate(): DocumentTemplate
    {
        return $GLOBALS['SOBE']->doc;
    }
}
