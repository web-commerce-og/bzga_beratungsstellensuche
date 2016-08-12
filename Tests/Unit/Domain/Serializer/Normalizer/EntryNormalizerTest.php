<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\Normalizer;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CountryZoneRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\SerializerInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;

class EntryNormalizerTest extends UnitTestCase
{

    /**
     * @var EntryNormalizer
     */
    protected $subject;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CountryZoneRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $countryZoneRepository;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->countryZoneRepository = $this->getMock(CountryZoneRepository::class);
        $this->serializer = $this->getMock(__NAMESPACE__.'\SerializerNormalizer');
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->subject = new EntryNormalizer($classMetadataFactory, new EntryNameConverter());
        $this->subject->setSerializer($this->serializer);
        $this->inject($this->subject, 'countryZoneRepository', $this->countryZoneRepository);
    }

    /**
     * @test
     */
    public function denormalizeEntryWithEntryNameConverter()
    {

        $data = array(
            'bundesland' => 2,
            'traeger' => 'Institution',
            'titel' => 'Title',
            'untertitel' => 'Subtitle',
            'ansprechpartner' => 'Contact Person',
            'mapx' => 'Latitude',
            'mapy' => 'Longitude',
            'kurztext' => 'Teaser',
            'plz' => 'Zip',
            'ort' => 'City',
            'logo' => 'Image',
            'konfession' => 1,
            'pndberatunglangsons' => 'Other language',
            'strasse' => 'Street',
            'mapok' => 1,
            'telefon' => 'Telephone',
            'fax' => 'Telefax',
            'email' => 'Email',
            'link' => 'Link',
            'website' => 'Website',
            'beratertelefon' => 'Hotline',
            'hinweistext' => 'Notice',
            'mutterundkind' => 1,
            'mutterundkindtext' => 'Mother and child notice',
            'beratungsschein' => 1,
            'angebot' => 'Description',
            'verband' => 'Association',
            'kontaktemail' => 'Contact email',
            'suchcontent' => 'Keywords',
            'beratungsart' => array(),
            'pndberatunglang' => array(),
        );

        $countryZone = new CountryZone();
        $countryZone->setNameLocalized('Namelocalized');
        $this->countryZoneRepository->expects($this->once())->method('findOneByZnCodeFromGermany')->willReturn($countryZone);

        $object = $this->subject->denormalize($data, 'BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry');
        /* @var $object Entry */
        self::assertSame('Namelocalized', $object->getState());
        self::assertSame('Institution', $object->getInstitution());
        self::assertSame('Title', $object->getTitle());
        self::assertSame('Subtitle', $object->getSubtitle());
        self::assertSame('Contact Person', $object->getContactPerson());
        self::assertSame('Latitude', $object->getLatitude());
        self::assertSame('Longitude', $object->getLongitude());
        self::assertSame('Teaser', $object->getTeaser());
        self::assertSame('Zip', $object->getZip());
        self::assertSame('City', $object->getCity());
        self::assertSame('Image', $object->getImage());
        self::assertSame('Other language', $object->getPndOtherLanguage());
        self::assertSame('Street', $object->getStreet());
        self::assertSame('Telephone', $object->getTelephone());
        self::assertSame('Telefax', $object->getTelefax());
        self::assertSame('Email', $object->getEmail());
        self::assertSame('Link', $object->getLink());
        self::assertSame('Website', $object->getWebsite());
        self::assertSame('Hotline', $object->getHotline());
        self::assertSame('Notice', $object->getNotice());
        self::assertTrue($object->getMotherAndChild());
        self::assertSame('Mother and child notice', $object->getMotherAndChildNotice());
        self::assertTrue($object->getConsultingAgreement());
        self::assertSame('Description', $object->getDescription());
        self::assertSame('Association', $object->getAssociation());
        self::assertSame('Contact email', $object->getContactEmail());
        self::assertSame('Keywords', $object->getKeywords());
        self::assertNull($object->getReligiousDenomination());

    }

}
