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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\HeaderDataViewHelper;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

class HeaderDataViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var PageRenderer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageRenderer;

    /**
     * @var HeaderDataViewHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(HeaderDataViewHelper::class, ['renderChildren']);
        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)->disableOriginalConstructor()->getMock();
        $this->inject($this->subject, 'pageRenderer', $this->pageRenderer);
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     */
    public function render()
    {
        $this->subject->expects($this->once())->method('renderChildren')->willReturn('<script src="some.js"></script>');
        $this->pageRenderer->expects($this->once())->method('addHeaderData');
        $this->subject->render();
    }
}
