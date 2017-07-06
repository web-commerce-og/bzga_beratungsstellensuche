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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\ExplodeViewHelper;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

class ExplodeViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var ExplodeViewHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(ExplodeViewHelper::class)->setMethods(['renderChildren'])->getMock();
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     */
    public function renderWithoutRemovingEmptyValues()
    {
        $this->setRenderChildrenDefaultExpectation();
        $this->assertSame(['Title', '', 'Subject'], $this->subject->render(null, ',', false, false));
    }

    /**
     * @test
     */
    public function renderWithRemovingEmptyValues()
    {
        $this->setRenderChildrenDefaultExpectation();
        $this->assertSame(['Title', 'Subject'], $this->subject->render(null, ',', true, false));
    }

    /**
     * @test
     */
    public function renderWithRemovingEmptyValuesAndSettingsKeysAsValues()
    {
        $this->setRenderChildrenDefaultExpectation();
        $this->assertSame(['Title' => 'Title', 'Subject' => 'Subject'], $this->subject->render(null, ',', true, true));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function renderWithWrongSubjectType()
    {
        $this->subject->render(new \stdClass());
    }

    private function setRenderChildrenDefaultExpectation()
    {
        $subject = 'Title,,Subject';
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($subject);
    }
}
