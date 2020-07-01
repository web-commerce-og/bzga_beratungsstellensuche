<?php

namespace Bzga\BzgaBeratungsstellensuche\Hooks;

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
use Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility;
use Bzga\BzgaBeratungsstellensuche\Utility\IconUtility;
use Bzga\BzgaBeratungsstellensuche\Utility\TemplateLayout;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @author Sebastian Schreiber
 */
class PageLayoutView
{

    /**
     * Extension key
     *
     * @var string
     */
    public const KEY = 'bzgaberatungsstellensuche';

    /**
     * Path to the locallang file
     *
     * @var string
     */
    public const LLPATH = 'LLL:EXT:%s/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Table information
     *
     * @var array
     */
    public $tableData = [];

    /**
     * Flexform information
     *
     * @var array
     */
    public $flexformData = [];

    /**
     * @var TemplateLayout
     */
    protected $templateLayoutsUtility;

    /**
     * @var IconUtility
     */
    protected $iconUtility;

    /**
     * PageLayoutView constructor.
     */
    public function __construct()
    {
        $this->templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
        $this->iconUtility = GeneralUtility::makeInstance(IconUtility::class);
    }

    public function getExtensionSummary(array $params): string
    {
        $actionTranslationKey = '';

        $result = '<strong>' . $this->sL('pi1_title', true) . '</strong><br>';

        if ($params['row']['list_type'] == self::KEY . '_pi1') {
            $this->flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

            // if flexform data is found
            $actions = $this->getFieldFromFlexform('switchableControllerActions');
            if (! empty($actions)) {
                $actionList = GeneralUtility::trimExplode(';', $actions);

                // translate the first action into its translation
                $actionTranslationKey = strtolower(str_replace('->', '_', $actionList[0]));
                $actionTranslation = $this->sL('flexforms_general.mode.' . $actionTranslationKey);

                $result .= $actionTranslation;
            } else {
                $result .= $this->sL('flexforms_general.mode.not_configured');
            }
            $result .= '<hr>';
            if (is_array($this->flexformData)) {
                switch ($actionTranslationKey) {
                    case 'entry_list':
                        $this->getStartingPoint();
                        $this->getDetailPidSetting();
                        $this->getListPidSetting();
                        $this->getFormFieldsSetting();
                        $this->getListItemsPerPageSetting();
                        break;
                    case 'entry_show':
                        $this->getListPidSetting();
                        $this->getBackPidSetting();
                        $this->getDetailPidSetting();
                        break;
                    case 'entry_form':
                        $this->getListPidSetting();
                        $this->getFormFieldsSetting();
                        break;
                    default:
                }

                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche'][__CLASS__]['extensionSummary'])) {
                    $params = [
                        'action' => $actionTranslationKey,
                    ];
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche'][__CLASS__]['extensionSummary'] as $reference) {
                        GeneralUtility::callUserFunction($reference, $params, $this);
                    }
                }

                // for all views
                $this->getTemplateLayoutSettings($params['row']['pid']);

                $result .= $this->renderSettingsAsTable();
            }
        }

        return $result;
    }

    private function getDetailPidSetting(): void
    {
        $detailPid = (int)$this->getFieldFromFlexform('settings.singlePid', 'additional');

        if ($detailPid > 0) {
            $content = $this->getPageRecordData($detailPid);

            $this->tableData[] = [
                $this->sL('flexforms_additional.singlePid'),
                $content,
            ];
        }
    }

    private function getFormFieldsSetting(): void
    {
        $formFields = $this->getFieldFromFlexform('settings.formFields', 'additional');
        if ($formFields) {
            $formFieldsArray = GeneralUtility::trimExplode(',', $formFields);
            $formFieldsLabels = [];
            foreach ($formFieldsArray as $formField) {
                $formFieldsLabels[] = $this->sL('flexforms_additional.formFields.' . $formField);
            }
            $this->tableData[] = [
                $this->sL('flexforms_additional.formFields'),
                implode(',', $formFieldsLabels),
            ];
        }
    }

    private function getListPidSetting(): void
    {
        $listPid = (int)$this->getFieldFromFlexform('settings.listPid', 'additional');

        if ($listPid > 0) {
            $content = $this->getPageRecordData($listPid);

            $this->tableData[] = [
                $this->sL('flexforms_additional.listPid'),
                $content,
            ];
        }
    }

    private function getListItemsPerPageSetting(): void
    {
        $itemsPerPage = (int)$this->getFieldFromFlexform('settings.list.itemsPerPage', 'additional');

        if ($itemsPerPage > 0) {
            $this->tableData[] = [
                $this->sL('flexforms_additional.itemsPerPage'),
                $itemsPerPage,
            ];
        }
    }

    private function getBackPidSetting(): void
    {
        $listPid = (int)$this->getFieldFromFlexform('settings.backPid', 'additional');

        if ($listPid > 0) {
            $content = $this->getPageRecordData($listPid);

            $this->tableData[] = [
                $this->sL('flexforms_additional.backPid'),
                $content,
            ];
        }
    }

    private function getPageRecordData(int $detailPid): string
    {
        $pageRecord = BackendUtilityCore::getRecord('pages', $detailPid);

        if (is_array($pageRecord)) {
            $content = $this->iconUtility->getIconForRecord('pages', $pageRecord);
        } else {
            $text = sprintf(
                $this->sL('pagemodule.pageNotAvailable', true),
                $detailPid
            );
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                $text,
                '',
                FlashMessage::WARNING
            );
            /** @var $message FlashMessage */
            $content = $message->render();
        }

        return $content;
    }

    private function getTemplateLayoutSettings(int $pageUid): void
    {
        $title = '';
        $field = $this->getFieldFromFlexform('settings.templateLayout', 'template');

        // Find correct title by looping over all options
        if (! empty($field)) {
            foreach ($this->templateLayoutsUtility->getAvailableTemplateLayouts($pageUid) as $layout) {
                if ($layout[1] === $field) {
                    $title = $layout[0];
                }
            }
        }

        if (! empty($title)) {
            $this->tableData[] = [
                $this->sL('flexforms_template.templateLayout'),
                $this->sL($title),
            ];
        }
    }

    private function getStartingPoint(): void
    {
        $value = $this->getFieldFromFlexform('settings.startingpoint');

        if (! empty($value)) {
            $pagesOut = [];
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $rawPagesRecords = $queryBuilder
                ->select('*')
                ->from('pages')
                ->where($queryBuilder->expr()->in('uid', GeneralUtility::intExplode(',', $value, true)))
                ->execute()
                ->fetchAll();

            foreach ($rawPagesRecords as $page) {
                $pagesOut[] = htmlspecialchars(BackendUtilityCore::getRecordTitle(
                        'pages',
                        $page
                    )) . ' (' . $page['uid'] . ')';
            }

            $recursiveLevel = (int)$this->getFieldFromFlexform('settings.recursive');
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:cms/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:cms/locallang_ttc.xlf:recursive.I.' . $recursiveLevel);
            }

            if (! empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                                      $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.recursive', true) . ' ' .
                                      $recursiveLevelText;
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.startingpoint'),
                implode(', ', $pagesOut) . $recursiveLevelText,
            ];
        }
    }

    private function renderSettingsAsTable(): string
    {
        if (count($this->tableData) === 0) {
            return '';
        }

        $content = '';
        foreach ($this->tableData as $line) {
            $content .= '<strong>' . $line[0] . '</strong>' . ' ' . $line[1] . '<br />';
        }

        return '<pre style="white-space:normal">' . $content . '</pre>';
    }

    private function getFieldFromFlexform(string $key, string $sheet = 'sDEF')
    {
        $flexform = $this->flexformData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (is_array($flexform) && is_array($flexform[$sheet]) && is_array($flexform[$sheet]['lDEF'])
                && is_array($flexform[$sheet]['lDEF'][$key]) && isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }

    private function sL(string $label, bool $hsc = false): string
    {
        $registeredExtensionKeys = ExtensionManagementUtility::getRegisteredExtensionKeys();
        foreach ($registeredExtensionKeys as $extensionKey) {
            $fullPathToLabel = sprintf(self::LLPATH, $extensionKey) . $label;
            $translation = $this->getLanguageService()->sL($fullPathToLabel, $hsc);
            if ('' !== $translation) {
                return $translation;
            }
        }

        return '';
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
