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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @author Sebastian Schreiber
 */
class TitleViewHelper extends AbstractViewHelper
{

    /**
     *
     * @var \TYPO3\CMS\Extbase\Service\ExtensionService
     * @inject
     */
    protected $extensionService;

    /**
     * With this flag, you can disable the escaping interceptor inside this ViewHelper.
     * THIS MIGHT CHANGE WITHOUT NOTICE, NO PUBLIC API!
     * @var bool
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('title', 'string', 'Title tag content');
        $this->registerArgument('whitespaceString', 'string',
            'String used to replace groups of white space characters, one replacement inserted per group', false, ' ');
        $this->registerArgument('setIndexedDocTitle', 'boolean', 'Set indexed doc title to title', false, false);
    }

    /**
     * Render method
     *
     * @return void
     */
    public function render()
    {
        if ('BE' === TYPO3_MODE) {
            throw new \RuntimeException('This method should only be called in the frontend context');
        }

        $typoscriptFrontendController = $this->getTyposcriptFrontendController();
        if ($this->arguments['title']) {
            $title = $this->arguments['title'];
        } else {
            $title = $this->renderChildren();
        }
        $title = trim(preg_replace('/\s+/', $this->arguments['whitespaceString'], $title),
            $this->arguments['whitespaceString']);
        if ($this->extensionService->isActionCacheable($this->controllerContext->getRequest()->getControllerExtensionName(),
            $this->controllerContext->getRequest()->getPluginName(),
            $this->controllerContext->getRequest()->getControllerName(),
            $this->controllerContext->getRequest()->getControllerActionName())
        ) {
            $typoscriptFrontendController->getPageRenderer()->setTitle($title);
            if (true === $this->arguments['setIndexedDocTitle']) {
                $typoscriptFrontendController->indexedDocTitle = $title;
            }
        } else {
            $typoscriptFrontendController->content = preg_replace('#<title>.*<\/title>#',
                '<title>' . htmlentities($title) . '</title>',
                $typoscriptFrontendController->content);
        }
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    private function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
