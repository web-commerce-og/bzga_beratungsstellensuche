<?php


namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer;

/**
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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Category;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\GetSetMethodNormalizer;
use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @author Sebastian Schreiber
 */
class SerializerTest extends UnitTestCase
{
    /**
     * @var Serializer
     */
    protected $subject;

    /**
     * @var CategoryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepository;

    /**
     * @var CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @var EntryNormalizer
     */
    protected $entryNormalizer;

    /**
     * @var Dispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $signalSlotDispatcher;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->entryNormalizer = new EntryNormalizer(null);

        $this->signalSlotDispatcher = $this->getMock(Dispatcher::class);
        $this->countryZoneRepository = $this->getMock(CountryZoneRepository::class, ['findOneByExternalId'],
            [], '', false);
        $this->categoryRepository = $this->getMock(CategoryRepository::class, ['findOneByExternalId'], [], '',
            false);
        $this->inject($this->entryNormalizer, 'signalSlotDispatcher', $this->signalSlotDispatcher);
        $this->inject($this->entryNormalizer, 'categoryRepository', $this->categoryRepository);
        $this->inject($this->entryNormalizer, 'countryZoneRepository', $this->countryZoneRepository);
        $normalizers = [
            $this->entryNormalizer,
            new GetSetMethodNormalizer()
        ];
        $this->subject = new Serializer($normalizers);
    }

    /**
     * @test
     */
    public function deserializeCategoryFromXml()
    {
        $xml = '<beratungsart><index>1</index><sort>2</sort>persönliche Beratung</beratungsart>';
        $object = $this->subject->deserialize($xml, Category::class, 'xml');
        /* @var $object Category */
        self::assertSame('persönliche Beratung', $object->getTitle());
        self::assertSame(1, (integer)$object->getExternalId());
    }

    /**
     * @test
     * @dataProvider xmlProvider
     */
    public function deserializeEntryFromXml($xml)
    {
        $categoryMock = $this->getMock(Category::class);
        $this->categoryRepository->expects($this->any())->method('findOneByExternalId')->willReturn($categoryMock);

        $countryZoneMock = $this->getMock(CountryZone::class);
        $this->countryZoneRepository->expects($this->any())->method('findOneByExternalId')->willReturn($countryZoneMock);

        $object = $this->subject->deserialize($xml, Entry::class, 'xml');
        /* @var $object Entry */
        self::assertSame('Gesundheitsamt Uelzen, Lüchow-Dannenberg, Schwangerschaftskonfliktberatungsstelle',
            $object->getTitle());
        self::assertSame(1858, (integer)$object->getExternalId());
    }

    /**
     * @return array
     */
    public function xmlProvider()
    {
        $xml = '<entry>
            <index>1858</index>
            <titel>Gesundheitsamt Uelzen, Lüchow-Dannenberg, Schwangerschaftskonfliktberatungsstelle</titel>
            <untertitel>Schwangerschaftskonfliktberatungsstelle</untertitel>
            <ansprechpartner></ansprechpartner>
            <link>http://www.bzga-rat.de/referat/famplan/minisite/?idx=1858</link>
            <kurztext></kurztext>
            <plz>29439</plz>
            <ort>Lüchow</ort>
            <bundesland>9</bundesland>
            <strasse>Königsberger Straße 10</strasse>
            <mapx>11.1546438</mapx>
            <mapy>52.9705095</mapy>
            <telefon>05841 120476</telefon>
            <fax>05841 120479</fax>
            <email>r.hoeber-ramlow@gesundheitsamt-ue-dan.de</email>
            <traeger>LandkreisGesundheitsamt Uelzen-Lüchow-Dannenberg</traeger>
            <website>www.luechow-dannenberg.de</website>
            <beratertelefon>05841 120476</beratertelefon>
            <hinweistext>Hinweistext</hinweistext>
            <beratungsschein>1</beratungsschein>
            <angebot></angebot>
            <logo></logo>
            <beratungsart>
                <index>2</index>
                <index>1</index>
            </beratungsart>
            <verband>Kommunale / Freie Land Niedersachsen</verband>
            <kontaktform>0</kontaktform>
            <kontaktemail></kontaktemail>
            <suchcontent>Gesundheitsamt Uelzen  Lüchow Dannenberg  Schwangerschaftskonfliktberatungsstelle</suchcontent>
        </entry>';

        return [
            [$xml],
        ];
    }
}
