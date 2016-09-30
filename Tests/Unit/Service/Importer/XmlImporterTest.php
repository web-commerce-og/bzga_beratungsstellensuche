<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Service\Importer;


use Bzga\BzgaBeratungsstellensuche\Domain\Manager\CategoryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\EntryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\PndConsultingManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\ReligionManager;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\PndConsultingNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class XmlImporterTest extends UnitTestCase
{

    /**
     * @var XmlImporter
     */
    protected $subject;

    /**
     * @var string
     */
    protected $fixture;

    /**
     * @var int
     */
    protected static $numberOfEntriesInFile = 1620;

    /**
     * @var int
     */
    protected static $numberOfReligionsInFile = 3;

    /**
     * @var int
     */
    protected static $numberOfCategoriesInFile = 3;

    /**
     * @var int
     */
    protected static $numberOfPndConsultingsInFile = 3;

    /**
     * @var EntryManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entryManager;

    /**
     * @var ReligionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $religionManager;

    /**
     * @var CategoryManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryManager;

    /**
     * @var PndConsultingManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pndConsultingManager;

    /**
     * @var Serializer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializer;

    /**
     * @var EntryNormalizer
     */
    protected $entryNormalizer;

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
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @return void
     */
    protected function setUp()
    {

        $this->signalSlotDispatcher = $this->getMock(Dispatcher::class);

        $this->entryManager = $this->getMock(EntryManager::class, array(), array(), '', false);

        $this->categoryManager = $this->getMock(CategoryManager::class, array(), array(), '', false);

        $this->pndConsultingManager = $this->getMock(PndConsultingManager::class, array(), array(), '', false);

        $this->religionManager = $this->getMock(ReligionManager::class, array(), array(), '', false);


        $this->entryNormalizer = new EntryNormalizer();

        $this->countryZoneRepository = $this->getMock(CountryZoneRepository::class,
            array('findOneByExternalId'), array(), '', false);

        $this->religionRepository = $this->getMock(ReligionRepository::class, array('findOneByExternalId'), array(), '',
            false);

        $this->categoryRepository = $this->getMock(CategoryRepository::class, array('findOneByExternalId'), array(), '',
            false);

        $this->languageRepository = $this->getMock(LanguageRepository::class, array('findOneByExternalId'), array(), '',
            false);

        $this->inject($this->entryNormalizer, 'religionRepository', $this->religionRepository);
        $this->inject($this->entryNormalizer, 'categoryRepository', $this->categoryRepository);
        $this->inject($this->entryNormalizer, 'languageRepository', $this->languageRepository);
        $this->inject($this->entryNormalizer, 'countryZoneRepository', $this->countryZoneRepository);

        $normalizers = array(
            $this->entryNormalizer,
            new CategoryNormalizer(),
            new ReligionNormalizer(),
            new PndConsultingNormalizer(),
        );

        $this->serializer = new Serializer($normalizers);
        $this->subject = new XmlImporter();
        $this->fixture = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'beratungsstellen.xml';

        $this->inject($this->subject, 'signalSlotDispatcher', $this->signalSlotDispatcher);
        $this->inject($this->subject, 'entryManager', $this->entryManager);
        $this->inject($this->subject, 'serializer', $this->serializer);
        $this->inject($this->subject, 'categoryManager', $this->categoryManager);
        $this->inject($this->subject, 'religionManager', $this->religionManager);
        $this->inject($this->subject, 'pndConsultingManager', $this->pndConsultingManager);

        $this->religionManager->expects($this->exactly(static::$numberOfReligionsInFile))->method('create');
        $this->categoryManager->expects($this->exactly(static::$numberOfCategoriesInFile))->method('create');
        $this->entryManager->expects($this->exactly(static::$numberOfEntriesInFile))->method('remove');
        $this->entryManager->expects($this->exactly(static::$numberOfEntriesInFile))->method('create');
        $this->pndConsultingManager->expects($this->exactly(static::$numberOfPndConsultingsInFile))->method('create');
        $this->signalSlotDispatcher->expects($this->exactly(2))->method('dispatch');

    }

    /**
     * @test
     */
    public function importFromFile()
    {
        $this->mockExpectations();
        $this->subject->importFromFile($this->fixture);
    }

    /**
     * @test
     */
    public function importFromUrl()
    {
        $this->mockExpectations();
        $this->subject->importFromUrl($this->fixture);
    }

    /**
     * @return void
     */
    public function mockExpectations()
    {
        $categoryMock = $this->getMock(Category::class);
        $this->categoryRepository->expects($this->any())->method('findOneByExternalId')->willReturn($categoryMock);

        $languageMock = $this->getMock(Language::class);
        $this->languageRepository->expects($this->any())->method('findOneByExternalId')->willReturn($languageMock);

        $religionMock = $this->getMock(Religion::class);
        $this->religionRepository->expects($this->any())->method('findOneByExternalId')->willReturn($religionMock);

        $countryZoneMock = $this->getMock(CountryZone::class);
        $this->countryZoneRepository->expects($this->any())->method('findOneByExternalId')->willReturn($countryZoneMock);
    }
}
