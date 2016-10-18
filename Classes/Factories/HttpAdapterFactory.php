<?php


namespace BZgA\BzgaBeratungsstellensuche\Factories;

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

use BZgA\BzgaBeratungsstellensuche\Service\Geolocation\HttpAdapter\CurlHttpAdapter;
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Ivory\HttpAdapter\HttpAdapterInterface;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
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
                return new FileGetContentsHttpAdapter();
                break;
        }
    }

}