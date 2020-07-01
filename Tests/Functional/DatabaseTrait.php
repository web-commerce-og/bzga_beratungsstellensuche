<?php
declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Tests\Functional;

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

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

trait DatabaseTrait
{
    public function selectCount(string $fields, string $table, $where = '1=1'): int
    {
        return $this->getDatabaseInstance($table)
                    ->count($fields)
                    ->from($table)
                    ->where($where)
                    ->execute()
                    ->fetchColumn(0);
    }

    public function getDatabaseInstance(string $table): QueryBuilder
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder;
    }
}
