<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Format;

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
use InvalidArgumentException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * @author Sebastian Schreiber
 */
class UppercaseFirstLetterViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $subject = $arguments['subject'];
        if (null === $subject) {
            $subject = $renderChildrenClosure();
        }
        if (! is_string($subject)) {
            throw new InvalidArgumentException('This is not a string');
        }
        $parts = explode('_', $subject);
        $subjectParts = [];
        foreach ($parts as $part) {
            $subjectParts[] = ucfirst($part[0]) . substr($part, 1);
        }

        return implode('', $subjectParts);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('subject', 'string', '', false, null);
    }
}
