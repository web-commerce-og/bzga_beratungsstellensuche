<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Model;

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

use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class EntryTest extends UnitTestCase
{

    /**
     * @var Entry
     */
    protected $subject;

    protected function setUp()
    {
        $this->subject = new Entry();
    }

    /**
     * @test
     */
    public function getAddress()
    {
        $address = 'Zip City, Street';
        $this->subject->setCity('City');
        $this->subject->setZip('Zip');
        $this->subject->setStreet('Street');
        $this->assertEquals($address, $this->subject->getAddress());
    }

    /**
     * @test
     */
    public function getInfoWindowWithoutLink()
    {
        $this->subject->setTitle('Title');
        $this->subject->setCity('City');
        $this->subject->setZip('Zip');
        $this->subject->setStreet('Street');
        $infoWindow = '<p><strong>Title</strong><br>Street<br>Zip City</p>';
        $this->assertEquals($infoWindow, $this->subject->getInfoWindow());
    }

    /**
     * @test
     */
    public function getInfoWindowWithLink()
    {
        $this->subject->setTitle('Title');
        $this->subject->setCity('City');
        $this->subject->setZip('Zip');
        $this->subject->setStreet('Street');
        $infoWindow = '<p><strong><a href="http://domain.com">Title</a></strong><br>Street<br>Zip City</p>';
        $this->assertEquals($infoWindow, $this->subject->getInfoWindow(['detailLink' => 'http://domain.com']));
    }

    /**
     * @test
     */
    public function toString()
    {
        $this->subject->setTitle('Title');
        $this->assertEquals('Title', (string)$this->subject);
    }
}
