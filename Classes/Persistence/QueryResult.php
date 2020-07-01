<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Persistence;

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

use TYPO3\CMS\Extbase\Persistence\Generic\Qom\Statement;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult as CoreQueryResult;

class QueryResult extends CoreQueryResult
{

    /**
     * Overwrites the original implementation of Extbase
     *
     * When the query contains a $statement the query is regularly executed and the number of results is counted
     * instead of the original implementation which tries to create a custom COUNT(*) query and delivers wrong results.
     */
    public function count(): int
    {
        if ($this->numberOfResults === null) {
            if (is_array($this->queryResult)) {
                $this->numberOfResults = count($this->queryResult);
            } else {
                $statement = $this->query->getStatement();
                if ($statement instanceof Statement) {
                    $this->initialize();
                    $this->numberOfResults = count($this->queryResult);
                } else {
                    return parent::count();
                }
            }
        }
        return $this->numberOfResults;
    }
}
