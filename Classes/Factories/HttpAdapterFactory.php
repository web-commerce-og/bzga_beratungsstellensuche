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
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;
use Ivory\HttpAdapter\HttpAdapterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Sebastian Schreiber
 */
class HttpAdapterFactory
{

    /**
     * @var string
     */
    const TYPE_CURL = 'curl';

    /**
     * @param $type
     * @return HttpAdapterInterface
     */
    public static function createInstance($type)
    {
        switch ($type) {
            case self::TYPE_CURL:
                return GeneralUtility::makeInstance(CurlHttpAdapter::class);
                break;
            default:
                $curlUse = isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse']) ? (bool)$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] : false;

                if (true === $curlUse) {
                    return GeneralUtility::makeInstance(CurlHttpAdapter::class);
                }

                return new FileGetContentsHttpAdapter();
                break;
        }
    }
}
