<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\ViewHelpers;

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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\TitleViewHelper;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class TitleViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var EnvironmentService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $environmentService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TypoScriptFrontendController
     */
    protected $typoscriptFrontendController;

    /**
     * @var ExtensionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensionService;

    /**
     * @var PageRenderer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageRenderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TitleViewHelper
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->typoscriptFrontendController = $this->getMockBuilder(TypoScriptFrontendController::class)->disableOriginalConstructor()->getMock();
        $this->extensionService             = $this->getMockBuilder(ExtensionService::class)->getMock();
        $this->environmentService           = $this->getMockBuilder(EnvironmentService::class)->getMock();
        $this->pageRenderer           = $this->getMockBuilder(PageRenderer::class)->getMock();

        $this->subject = $this->getAccessibleMock(TitleViewHelper::class, ['renderChildren', 'getTyposcriptFrontendController']);
        $this->subject->method('getTyposcriptFrontendController')->willReturn($this->typoscriptFrontendController);
        $this->subject->initializeArguments();
        $this->injectDependenciesIntoViewHelper($this->subject);
        $this->inject($this->subject, 'pageRenderer', $this->pageRenderer);
        $this->inject($this->subject, 'extensionService', $this->extensionService);
        $this->inject($this->subject, 'environmentService', $this->environmentService);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function renderThrowsException()
    {
        $this->environmentService->expects($this->once())->method('isEnvironmentInBackendMode')->willReturn(true);
        $this->subject->render();
    }

    /**
     * @test
     */
    public function renderWithTitleFromArgumentsForNonCacheableAction()
    {
        $title                                       = 'Detail title';
        $this->typoscriptFrontendController->content = '<title>Some title</title>';
        $this->extensionService->expects($this->once())->method('isActionCacheable')->willReturn(false);
        $this->subject->setArguments(['title' => $title]);
        $this->subject->render();
        $this->assertContains($title, $this->typoscriptFrontendController->content);
    }

    /**
     * @test
     */
    public function renderWithTitleFromArgumentsForCacheableActionAndIndexDocTitle()
    {
        $title = 'Detail title';
        $this->extensionService->expects($this->once())->method('isActionCacheable')->willReturn(true);
        /** @var PageRenderer|\PHPUnit_Framework_MockObject_MockObject $pageRenderer */
        $this->pageRenderer->expects($this->once())->method('setTitle')->with('Detail title');
        $this->subject->setArguments(['title' => $title, 'setIndexedDocTitle' => true]);
        $this->subject->render();
        $this->assertEquals($title, $this->typoscriptFrontendController->indexedDocTitle);
    }
}
