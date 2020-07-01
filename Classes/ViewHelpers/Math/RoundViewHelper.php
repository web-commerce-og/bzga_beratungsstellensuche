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
use Closure;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * @author Sebastian Schreiber
 */
class RoundViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $number = $arguments['number'];
        $precision = $arguments['precision'];
        if (null === $number) {
            $number = $renderChildrenClosure();
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
