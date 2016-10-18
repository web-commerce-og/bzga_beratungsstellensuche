<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Format;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class UppercaseFirstLetterViewHelper extends AbstractViewHelper
{

    /**
     * @param string $subject
     */
    public function render($subject = null)
    {
        if(null === $subject) {
            $subject = $this->renderChildren();
        }

        if (!is_string($subject)) {
            throw new \InvalidArgumentException('This is not a string');
        }

        return ucfirst(GeneralUtility::underscoredToLowerCamelCase($subject));
    }

}