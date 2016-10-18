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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class ImplodeViewHelper extends AbstractViewHelper
{

    /**
     * @param null|array|\Traversable $pieces
     * @param string $glue
     * @return string
     */
    public function render($pieces = null, $glue = ',')
    {
        if (null === $pieces) {
            $pieces = $this->renderChildren();
        }

        if ($pieces instanceof \Traversable) {
            $pieces = iterator_to_array($pieces);
        }
        return implode($glue, $pieces);
    }

}