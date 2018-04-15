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
     * @param string $type
     *
     * @return HttpAdapterInterface|object
     * @throws \InvalidArgumentException
     */
    public static function createInstance($type = null)
    {
        $type = (bool)$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] ? self::TYPE_CURL : $type;

        switch ($type) {
            case self::TYPE_CURL:
                return GeneralUtility::makeInstance(CurlHttpAdapter::class);
                break;
            default:
                return new FileGetContentsHttpAdapter();
                break;
        }
    }
}
