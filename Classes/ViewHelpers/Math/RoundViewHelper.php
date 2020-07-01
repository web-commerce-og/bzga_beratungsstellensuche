<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Math;

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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class RoundViewHelper extends AbstractViewHelper
{
    public function render(): float
    {
        $number = $this->arguments['number'];
        $precision = $this->arguments['precision'];
        if (null === $number) {
            $number = $this->renderChildren();
        }
        return round($number, $precision);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('number', 'float|null', '', false, null);
        $this->registerArgument('precision', 'int', '', false, 2);
    }
}
