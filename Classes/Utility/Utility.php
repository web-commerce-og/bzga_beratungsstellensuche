<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Utility;

use TYPO3\CMS\Core\Core\Environment;
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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * @author Sebastian Schreiber
 */
class Utility
{
    public static function transformQueryResultToObjectStorage(QueryResultInterface $queryResult): ObjectStorage
    {
        $objectStorage = new ObjectStorage();
        foreach ($queryResult as $item) {
            $objectStorage->attach($item);
        }

        return $objectStorage;
    }

    public static function stripPathSite(string $string): string
    {
        return substr($string, strlen(Environment::getPublicPath()));
    }
}
