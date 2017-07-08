<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional\Domain\Repository;

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

use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class EntryRepositoryTest extends FunctionalTestCase
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/bzga_beratungsstellensuche', 'typo3conf/ext/static_info_tables'];

    /**
     * @var array
     */
    protected $additionalFoldersToCreate = [
        'fileadmin',
        'fileadmin/_migrated',
        'fileadmin/_migrated/pics',
    ];

    /**
     * @var array
     */
    protected $directoriesToCopy = [
        'typo3conf/ext/bzga_beratungsstellensuche/Tests/Functional/Fixtures/Files/fileadmin/_migrated/pics/' => 'fileadmin/_migrated/pics/',
    ];

    const ENTRY_DEFAULT_FIXTURE_UID = 1;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->objectManager   = GeneralUtility::makeInstance(ObjectManager::class);
        $this->entryRepository = $this->objectManager->get(EntryRepository::class);
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bzgaberatungsstellensuche_domain_model_category.xml');
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_bzgaberatungsstellensuche_domain_model_entry.xml');
    }

    /**
     * @test
     */
    public function findDemanded()
    {
        /** @var Demand $demand */
        $demand = $this->objectManager->get(Demand::class);
        $demand->setKeywords('Keyword');
        $entries = $this->entryRepository->findDemanded($demand);
        $this->assertEquals(self::ENTRY_DEFAULT_FIXTURE_UID, $this->getIdListOfItems($entries));
    }

    /**
     * @test
     */
    public function countByExternalIdAndHash()
    {
        $this->assertEquals(1, $this->entryRepository->countByExternalIdAndHash(1, '32dwwes8'));
    }

    /**
     * @test
     */
    public function findOneByExternalId()
    {
        /** @var Entry $entry */
        $entry = $this->entryRepository->findOneByExternalId(1);
        $this->assertEquals($entry->getUid(), self::ENTRY_DEFAULT_FIXTURE_UID);
    }

    /**
     * @test
     */
    public function deleteByUid()
    {
        $this->setUpRealFiles();
        $this->importDataSet('ntf://Database/sys_file_storage.xml');

        $this->setUpBackendUserFromFixture(1);
        $this->entryRepository->deleteByUid(self::ENTRY_DEFAULT_FIXTURE_UID);
        $this->assertEquals(0, $this->entryRepository->countByUid(self::ENTRY_DEFAULT_FIXTURE_UID));
        $this->assertEquals(0,
            $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'tx_bzgaberatungsstellensuche_entry_category_mm',
                'uid_local = ' . self::ENTRY_DEFAULT_FIXTURE_UID));
        $this->assertEquals(0,
            $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'sys_file_reference',
                'deleted = 0 AND fieldname = "image" AND tablename = "tx_bzgaberatungsstellensuche_domain_model_entry" AND uid_foreign = ' . self::ENTRY_DEFAULT_FIXTURE_UID));
        $this->assertEquals(0,
            $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'sys_file_metadata',
                'file = 10014'));
        $this->assertEquals(0,
            $this->getDatabaseConnection()->exec_SELECTcountRows('*', 'sys_file',
                'uid = 10014'));
    }

    /**
     * @test
     */
    public function findOldEntriesByExternalUidsDiffForTable()
    {
        $oldEntries      = $this->entryRepository->findOldEntriesByExternalUidsDiffForTable('tx_bzgaberatungsstellensuche_domain_model_entry',
            [1]);
        $expectedEntries = [
            [
                'uid' => 2,
            ],
        ];
        $this->assertEquals($expectedEntries, $oldEntries);
    }

    /**
     * @param QueryResultInterface $items
     *
     * @return string
     */
    protected function getIdListOfItems(QueryResultInterface $items)
    {
        $idList = [];
        foreach ($items as $item) {
            $idList[] = $item->getUid();
        }

        return implode(',', $idList);
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->entryRepository);
        unset($this->objectManager);
    }

    /**
     * @return void
     */
    private function setUpRealFiles()
    {
        foreach ($this->directoriesToCopy as $source => $desination) {
            GeneralUtility::copyDirectory($this->getInstancePath() . $source, $this->getInstancePath() . $desination);
        }
    }
}
