<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Repository;

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

/**
 * @author Sebastian Schreiber
 */
class KilometerRepository
{

    /**
     * @param array $settings
     * @return array
     */
    public function findKilometersBySettings(array $settings)
    {
        $kilometers = isset($settings['form']['kilometers']) ? $settings['form']['kilometers'] : '10,20,50,100';
        $kilometersArray = GeneralUtility::intExplode(',', $kilometers);
        return array_combine($kilometersArray, $kilometersArray);
    }
}
