<?php declare(strict_types = 1);

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
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

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
        $this->setArgumentsUnderTest($this->subject, [
            'subject' => null,
            'glue' => ',',
            'removeEmptyValues' => false,
            'valuesAsKeys' => false,
        ]);
        $this->assertSame(['Title', '', 'Subject'], $this->subject->render());
    }

    /**
     * @test
     */
    public function renderWithRemovingEmptyValues()
    {
        $this->setRenderChildrenDefaultExpectation();
        $this->setArgumentsUnderTest($this->subject, [
            'subject' => null,
            'glue' => ',',
            'removeEmptyValues' => true,
            'valuesAsKeys' => false,
        ]);
        $this->assertSame(['Title', 'Subject'], $this->subject->render());
    }

    /**
     * @test
     */
    public function renderWithRemovingEmptyValuesAndSettingsKeysAsValues()
    {
        $this->setRenderChildrenDefaultExpectation();
        $this->setArgumentsUnderTest($this->subject, [
            'subject' => null,
            'glue' => ',',
            'removeEmptyValues' => true,
            'valuesAsKeys' => true,
        ]);
        $this->assertSame(['Title' => 'Title', 'Subject' => 'Subject'], $this->subject->render());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function renderWithWrongSubjectType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->setArgumentsUnderTest($this->subject, ['subject' => new \stdClass()]);
        $this->subject->render();
    }

    private function setRenderChildrenDefaultExpectation()
    {
        $subject = 'Title,,Subject';
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($subject);
    }
}
