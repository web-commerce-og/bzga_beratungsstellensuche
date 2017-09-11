<?php


namespace Bzga\BzgaBeratungsstellensuche\Utility;

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

    /**
     * @param QueryResultInterface $queryResult
     * @return ObjectStorage
     */
    public static function transformQueryResultToObjectStorage(QueryResultInterface $queryResult)
    {
        $objectStorage = new ObjectStorage();
        foreach ($queryResult as $item) {
            $objectStorage->attach($item);
        }

        return $objectStorage;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function stripPathSite($string)
    {
        return substr($string, strlen(PATH_site));
    }
}
