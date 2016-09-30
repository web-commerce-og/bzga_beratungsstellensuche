<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Service\Importer;


use Bzga\BzgaBeratungsstellensuche\Domain\Manager\CategoryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\EntryManager;
use Bzga\BzgaBeratungsstellensuche\Domain\Manager\ReligionManager;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CountryZoneRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use BZgA\BzgaBeratungsstellensuche\Service\CacheService;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;

class XmlImporterErnaehrungTest extends XmlImporterTest
{

    /**
     * @var int
     */
    protected static $numberOfEntriesInFile = 171;

    /**
     * @var int
     */
    protected static $numberOfReligionsInFile = 3;

    /**
     * @var int
     */
    protected static $numberOfCategoriesInFile = 0;

    /**
     * @var int
     */
    protected static $numberOfPndConsultingsInFile = 0;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'beratungsstellen_ernaehrung.xml';
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
        self::assertSame('*sowieso* KULTUR BERATUNG BILDUNG Frauen für Frauen e. V.', $entry->getTitle());
        self::assertSame('01099', $entry->getZip());
        self::assertSame('http://www.bzga-rat.de/referat/ebs2/minisite/?idx=3063', $entry->getLink());
    }
}
