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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    public function injectEnvironmentService(EnvironmentService $environmentService): void
    {
        $this->environmentService = $environmentService;
    }

    public function injectExtensionService(ExtensionService $extensionService): void
    {
        $this->extensionService = $extensionService;
    }

    public function injectPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('title', 'string', 'Title tag content');
        $this->registerArgument('setIndexedDocTitle', 'boolean', 'Set indexed doc title to title', false, false);
    }

    public function render(): void
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
     * @codeCoverageIgnore
     */
    protected function getTyposcriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
