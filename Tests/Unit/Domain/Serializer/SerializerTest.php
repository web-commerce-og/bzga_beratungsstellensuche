<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer;


use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class SerializerTest extends UnitTestCase
{
    /**
     * @var Serializer
     */
    protected $subject;

    /**
     * @var ReligionRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $religionRepository;

    /**
     * @var CategoryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryRepository;

    /**
     * @var LanguageRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $languageRepository;

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
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('validate');
        AnnotationReader::addGlobalIgnoredName('inject');

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader($reader));
        $this->entryNormalizer = new EntryNormalizer($classMetadataFactory);

        $this->signalSlotDispatcher = $this->getMock(Dispatcher::class);
        $this->countryZoneRepository = $this->getMock(CountryZoneRepository::class, array('findOneByExternalId'),
            array(), '', false);
        $this->religionRepository = $this->getMock(ReligionRepository::class, array('findOneByExternalId'), array(), '',
            false);
        $this->categoryRepository = $this->getMock(CategoryRepository::class, array('findOneByExternalId'), array(), '',
            false);
        $this->languageRepository = $this->getMock(LanguageRepository::class, array('findOneByExternalId'), array(), '',
            false);
        $this->inject($this->entryNormalizer, 'signalSlotDispatcher', $this->signalSlotDispatcher);
        $this->inject($this->entryNormalizer, 'religionRepository', $this->religionRepository);
        $this->inject($this->entryNormalizer, 'categoryRepository', $this->categoryRepository);
        $this->inject($this->entryNormalizer, 'languageRepository', $this->languageRepository);
        $this->inject($this->entryNormalizer, 'countryZoneRepository', $this->countryZoneRepository);
        $normalizers = array(
            $this->entryNormalizer,
            new CategoryNormalizer($classMetadataFactory),
            new ReligionNormalizer($classMetadataFactory),
        );
        $this->subject = new Serializer($normalizers);

    }


    /**
     * @test
     */
    public function deserializeReligionFromXml()
    {
        $xml = "<konfession><index>1</index><sort>1</sort>evangelische Beratungsstellen</konfession>";
        $object = $this->subject->deserialize($xml, Religion::class, 'xml');
        /* @var $object Religion */
        self::assertSame('evangelische Beratungsstellen', $object->getTitle());
        self::assertSame(1, (integer)$object->getExternalId());
    }

    /**
     * @test
     */
    public function deserializeCategoryFromXml()
    {
        $xml = "<beratungsart><index>1</index><sort>2</sort>persönliche Beratung</beratungsart>";
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

        $languageMock = $this->getMock(Language::class);
        $this->languageRepository->expects($this->any())->method('findOneByExternalId')->willReturn($languageMock);

        $religionMock = $this->getMock(Religion::class);
        $this->religionRepository->expects($this->any())->method('findOneByExternalId')->willReturn($religionMock);

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
        $xml = "<entry>
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
            <mapok>1</mapok>
            <mapx>11.1546438</mapx>
            <mapy>52.9705095</mapy>
            <telefon>05841 120476</telefon>
            <fax>05841 120479</fax>
            <email>r.hoeber-ramlow@gesundheitsamt-ue-dan.de</email>
            <traeger>LandkreisGesundheitsamt Uelzen-Lüchow-Dannenberg</traeger>
            <website>www.luechow-dannenberg.de</website>
            <beratertelefon>05841 120476</beratertelefon>
            <hinweistext>Hinweistext</hinweistext>
            <mutterundkind>1</mutterundkind>
            <mutterundkindtext></mutterundkindtext>
            <beratungsschein>1</beratungsschein>
            <angebot></angebot>
            <logo></logo>
            <konfession>3</konfession>
            <beratungsart>
                <index>2</index>
                <index>1</index>
            </beratungsart>
            <pndberatung></pndberatung>
            <pndberatunglang>
                <index>2</index>
                <index>1</index>
            </pndberatunglang>
            <pndberatunglangsons>Esperanto</pndberatunglangsons>
            <verband>Kommunale / Freie Land Niedersachsen</verband>
            <kontaktform>0</kontaktform>
            <kontaktemail></kontaktemail>
            <suchcontent>Gesundheitsamt Uelzen  Lüchow Dannenberg  Schwangerschaftskonfliktberatungsstelle</suchcontent>
        </entry>";

        return array(
            array($xml),
        );
    }


}
