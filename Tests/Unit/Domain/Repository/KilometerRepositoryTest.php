<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Repository;

use Bzga\BzgaBeratungsstellensuche\Domain\Repository\KilometerRepository;

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

use Nimut\TestingFramework\TestCase\UnitTestCase;

class KilometerRepositoryTest extends UnitTestCase
{

    /**
     * @var KilometerRepository
     */
    protected $subject;


    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new KilometerRepository();
    }


    /**
     * @test
     */
    public function findKilometersBySettingsDefault()
    {
        $this->assertSame([10 => 10, 20 => 20, 50 => 50, 100 => 100], $this->subject->findKilometersBySettings([]));
    }

    /**
     * @test
     * @dataProvider kilometers
     */
    public function findKilometersByDefinedSettings($expected, $input)
    {
        $settings = ['form' => ['kilometers' => $input]];
        $this->assertSame($expected, $this->subject->findKilometersBySettings($settings));
    }

    /**
     * @return array
     */
    public function kilometers()
    {
        return [
            [
                [10 => 10, 20 => 20],
                '10,20',
            ],
            [
                [10 => 10, 20 => 20],
                '10, 20',
            ],
        ];
    }

}
