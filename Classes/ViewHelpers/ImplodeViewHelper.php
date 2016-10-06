<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImplodeViewHelper extends AbstractViewHelper
{

    /**
     * @param null|array|\Traversable $pieces
     * @param string $delimiter
     * @return string
     */
    public function render($pieces = null, $delimiter = ',')
    {
        if (null === $pieces) {
            $pieces = $this->renderChildren();
        }

        if ($pieces instanceof \Traversable) {
            $pieces = iterator_to_array($pieces);
        }

        return implode($delimiter, $pieces);
    }

}