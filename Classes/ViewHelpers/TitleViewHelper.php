<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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
     * @var boolean
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
            return '';
        }
        if (false === empty($this->arguments['title'])) {
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
            $GLOBALS['TSFE']->getPageRenderer()->setTitle($title);
            if (true === $this->arguments['setIndexedDocTitle']) {
                $GLOBALS['TSFE']->indexedDocTitle = $title;
            }
        } else {
            $GLOBALS['TSFE']->content = preg_replace('#<title>.*<\/title>#', '<title>'.htmlentities($title).'</title>',
                $GLOBALS['TSFE']->content);
        }
    }

}