<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional\Utility;

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

use Bzga\BzgaBeratungsstellensuche\Utility\TemplateLayout;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TemplateLayoutTest extends FunctionalTestCase
{

    /**
     * @var TemplateLayout
     */
    protected $subject;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/bzga_beratungsstellensuche'];

    protected function setUp(): void
    {
        parent::setUp();
        $backendUser = $this->setUpBackendUserFromFixture(1);
        $backendUser->workspace = 0;
        Bootstrap::initializeLanguageObject();
        $this->subject = GeneralUtility::makeInstance(TemplateLayout::class);
    }

    /**
     * @test
     */
    public function getAvailableTemplateLayouts(): void
    {
        ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:bzga_beratungsstellensuche/Tests/Functional/Fixtures/TSconfig/Beratungsstellensuche.txt">'
        );

        $templateLayouts = $this->subject->getAvailableTemplateLayouts(0);
        $this->assertSame([['Form Sidebar', 88]], $templateLayouts);
    }
}
