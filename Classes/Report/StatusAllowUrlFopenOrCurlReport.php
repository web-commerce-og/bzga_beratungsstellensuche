<?php

namespace BZgA\BzgaBeratungsstellensuche\Report;

use TYPO3\CMS\Reports\StatusProviderInterface;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StatusAllowUrlFopenOrCurlReport implements StatusProviderInterface
{
    /**
     * @var string
     */
    const MESSAGE = 'allow_url_fopen must be on or curl must be enabled to allow
				communication between TYPO3 and the remote Server to fetch the XML-Url.';

    /**
     * Checks whether allow_url_fopen is enabled.
     *
     * @see typo3/sysext/reports/interfaces/tx_reports_StatusProvider::getStatus()
     */
    public function getStatus()
    {
        $reports = array();
        $severity = Status::OK;
        $value = 'On';
        $message = '';

        if (!ini_get('allow_url_fopen') && !$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse']) {
            $severity = Status::ERROR;
            $value = 'Off';
            $message = self::MESSAGE;
        }

        $reports[] = GeneralUtility::makeInstance(
            Status::class,
            'allow_url_fopen on or curl is used',
            $value,
            $message,
            $severity
        );

        return $reports;
    }
}
