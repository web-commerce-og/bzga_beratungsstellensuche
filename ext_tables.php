<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

# We check if either curl is installed or allow_url_fopen
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['bzgaberatungsstellensuche'] = [
        \Bzga\BzgaBeratungsstellensuche\Report\StatusAllowUrlFopenOrCurlReport::class,
    ];
}
