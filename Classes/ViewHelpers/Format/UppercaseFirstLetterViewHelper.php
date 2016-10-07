<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Format;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

        return ucfirst($subject);
    }

}