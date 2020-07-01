<?php
declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\View\Entry;

/*
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

use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Mvc\View\AbstractView;

final class AutocompleteJson extends AbstractView
{
    public function render(): string
    {
        /** @var Entry[] $entries */
        $entries = $this->variables['entries'];
        $q = $this->variables['q'];

        $suggestions = [];

        foreach ($entries as $entry) {
            if (StringUtility::beginsWith($entry->getCity(), $q)) {
                $suggestions[] = $entry->getCity();
            }

            if (StringUtility::beginsWith($entry->getZip(), $q)) {
                $suggestions[] = $entry->getZip();
            }
        }

        return json_encode(array_unique($suggestions));
    }
}
