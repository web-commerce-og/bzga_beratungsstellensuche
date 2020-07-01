<?php declare(strict_types = 1);

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
class BackendUtility
{

    /**
     * Fields which are removed in detail view
     *
     * @var array
     */
    private $removedFieldsInDetailView = [
        'sDEF' => 'startingpoint,recursive',
        'additional' => 'listPid,list.itemsPerPage,formFields',
        'template' => '',
    ];

    /**
     * Fields which are removed in list view
     *
     * @var array
     */
    private $removedFieldsInListView = [
        'sDEF' => '',
        'additional' => '',
        'template' => '',
    ];

    /**
     * Fields which are remove in form view
     *
     * @var array
     */
    private $removedFieldsInFormView = [
        'sDEF' => 'startingpoint,recursive',
        'additional' => 'singlePid,backPid,list.itemsPerPage',
        'template' => '',
    ];

    public function getFlexFormDS_postProcessDS(array &$dataStructure, array $conf, array $row, string $table): void
    {
        if ($table === 'tt_content' && $row['list_type'] === 'bzgaberatungsstellensuche_pi1' && is_array($dataStructure)) {
            $this->updateFlexforms($dataStructure, $row);
        }
    }

    private function updateFlexforms(array &$dataStructure, array $row): void
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
                $params = [
                    'selectedView' => $selectedView,
                    'dataStructure' => &$dataStructure,
                ];
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Hooks/BackendUtility.php']['updateFlexforms'] as $reference) {
                    GeneralUtility::callUserFunction($reference, $params, $this);
                }
            }
        }
    }

    private function deleteFromStructure(array &$dataStructure, array $fieldsToBeRemoved): void
    {
        foreach ($fieldsToBeRemoved as $sheetName => $sheetFields) {
            $fieldsInSheet = GeneralUtility::trimExplode(',', $sheetFields, true);

            foreach ($fieldsInSheet as $fieldName) {
                unset($dataStructure['sheets'][$sheetName]['ROOT']['el']['settings.' . $fieldName]);
            }
        }
    }
}
