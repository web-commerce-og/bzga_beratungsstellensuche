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
use RuntimeException;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @author Sebastian Schreiber
 */
class TitleViewHelper extends AbstractViewHelper
{

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var EnvironmentService
     */
    protected $environmentService;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * With this flag, you can disable the escaping interceptor inside this ViewHelper.
     * THIS MIGHT CHANGE WITHOUT NOTICE, NO PUBLIC API!
     * @var bool
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @param EnvironmentService $environmentService
     */
    public function injectEnvironmentService(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * @param ExtensionService $extensionService
     */
    public function injectExtensionService(ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * @param PageRenderer $pageRenderer
     */
    public function injectPageRenderer(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        $this->registerArgument('title', 'string', 'Title tag content');
        $this->registerArgument('setIndexedDocTitle', 'boolean', 'Set indexed doc title to title', false, false);
    }

    /**
     * Render method
     */
    public function render()
    {
        if ($this->environmentService->isEnvironmentInBackendMode()) {
            throw new RuntimeException('This method should only be called in the frontend context');
        }

        $typoscriptFrontendController = $this->getTyposcriptFrontendController();

        $title = $this->arguments['title'] ?: $this->renderChildren();

        $request = $this->renderingContext->getControllerContext()->getRequest();

        if ($this->extensionService->isActionCacheable(
            $request->getControllerExtensionName(),
            $request->getPluginName(),
            $request->getControllerName(),
            $request->getControllerActionName()
        )
        ) {
            $this->pageRenderer->setTitle($title);
            if (true === $this->arguments['setIndexedDocTitle']) {
                $typoscriptFrontendController->indexedDocTitle = $title;
            }
        } else {
            $typoscriptFrontendController->content = preg_replace(
                '#<title>.*<\/title>#s',
                '<title>' . htmlentities($title) . '</title>',
                $typoscriptFrontendController->content
            );
        }
    }

    /**
     * @return TypoScriptFrontendController
     * @codeCoverageIgnore
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
