<?php


namespace BZgA\BzgaBeratungsstellensuche\Factories;

use BZgA\BzgaBeratungsstellensuche\Service\Geolocation\HttpAdapter\CurlHttpAdapter;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class HttpAdapterFactory
{

    /**
     * @var string
     */
    const TYPE_CURL = 'curl';

    /**
     * @param $type
     * @return \Ivory\HttpAdapter\HttpAdapterInterface
     */
    public static function createInstance($type)
    {
        switch ($type) {
            case self::TYPE_CURL:
                return GeneralUtility::makeInstance(CurlHttpAdapter::class);
                break;
            default:
                return new \Ivory\HttpAdapter\FileGetContentsHttpAdapter();
                break;
        }
    }

}