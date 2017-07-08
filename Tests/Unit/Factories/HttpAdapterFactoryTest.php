<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Factories;

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

use Bzga\BzgaBeratungsstellensuche\Factories\HttpAdapterFactory;
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\HttpAdapter\CurlHttpAdapter;
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class HttpAdapterFactoryTest extends UnitTestCase
{

    /**
     * @test
     */
    public function curlHttpAdapaterReturned()
    {
        $this->assertInstanceOf(CurlHttpAdapter::class, HttpAdapterFactory::createInstance(HttpAdapterFactory::TYPE_CURL));
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function curlAsHttpAdapaterReturned()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = 1;
        $this->assertInstanceOf(CurlHttpAdapter::class, HttpAdapterFactory::createInstance());
    }

    /**
     * @test
     */
    public function fileGetContentsAsHttpAdapaterReturned()
    {
        $this->assertInstanceOf(FileGetContentsHttpAdapter::class, HttpAdapterFactory::createInstance());
    }
}
