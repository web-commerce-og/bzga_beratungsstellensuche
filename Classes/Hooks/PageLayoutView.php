<?php

namespace BZgA\BzgaBeratungsstellensuche\Hooks;


use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use BZgA\BzgaBeratungsstellensuche\Utility\TemplateLayout;

class PageLayoutView
{

    /**
     * Extension key
     *
     * @var string
     */
    const KEY = 'bzgaberatungsstellensuche';

    /**
     * Path to the locallang file
     *
     * @var string
     */
    const LLPATH = 'LLL:EXT:bzga_beratungsstellensuche/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Table information
     *
     * @var array
     */
    public $tableData = array();

    /**
     * Flexform information
     *
     * @var array
     */
    public $flexformData = array();

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Utility\TemplateLayout
     */
    protected $templateLayoutsUtility;

    /**
     * PageLayoutView constructor.
     */
    public function __construct()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
        $this->templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
    }

    /**
     * Returns information about this extension's pi1 plugin
     *
     * @param array $params Parameters to the hook
     * @return string Information about pi1 plugin
     */
    public function getExtensionSummary(array $params)
    {
        $actionTranslationKey = '';

        $result = '<strong>'.$this->getLanguageService()->sL(self::LLPATH.'pi1_title', true).'</strong><br>';

        if ($params['row']['list_type'] == self::KEY.'_pi1') {
            $this->flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

            // if flexform data is found
            $actions = $this->getFieldFromFlexform('switchableControllerActions');
            if (!empty($actions)) {
                $actionList = GeneralUtility::trimExplode(';', $actions);

                // translate the first action into its translation
                $actionTranslationKey = strtolower(str_replace('->', '_', $actionList[0]));
                $actionTranslation = $this->getLanguageService()->sL(self::LLPATH.'flexforms_general.mode.'.$actionTranslationKey);

                $result .= $actionTranslation;

            } else {
                $result .= $this->getLanguageService()->sL(self::LLPATH.'flexforms_general.mode.not_configured');
            }
            $result .= '<hr>';
            if (is_array($this->flexformData)) {

                switch ($actionTranslationKey) {
                    case 'entry_list':
                        $this->getStartingPoint();
                        $this->getDetailPidSetting();
                        $this->getListPidSetting();
                        $this->getFormFieldsSetting();
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

                if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['BZgA\\BzgaBeratungsstellensuche\\Hooks\\PageLayoutView']['extensionSummary'])) {
                    $params = array(
                        'action' => $actionTranslationKey,
                    );
                    foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['BZgA\\BzgaBeratungsstellensuche\\Hooks\\PageLayoutView']['extensionSummary'] as $reference) {
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


    /**
     * Render single settings
     *
     * @return void
     */
    private function getDetailPidSetting()
    {
        $detailPid = (int)$this->getFieldFromFlexform('settings.singlePid', 'additional');

        if ($detailPid > 0) {
            $content = $this->getPageRecordData($detailPid);

            $this->tableData[] = array(
                $this->getLanguageService()->sL(self::LLPATH.'flexforms_additional.singlePid'),
                $content,
            );
        }
    }

    /**
     * @return void
     */
    private function getFormFieldsSetting()
    {
        $formFields = $this->getFieldFromFlexform('settings.formFields', 'additional');;
        if ($formFields) {
            $formFieldsArray = GeneralUtility::trimExplode(',', $formFields);
            $formFieldsLabels = array();
            foreach ($formFieldsArray as $formField) {
                $formFieldsLabels[] = $this->getLanguageService()->sL(self::LLPATH.'flexforms_additional.formFields.'.$formField);
            }
            $this->tableData[] = array(
                $this->getLanguageService()->sL(self::LLPATH.'flexforms_additional.formFields'),
                implode(',', $formFieldsLabels),
            );
        }
    }

    /**
     * Render listPid settings
     *
     * @return void
     */
    private function getListPidSetting()
    {
        $listPid = (int)$this->getFieldFromFlexform('settings.listPid', 'additional');

        if ($listPid > 0) {
            $content = $this->getPageRecordData($listPid);

            $this->tableData[] = array(
                $this->getLanguageService()->sL(self::LLPATH.'flexforms_additional.listPid'),
                $content,
            );
        }
    }

    /**
     * Render listPid settings
     *
     * @return void
     */
    private function getBackPidSetting()
    {
        $listPid = (int)$this->getFieldFromFlexform('settings.backPid', 'additional');

        if ($listPid > 0) {
            $content = $this->getPageRecordData($listPid);

            $this->tableData[] = array(
                $this->getLanguageService()->sL(self::LLPATH.'flexforms_additional.backPid'),
                $content,
            );
        }
    }

    /**
     * Get the rendered page title including onclick menu
     *
     * @param $detailPid
     * @return string
     */
    private function getPageRecordData($detailPid)
    {
        $pageRecord = BackendUtilityCore::getRecord('pages', $detailPid);

        if (is_array($pageRecord)) {
            $data = IconUtility::getSpriteIconForRecord('pages', $pageRecord,
                    array('title' => 'Uid: '.$pageRecord['uid']))
                .htmlspecialchars(BackendUtilityCore::getRecordTitle('pages', $pageRecord));
            $content = $this->getDocumentTemplate()->wrapClickMenuOnIcon($data, 'pages', $pageRecord['uid'], true, '',
                '+info,edit');
        } else {
            $text = sprintf($this->getLanguageService()->sL(self::LLPATH.'pagemodule.pageNotAvailable', true),
                $detailPid);
            $message = GeneralUtility::makeInstance(FlashMessage::class, $text, '',
                FlashMessage::WARNING);
            /** @var $message FlashMessage */
            $content = $message->render();
        }

        return $content;
    }

    /**
     * Render template layout configuration
     *
     * @param int $pageUid
     * @return void
     */
    private function getTemplateLayoutSettings($pageUid)
    {
        $title = '';
        $field = $this->getFieldFromFlexform('settings.templateLayout', 'template');

        // Find correct title by looping over all options
        if (!empty($field)) {

            foreach ($this->templateLayoutsUtility->getAvailableTemplateLayouts($pageUid) as $layout) {
                if ($layout[1] === $field) {
                    $title = $layout[0];
                }
            }
        }

        if (!empty($title)) {
            $this->tableData[] = array(
                $this->getLanguageService()->sL(self::LLPATH.'flexforms_template.templateLayout'),
                $this->getLanguageService()->sL($title),
            );
        }
    }

    /**
     * Get the startingpoint
     *
     * @return void
     */
    private function getStartingPoint()
    {
        $value = $this->getFieldFromFlexform('settings.startingpoint');

        if (!empty($value)) {
            $pagesOut = array();
            $rawPagesRecords = $this->databaseConnection->exec_SELECTgetRows(
                '*',
                'pages',
                'deleted=0 AND uid IN('.implode(',', GeneralUtility::intExplode(',', $value, true)).')'
            );

            foreach ($rawPagesRecords as $page) {
                $pagesOut[] = htmlspecialchars(BackendUtilityCore::getRecordTitle('pages',
                        $page)).' ('.$page['uid'].')';
            }

            $recursiveLevel = (int)$this->getFieldFromFlexform('settings.recursive');
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:cms/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:cms/locallang_ttc.xlf:recursive.I.'.$recursiveLevel);
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />'.
                    $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.recursive', true).' '.
                    $recursiveLevelText;
            }

            $this->tableData[] = array(
                $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.php:LGL.startingpoint'),
                implode(', ', $pagesOut).$recursiveLevelText,
            );
        }
    }

    /**
     * Render the settings as table for Web>Page module
     * System settings are displayed in mono font
     *
     * @return string
     */
    private function renderSettingsAsTable()
    {
        if (count($this->tableData) == 0) {
            return '';
        }

        $content = '';
        foreach ($this->tableData as $line) {
            $content .= '<strong>'.$line[0].'</strong>'.' '.$line[1].'<br />';
        }

        return '<pre style="white-space:normal">'.$content.'</pre>';
    }

    /**
     * Get field value from flexform configuration,
     * including checks if flexform configuration is available
     *
     * @param string $key name of the key
     * @param string $sheet name of the sheet
     * @return string|NULL if nothing found, value if found
     */
    private function getFieldFromFlexform($key, $sheet = 'sDEF')
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

    /**
     * Return language service instance
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    private function getLanguageService()
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