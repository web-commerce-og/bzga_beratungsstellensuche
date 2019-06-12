<?php


namespace Bzga\BzgaBeratungsstellensuche\Factories;

/**
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
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\HttpAdapter\CurlHttpAdapter;
use GuzzleHttp\Client;
use Http\Client\HttpClient;
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;
use Ivory\HttpAdapter\HttpAdapterInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
class HttpClientFactory
{

    /**
     * @var string
     */
    const TYPE_CURL = 'curl';

    /**
     * @return HttpClient
     */
    public static function createInstance(): HttpClient
    {
        $httpOptions = $GLOBALS['TYPO3_CONF_VARS']['HTTP'];
        $httpOptions['verify'] = filter_var($httpOptions['verify'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $httpOptions['verify'];

        return \Http\Adapter\Guzzle6\Client::createWithConfig($httpOptions);
    }
}
