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

use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use Bzga\BzgaBeratungsstellensucheEssstoerungen\Domain\Model\Dto\Demand;
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
    protected $testExtensionsToLoad = ['typo3conf/ext/bzga_beratungsstellensuche'];

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
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
        $this->assertEquals(1, $this->getIdListOfItems($entries));
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
        unset($this->newsRepository);
        unset($this->objectManager);
    }
}
