<?php declare(strict_types = 1);

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
use Closure;
use InvalidArgumentException;
use Traversable;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * @author Sebastian Schreiber
 */
class ImplodeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public static function renderStatic(array $arguments, Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $pieces = $arguments['pieces'];
        $glue = $arguments['glue'];
        if (null === $pieces) {
            $pieces = $renderChildrenClosure();
        }
        if (! is_array($pieces) && ! $pieces instanceof Traversable) {
            throw new InvalidArgumentException('The value is not of type array or not implementing the Traversable interface');
        }
        // This is only working with objects implementing __toString method
        if ($pieces instanceof Traversable) {
            $pieces = iterator_to_array($pieces);
            self::validatePieces($pieces);
        }
        return implode($glue, $pieces);
    }

    private static function validatePieces(array $pieces): void
    {
        foreach ($pieces as $piece) {
            if (! is_scalar($piece) && ! method_exists($piece, '__toString')) {
                throw new InvalidArgumentException('The provided value must be of type scalar or implementing the __toString method');
            }
        }
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pieces', 'mixed', '', false, null);
        $this->registerArgument('glue', 'string', '', false, ',');
    }
}
