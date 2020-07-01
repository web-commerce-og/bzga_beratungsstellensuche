<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Report;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

/**
 * @author Sebastian Schreiber
 */
class StatusAllowUrlFopenOrCurlReport implements StatusProviderInterface
{
    /**
     * @var string
     */
    public const MESSAGE = 'allow_url_fopen must be on or curl must be enabled to allow
				communication between TYPO3 and the remote Server to fetch the XML-Url.';

    public function getStatus(): array
    {
        $reports = [];
        $severity = Status::OK;
        $value = 'On';
        $message = '';

        // @TODO: Do we need extra proxy configuration check too?
        if (!ini_get('allow_url_fopen') && !$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse']) {
            $severity = Status::ERROR;
            $value = 'Off';
            $message = self::MESSAGE;
        }

        $reports[] = GeneralUtility::makeInstance(
            Status::class,
            'allow_url_fopen on or curl is enabled',
            $value,
            $message,
            $severity
        );

        return $reports;
    }
}
