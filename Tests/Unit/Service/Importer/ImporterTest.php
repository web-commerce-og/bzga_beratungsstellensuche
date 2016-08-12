<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Service\Importer;


use Bzga\BzgaBeratungsstellensuche\Domain\Manager\CategoryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\EntryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\PndConsultingManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\ReligionManager;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CountryZoneRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\PndConsultingNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use BZgA\BzgaBeratungsstellensuche\Service\CacheService;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\Importer;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class ImporterTest extends UnitTestCase
{

    /**
     * @var Importer
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
     * @var CacheService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheService;

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
        $this->entryManager = $this->getMock(EntryManager::class);
        $this->categoryManager = $this->getMock(CategoryManager::class);
        $this->pndConsultingManager = $this->getMock(PndConsultingManager::class);
        $this->religionManager = $this->getMock(ReligionManager::class);
        $this->cacheService = $this->getMock(CacheService::class);
        $this->entryNormalizer = new EntryNormalizer();
        $this->countryZoneRepository = $this->getMock(CountryZoneRepository::class,
            array('findOneByZnCodeFromGermany'));
        $this->religionRepository = $this->getMock(ReligionRepository::class, array('findOneByExternalId'), array(), '',
            false);
        $this->categoryRepository = $this->getMock(CategoryRepository::class, array('findOneByExternalId'), array(), '',
            false);
        $this->languageRepository = $this->getMock(LanguageRepository::class, array('findOneByIsoCodes'), array(), '',
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
        $this->subject = new Importer();
        $this->fixture = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'beratungsstellen.xml';
        $this->inject($this->subject, 'signalSlotDispatcher', $this->signalSlotDispatcher);
        $this->inject($this->subject, 'entryManager', $this->entryManager);
        $this->inject($this->subject, 'serializer', $this->serializer);
        $this->inject($this->subject, 'cacheService', $this->cacheService);
        $this->inject($this->subject, 'categoryManager', $this->categoryManager);
        $this->inject($this->subject, 'religionManager', $this->religionManager);
        $this->inject($this->subject, 'pndConsultingManager', $this->pndConsultingManager);
        $this->religionManager->expects($this->exactly(static::$numberOfReligionsInFile))->method('create');
        $this->categoryManager->expects($this->exactly(static::$numberOfCategoriesInFile))->method('create');
        $this->entryManager->expects($this->exactly(static::$numberOfEntriesInFile))->method('remove');
        $this->entryManager->expects($this->exactly(static::$numberOfEntriesInFile))->method('create');
        $this->pndConsultingManager->expects($this->exactly(static::$numberOfPndConsultingsInFile))->method('create');
        $this->cacheService->expects($this->once())->method('clearCache');
        $this->signalSlotDispatcher->expects($this->exactly(2))->method('dispatch');

    }

    /**
     * @test
     */
    public function importFromFile()
    {
        $this->mockExpectations();
        $this->subject->importFromFile($this->fixture);
        $this->importExpectations();
    }

    /**
     * @test
     */
    public function importFromUrl()
    {
        $this->mockExpectations();
        $this->subject->importFromUrl($this->fixture);
        $this->importExpectations();
    }

    /**
     * @return void
     */
    public function mockExpectations()
    {
        $this->categoryRepository->expects($this->any())->method('findOneByExternalId')->willReturn(new Category());
        $this->languageRepository->expects($this->any())->method('findOneByIsoCodes')->willReturn(new Language());
        $this->religionRepository->expects($this->any())->method('findOneByExternalId')->willReturn(new Religion());

        $countryZone = new CountryZone();
        $countryZone->setNameLocalized('Name');
        $this->countryZoneRepository->expects($this->any())->method('findOneByZnCodeFromGermany')->willReturn($countryZone);
    }

    /**
     * @return void
     */
    protected function importExpectations()
    {
        self::assertCount(static::$numberOfEntriesInFile, $this->subject);
        $iterator = $this->subject->getIterator();
        $entry = $iterator[0];
        /* @var $entry Entry */
        self::assertInstanceOf(Religion::class, $entry->getReligiousDenomination());
        self::assertSame('Name', $entry->getState());
        self::assertCount(1, $entry->getPndLanguages());
        self::assertCount(1, $entry->getCategories());
    }
}
