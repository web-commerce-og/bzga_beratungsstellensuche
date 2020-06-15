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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\ImplodeViewHelper;
use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ImplodeViewHelperTest extends ViewHelperBaseTestcase
{

    /**
     * @var ImplodeViewHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(ImplodeViewHelper::class)->setMethods(['renderChildren'])->getMock();
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     * @dataProvider possibleValidValues
     */
    public function renderPossibleValues($input, $expected)
    {
        $this->subject->expects($this->once())->method('renderChildren')->willReturn($input);

        $this->setArgumentsUnderTest($this->subject);
        $this->assertEquals($expected, $this->subject->render());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @dataProvider possibleInvalidValues
     */
    public function renderThrowsException($pieces)
    {
        $this->setArgumentsUnderTest($this->subject, ['pieces' => $pieces]);
        $this->subject->render();
    }

    /**
     * @return array
     */
    public function possibleInvalidValues(): array
    {
        $objectStorage = new ObjectStorage();
        $objectStorage->attach(new \stdClass());

        return [
            [new \stdClass()],
            [$objectStorage]
        ];
    }

    /**
     * @return array
     */
    public function possibleValidValues()
    {
        $objectStorage = new ObjectStorage();
        $class = new ObjectToString(1);
        $objectStorage->attach($class);
        $class = new ObjectToString(2);
        $objectStorage->attach($class);
        $class = new ObjectToString(3);
        $objectStorage->attach($class);

        return [
            [[1, 2, 3], '1,2,3'],
            [['Title', 'Subject', 'Text'], 'Title,Subject,Text'],
            [$objectStorage, '1,2,3']
        ];
    }
}

class ObjectToString
{

    /**
     * @var string
     */
    private $title;

    /**
     * ObjectToString constructor.
     *
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->title;
    }
}
