<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HeaderDataViewHelper extends AbstractViewHelper
{

    /**
     * Renders HeaderData
     *
     * @return void
     */
    public function render()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        /* @var $pageRenderer PageRenderer */
        $pageRenderer->addHeaderData($this->renderChildren());
    }

}