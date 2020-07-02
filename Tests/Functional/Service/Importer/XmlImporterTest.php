<?php declare(strict_types=1);

namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional\Service\Importer;

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

use Bzga\BzgaBeratungsstellensuche\Service\Importer\Exception\ContentCouldNotBeFetchedException;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\XmlImporter;
use Bzga\BzgaBeratungsstellensuche\Tests\Functional\DatabaseTrait;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class XmlImporterTest extends FunctionalTestCase
{
    use DatabaseTrait;

    /**
     * @var string
     */
    private const SYS_FOLDER_FOR_ENTRIES = 10001;

    /**
     * @var XmlImporter
     */
    protected $subject;

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['extbase', 'fluid'];

    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/bzga_beratungsstellensuche', 'typo3conf/ext/static_info_tables', 'typo3conf/ext/static_info_tables_de'];

    /**
     * @var array
     */
    protected $additionalFoldersToCreate = [
        'fileadmin/user_upload/tx_bzgaberatungsstellensuche',
    ];

    /**
     * To prevent some false/positive sql failures
     * @var array
     */
    protected $configurationToUseInTestInstance = [
        'SYS' => [
            'setDBinit' => 'SET SESSION sql_mode = \'\';',
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();
        $backendUser = $this->setUpBackendUserFromFixture(1);
        $backendUser->workspace = 0;
        Bootstrap::initializeLanguageObject();
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $objectManager->get(XmlImporter::class);

        $this->importDataSet(__DIR__.'/../../Fixtures/pages.xml');
        $this->importDataSet(__DIR__.'/../../Fixtures/sys_file_storage.xml');
    }

    /**
     * @test
     */
    public function importFromFile(): void
    {
        $this->subject->importFromFile(
            'EXT:bzga_beratungsstellensuche/Tests/Functional/Fixtures/Import/beratungsstellen.xml',
            self::SYS_FOLDER_FOR_ENTRIES
        );

        foreach ($this->subject as $value) {
            $this->subject->importEntry($value);
        }
        $this->subject->persist();

        $this->assertEquals(3, $this->selectCount('*', 'tx_bzgaberatungsstellensuche_domain_model_category'));
        $this->assertEquals(1, $this->selectCount('*', 'tx_bzgaberatungsstellensuche_domain_model_entry'));
        $this->assertEquals(2, $this->selectCount('*', 'tx_bzgaberatungsstellensuche_entry_category_mm'));
    }

    public function tearDown(): void
    {
        unset($this->subject);
    }
}
