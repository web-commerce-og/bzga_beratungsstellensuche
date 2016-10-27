<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers;

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

use TYPO3\CMS\Fluid\ViewHelpers\TranslateViewHelper as CoreTranslateViewHelper;
use BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class TranslateViewHelper extends CoreTranslateViewHelper
{

    /**
     * Translate a given key or use the tag body as default.
     *
     * @param string $id The locallang id
     * @return string The translated key or tag body if key doesn't exist
     */
    protected function renderTranslation($id)
    {

        $registeredExtensionKeys = ExtensionManagementUtility::getRegisteredExtensionKeys();
        if (!is_array($registeredExtensionKeys) && empty($registeredExtensionKeys)) {
            return parent::renderTranslation($id);
        }

        foreach ($registeredExtensionKeys as $extensionKey) {
            $this->arguments['extensionName'] = GeneralUtility::underscoredToLowerCamelCase($extensionKey);
            $value = parent::renderTranslation($id);
            if (!empty($value)) {
                return $value;
            }
        }
    }

}