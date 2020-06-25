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
use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class ExplodeViewHelper extends AbstractViewHelper
{

    /**
     * @return array
     */
    public function render()
    {
        $subject = $this->arguments['subject'];
        $glue = $this->arguments['glue'];
        $removeEmptyValues = $this->arguments['removeEmptyValues'];
        $valuesAsKeys = $this->arguments['valuesAsKeys'];
        if (null === $subject) {
            $subject = $this->renderChildren();
        }
        if (!is_scalar($subject)) {
            throw new InvalidArgumentException('The provided value must be of type string');
        }
        $array = GeneralUtility::trimExplode($glue, $subject, $removeEmptyValues);
        if (true === $valuesAsKeys) {
            $array = array_combine($array, $array);
        }
        return $array;
    }

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('subject', 'string|null', '', false, null);
        $this->registerArgument('glue', 'string', '', false, ',');
        $this->registerArgument('removeEmptyValues', 'bool', '', false, null);
        $this->registerArgument('valuesAsKeys', 'bool', '', false, null);
    }
}
