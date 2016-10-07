<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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