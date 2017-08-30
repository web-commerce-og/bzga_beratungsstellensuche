<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional\ViewHelpers;

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

use Bzga\BzgaBeratungsstellensuche\ViewHelpers\TranslateViewHelper;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class TranslateViewHelperTest extends AbstractViewHelperBaseTestcase
{

    /**
     * @var array
     */
    protected $coreExtensionsToLoad = ['extbase', 'fluid'];

    /**
     * @var array
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/bzga_beratungsstellensuche', 'typo3conf/ext/static_info_tables'];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var TranslateViewHelper
     */
    private $subject;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Bootstrap::getInstance()->initializeLanguageObject();
        $this->objectManager   = GeneralUtility::makeInstance(ObjectManager::class);
        $this->subject = $this->objectManager->get(TranslateViewHelper::class);
        $this->injectDependenciesIntoViewHelper($this->subject);
    }

    /**
     * @test
     */
    public function translateFromDefaultExtension()
    {
        $this->subject->setArguments(['id' => 'previous-page']);
        $this->assertSame('vorherige Seite', $this->subject->render());
    }
}
