<?php


namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional\ViewHelpers;

/*
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

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\Rendering\RenderingContextFixture;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Variables\CmsVariableProvider;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Arguments;
use TYPO3\CMS\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3\CMS\Fluid\Core\ViewHelper\TemplateVariableContainer;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Base test class for testing view helpers
 */
abstract class AbstractViewHelperBaseTestcase extends FunctionalTestCase
{
    /**
     * @var ViewHelperVariableContainer|ObjectProphecy
     */
    protected $viewHelperVariableContainer;

    /**
     * @var CmsVariableProvider|TemplateVariableContainer
     */
    protected $templateVariableContainer;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * @var TagBuilder
     */
    protected $tagBuilder;

    /**
     * @var Arguments
     */
    protected $arguments;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RenderingContextFixture
     */
    protected $renderingContext;

    /**
     * @var MvcPropertyMappingConfigurationService
     */
    protected $mvcPropertyMapperConfigurationService;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->viewHelperVariableContainer = $this->prophesize('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\ViewHelperVariableContainer');
        $this->uriBuilder = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Routing\\UriBuilder');
        $this->uriBuilder->expects($this->any())->method('reset')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setArguments')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setSection')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setFormat')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setCreateAbsoluteUri')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setAddQueryString')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setArgumentsToBeExcludedFromQueryString')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setLinkAccessRestrictedPages')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setTargetPageUid')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setTargetPageType')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setNoCache')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setUseCacheHash')->will($this->returnValue($this->uriBuilder));
        $this->uriBuilder->expects($this->any())->method('setAddQueryStringMethod')->will($this->returnValue($this->uriBuilder));
        $this->request = $this->prophesize('TYPO3\\CMS\\Extbase\\Mvc\\Web\\Request');
        $this->controllerContext = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ControllerContext');
        $this->controllerContext->expects($this->any())->method('getUriBuilder')->will($this->returnValue($this->uriBuilder));
        $this->controllerContext->expects($this->any())->method('getRequest')->will($this->returnValue($this->request->reveal()));
        $this->arguments = [];

        if (class_exists('TYPO3\\CMS\\Fluid\\Core\\Variables\\CmsVariableProvider')) {
            $this->templateVariableContainer = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Variables\\CmsVariableProvider');
            $this->tagBuilder = new TagBuilder();
        } else {
            $this->templateVariableContainer = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TemplateVariableContainer');
            $this->tagBuilder = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\ViewHelper\\TagBuilder');
        }

        $this->renderingContext = $this->getAccessibleMock('Nimut\\TestingFramework\\Rendering\\RenderingContextFixture', ['getControllerContext']);
        $this->renderingContext->expects($this->any())->method('getControllerContext')->willReturn($this->controllerContext);
        if (is_callable([$this->renderingContext, 'setVariableProvider'])) {
            $this->renderingContext->setVariableProvider($this->templateVariableContainer);
        } else {
            $this->renderingContext->injectTemplateVariableContainer($this->templateVariableContainer);
        }
        $this->renderingContext->_set('viewHelperVariableContainer', $this->viewHelperVariableContainer->reveal());
        $this->renderingContext->setControllerContext($this->controllerContext);
        $this->mvcPropertyMapperConfigurationService = $this->getAccessibleMock('TYPO3\\CMS\\Extbase\\Mvc\\Controller\\MvcPropertyMappingConfigurationService', ['dummy']);
    }

    /**
     * @param ViewHelperInterface|AbstractViewHelper $viewHelper
     * @return void
     */
    protected function injectDependenciesIntoViewHelper($viewHelper)
    {
        if (!$viewHelper instanceof ViewHelperInterface && !$viewHelper instanceof AbstractViewHelper) {
            throw new \RuntimeException(
                'Invalid viewHelper type "' . get_class($viewHelper) . '" in injectDependenciesIntoViewHelper',
                1487208085
            );
        }
        $viewHelper->setRenderingContext($this->renderingContext);
        $viewHelper->setArguments($this->arguments);
        // this condition is needed, because the (Be)/Security\*ViewHelper don't extend the
        // AbstractViewHelper and contain no method injectReflectionService()
        if ($viewHelper instanceof AbstractViewHelper) {
            $reflectionServiceProphecy = $this->prophesize('TYPO3\\CMS\\Extbase\\Reflection\\ReflectionService');
            $viewHelper->injectReflectionService($reflectionServiceProphecy->reveal());
        }
        if ($viewHelper instanceof AbstractTagBasedViewHelper && $viewHelper instanceof AccessibleMockObjectInterface) {
            $viewHelper->_set('tag', $this->tagBuilder);
        }
    }
}
