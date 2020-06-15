<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\ViewHelpers\Math;

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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\Math\RoundViewHelper;
use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;

class RoundViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RoundViewHelper
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(RoundViewHelper::class)->setMethods(['renderChildren'])->getMock();
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     * @dataProvider validInputValues
     */
    public function renderWithRenderChildrenValue($input, $expected, $precision)
    {
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($input);
        $this->subject->setArguments(['number' => null, 'precision' => $precision]);
        $this->assertEquals($expected, $this->subject->render());
    }

    /**
     * @return array
     */
    public function validInputValues()
    {
        return [
            [3.4, 3.4, 2],
            [3.6, 4, 0],
            [1.95583, 1.956, 3],
        ];
    }
}
