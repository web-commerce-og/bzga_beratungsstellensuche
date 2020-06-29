<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\ViewHelpers\Format;

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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\Format\UppercaseFirstLetterViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

class UppercaseFirstLetterViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UppercaseFirstLetterViewHelper
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(UppercaseFirstLetterViewHelper::class)->setMethods(['renderChildren'])->getMock();
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     * @dataProvider validValuesProvider
     */
    public function renderWithRenderChildren($input, $expected)
    {
        $this->setArgumentsUnderTest($this->subject);
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($input);
        $this->assertEquals($expected, $this->subject->render());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function renderThrowsInvalidArgumentException()
    {
        $this->setArgumentsUnderTest($this->subject, ['subject' => new \stdClass()]);
        $this->subject->render();
    }

    /**
     * @return array
     */
    public function validValuesProvider()
    {
        return [
            ['string', 'String'],
            ['motherAndChild', 'MotherAndChild'],
            ['extension_key_with', 'ExtensionKeyWith']
        ];
    }
}
