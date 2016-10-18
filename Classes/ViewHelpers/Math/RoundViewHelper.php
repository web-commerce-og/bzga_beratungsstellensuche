<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Math;

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

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class RoundViewHelper extends AbstractViewHelper
{

    /**
     * @param float|null $number
     * @param int $precision
     * @return float
     */
    public function render($number = null, $precision = 2)
    {
        if (null === $number) {
            $number = $this->renderChildren();
        }

        return round($number, $precision);
    }

}