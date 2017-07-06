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
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

class UppercaseFirstLetterViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var UppercaseFirstLetterViewHelper|\PHPUnit_Framework_MockObject_MockObject
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
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($input);
        $this->assertEquals($expected, $this->subject->render(null));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function renderThrowsInvalidArgumentException()
    {
        $this->subject->render(new \stdClass());
    }

    /**
     * @return array
     */
    public function validValuesProvider()
    {
        return [
            ['string', 'String'],
            ['extension_key_with', 'ExtensionKeyWith']
        ];
    }

}
