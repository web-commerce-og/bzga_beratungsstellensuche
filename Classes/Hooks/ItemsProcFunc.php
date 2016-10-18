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
use BZgA\BzgaBeratungsstellensuche\Utility\TemplateLayout;
use BZgA\BzgaBeratungsstellensuche\Utility\FormFields;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class ItemsProcFunc
{

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     * @return void
     */
    public function user_templateLayout(array &$config)
    {
        $templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
        /** @var TemplateLayout $templateLayoutsUtility */
        $templateLayouts = $templateLayoutsUtility->getAvailableTemplateLayouts($config['row']['pid']);
        foreach ($templateLayouts as $layout) {
            $additionalLayout = array(
                $GLOBALS['LANG']->sL($layout[0], true),
                $layout[1],
            );
            array_push($config['items'], $additionalLayout);
        }
    }

    /**
     * @param array $config
     * @return void
     */
    public function user_formFields(array &$config)
    {
        $formFieldsUtility = GeneralUtility::makeInstance(FormFields::class);
        /** @var FormFields $formFieldsUtility */
        $formFields = $formFieldsUtility->getAvailableFormFields();
        foreach ($formFields as $formField) {
            $additionalFormField = array(
                $GLOBALS['LANG']->sL($formField[0], true),
                $formField[1],
            );
            array_push($config['items'], $additionalFormField);
        }
    }

}