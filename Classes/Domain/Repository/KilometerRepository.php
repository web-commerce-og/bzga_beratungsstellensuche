<?php declare(strict_types = 1);

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
    public function findKilometersBySettings(array $settings): array
    {
        $kilometersFromSettings = $settings['form']['kilometers'] ?? '10:10,20:20,50:50,100:100';
        $kilometerPairs = GeneralUtility::trimExplode(',', $kilometersFromSettings, true);
        $kilometers = [];

        foreach ($kilometerPairs as $kilometerPair) {
            list($label, $value) = GeneralUtility::trimExplode(':', $kilometerPair, true, 2);
            // This is for backwards compatibility reasons, if we have something like 10,20,30 and so on
            $value = $value ?? $label;
            $kilometers[$value] = $label;
        }

        return $kilometers;
    }
}
