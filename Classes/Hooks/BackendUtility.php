<?php

namespace BZgA\BzgaBeratungsstellensuche\Hooks;

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


use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class BackendUtility
{

    /**
     * Fields which are removed in detail view
     *
     * @var array
     */
    private $removedFieldsInDetailView = array(
        'sDEF' => 'startingpoint,recursive',
        'additional' => 'listPid,list.itemsPerPage,formFields',
        'template' => '',
    );

    /**
     * Fields which are removed in list view
     *
     * @var array
     */
    private $removedFieldsInListView = array(
        'sDEF' => '',
        'additional' => '',
        'template' => '',
    );

    /**
     * Fields which are remove in form view
     *
     * @var array
     */
    private $removedFieldsInFormView = array(
        'sDEF' => 'startingpoint,recursive',
        'additional' => 'singlePid,backPid,list.itemsPerPage',
        'template' => '',
    );

    /**
     * Hook function of \TYPO3\CMS\Backend\Utility\BackendUtility
     * It is used to change the flexform if it is about news
     *
     * @param array &$dataStructure Flexform structure
     * @param array $conf some strange configuration
     * @param array $row row of current record
     * @param string $table table name
     * @return void
     */
    public function getFlexFormDS_postProcessDS(&$dataStructure, $conf, $row, $table)
    {
        if ($table === 'tt_content' && $row['list_type'] === 'bzgaberatungsstellensuche_pi1' && is_array($dataStructure)) {
            $this->updateFlexforms($dataStructure, $row);
        }
    }

    /**
     * Update flexform configuration if a action is selected
     *
     * @param array|string &$dataStructure flexform structure
     * @param array $row row of current record
     * @return void
     */
    private function updateFlexforms(array &$dataStructure, array $row)
    {
        $selectedView = '';

        // get the first selected action
        if (is_string($row['pi_flexform'])) {
            $flexformSelection = GeneralUtility::xml2array($row['pi_flexform']);
        } else {
            $flexformSelection = $row['pi_flexform'];
        }
        if (is_array($flexformSelection) && is_array($flexformSelection['data'])) {
            $selectedView = $flexformSelection['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'];
            if (!empty($selectedView)) {
                $actionParts = GeneralUtility::trimExplode(';', $selectedView, true);
                $selectedView = $actionParts[0];
            }

            // new plugin element
        } elseif (GeneralUtility::isFirstPartOfStr($row['uid'], 'NEW')) {
            // use List as starting view
            $selectedView = 'Entry->list;Entry->show';
        }

        if (!empty($selectedView)) {
            // Modify the flexform structure depending on the first found action
            switch ($selectedView) {
                case 'Entry->list;Entry->show':
                    $this->deleteFromStructure($dataStructure, $this->removedFieldsInListView);
                    break;
                case 'Entry->show':
                    $this->deleteFromStructure($dataStructure, $this->removedFieldsInDetailView);
                    break;
                case 'Entry->form':
                    $this->deleteFromStructure($dataStructure, $this->removedFieldsInFormView);
                    break;
                default:
            }

            if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Hooks/BackendUtility.php']['updateFlexforms'])) {
                $params = array(
                    'selectedView' => $selectedView,
                    'dataStructure' => &$dataStructure,
                );
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Hooks/BackendUtility.php']['updateFlexforms'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }
        }
    }

    /**
     * Remove fields from flexform structure
     *
     * @param array &$dataStructure flexform structure
     * @param array $fieldsToBeRemoved fields which need to be removed
     * @return void
     */
    private function deleteFromStructure(array &$dataStructure, array $fieldsToBeRemoved)
    {
        foreach ($fieldsToBeRemoved as $sheetName => $sheetFields) {
            $fieldsInSheet = GeneralUtility::trimExplode(',', $sheetFields, true);

            foreach ($fieldsInSheet as $fieldName) {
                unset($dataStructure['sheets'][$sheetName]['ROOT']['el']['settings.'.$fieldName]);
            }
        }
    }
}