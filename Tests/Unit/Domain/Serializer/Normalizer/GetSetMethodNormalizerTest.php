<?php

namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\Normalizer;

use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\GetSetMethodNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class GetSetMethodNormalizerTest extends UnitTestCase
{

    /**
     * @var GetSetMethodNormalizer
     */
    protected $subject;

    /**
     * @var Dispatcher|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $signalSlotDispatcher;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->signalSlotDispatcher = $this->getMock(Dispatcher::class);
        $this->serializer = $this->getMock(__NAMESPACE__.'\SerializerNormalizer');
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->subject = new GetSetMethodNormalizer($classMetadataFactory, new EntryNameConverter());
        $this->inject($this->subject, 'signalSlotDispatcher', $this->signalSlotDispatcher);
        $this->subject->setSerializer($this->serializer);
    }

    /**
     * @test
     */
    public function denormalizeEntryWithEntryNameConverter()
    {
        $latitude = (float)81;

        $data = array(
            'mapy' => $latitude,
        );
        $object = $this->subject->denormalize($data, 'BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry');
        /* @var $object Entry */
        self::assertSame($latitude, $object->getLatitude());
    }

    /**
     * @test
     */
    public function denormalizeEntryWithEntryNameConverterAndStateCallback()
    {

        $countryZoneMock = $this->getMock(CountryZone::class);

        $stateCallback = function ($bundesland) use ($countryZoneMock) {
            return $countryZoneMock;
        };

        $this->subject->setDenormalizeCallbacks(array('state' => $stateCallback));

        $data = array(
            'bundesland' => 81,
        );
        $object = $this->subject->denormalize($data, Entry::class);
        /* @var $object Entry */
        self::assertSame($countryZoneMock, $object->getState());

    }

}

abstract class SerializerNormalizer implements SerializerInterface, NormalizerInterface
{
}
