<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class KilometerRepository
{

    /**
     * @param array $settings
     * @return array
     */
    public function findKilometersBySettings(array $settings)
    {
        $kilometers = isset($settings['kilometers']) ? $settings['kilometers'] : '10,20,50,100';
        $kilometersArray = GeneralUtility::intExplode(',', $kilometers);
        return array_combine($kilometersArray, $kilometersArray);
    }

}