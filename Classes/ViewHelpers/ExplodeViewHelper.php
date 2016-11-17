<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class ExplodeViewHelper extends AbstractViewHelper
{

    /**
     * @param string|null $subject
     * @param string $glue
     * @param bool $removeEmptyValues
     * @param bool $valuesAsKeys
     * @return array
     */
    public function render($subject = null, $glue = ',', $removeEmptyValues = true, $valuesAsKeys = true)
    {
        if (null === $subject) {
            $subject = $this->renderChildren();
        }
        $array = GeneralUtility::trimExplode($glue, $subject, $removeEmptyValues);
        if (true === $valuesAsKeys) {
            $array = array_combine($array, $array);
        }

        return $array;
    }
}
