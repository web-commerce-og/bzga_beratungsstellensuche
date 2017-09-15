<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Fluid\ViewHelpers\Widget\Controller\PaginateController as CorePaginateController;

/**
 * @author Sebastian Schreiber
 */
class PaginateController extends CorePaginateController
{

    /**
     * @var Demand
     */
    protected $demand;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
     * @inject
     */
    protected $geolocationService;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser
     * @inject
     */
    protected $queryParser;

    /**
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();
        $this->demand = $this->widgetConfiguration['demand'];
    }

    /**
     * @param int $currentPage
     */
    public function indexAction($currentPage = 1)
    {
        // set current page
        $this->currentPage = (int)$currentPage;
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }
        if ($this->currentPage > $this->numberOfPages) {
            // set $modifiedObjects to NULL if the page does not exist
            $modifiedObjects = null;
        } else {
            // modify query
            $itemsPerPage = (int)$this->configuration['itemsPerPage'];
            $query = $this->objects->getQuery();
            $query->setLimit($itemsPerPage);
            if ($this->currentPage > 1) {
                $query->setOffset($itemsPerPage * ($this->currentPage - 1));
            }
            $sql = $this->createSqlFromQuery($query, $this->demand);
            $modifiedObjects = $query->statement($sql)->execute();
        }
        $this->view->assign('contentArguments', [
            $this->widgetConfiguration['as'] => $modifiedObjects,
        ]);
        $this->view->assign('configuration', $this->configuration);
        $this->view->assign('pagination', $this->buildPagination());
    }

    /**
     * @param QueryInterface $query
     * @param Demand $demand
     * @return int|string
     */
    private function createSqlFromQuery(QueryInterface $query, Demand $demand)
    {
        $isBelow8 = method_exists(Typo3DbQueryParser::class, 'preparseQuery');
        $isBelow8_4 = method_exists(Typo3DbQueryParser::class, 'parseQuery');
        $parameters = [];

        $databaseConnection = $this->getDatabaseConnection();

        if ($isBelow8_4) {
            if ($isBelow8) {
                list($hash, $parameters) = $this->queryParser->preparseQuery($query);
            }
            $statementParts = $this->queryParser->parseQuery($query);

            $statementParts['limit']  = ((int)$query->getLimit() ?: null);
            $statementParts['offset'] = ((int)$query->getOffset() ?: null);

            $tableNameForEscape = (reset($statementParts['tables']) ?: 'foo');
            foreach ($parameters as $parameterPlaceholder => $parameter) {
                if ($parameter instanceof LazyLoadingProxy) {
                    $parameter = $parameter->_loadRealInstance();
                }

                if ($parameter instanceof \DateTime) {
                    $parameter = $parameter->format('U');
                } elseif ($parameter instanceof DomainObjectInterface) {
                    $parameter = (int)$parameter->getUid();
                } elseif (is_array($parameter)) {
                    $subParameters = [];
                    foreach ($parameter as $subParameter) {
                        $subParameters[] = $databaseConnection->fullQuoteStr($subParameter, $tableNameForEscape);
                    }
                    $parameter = implode(',', $subParameters);
                } elseif ($parameter === null) {
                    $parameter = 'NULL';
                } elseif (is_bool($parameter)) {
                    return $parameter === true ? 1 : 0;
                } else {
                    $parameter = $databaseConnection->fullQuoteStr((string)$parameter, $tableNameForEscape);
                }

                $statementParts['where'] = str_replace($parameterPlaceholder, $parameter, $statementParts['where']);
            }

            $statementParts = [
                'selectFields' => implode(' ', $statementParts['keywords']) . ' ' . implode(',', $statementParts['fields']),
                'fromTable'    => implode(' ', $statementParts['tables']) . ' ' . implode(' ', $statementParts['unions']),
                'whereClause'  => (! empty($statementParts['where']) ? implode('', $statementParts['where']) : '1')
                                  . (! empty($statementParts['additionalWhereClause'])
                        ? ' AND ' . implode(' AND ', $statementParts['additionalWhereClause'])
                        : ''
                                  ),
                'orderBy'      => ! empty($statementParts['orderings']) ? implode(', ',
                    $statementParts['orderings']) : '',
                'limit'        => ($statementParts['offset'] ? $statementParts['offset'] . ', ' : '')
                                  . ($statementParts['limit'] ? $statementParts['limit'] : ''),
            ];

            if ($demand->getLocation()) {
                $distanceField                  = $this->geolocationService->getDistanceSqlField($demand,
                    $statementParts['fromTable']);
                $statementParts['selectFields'] = $distanceField . ',' . $statementParts['selectFields'];
                $statementParts['orderBy']      = 'distance ASC';
            }

            $sql = $databaseConnection->SELECTquery(
                $statementParts['selectFields'],
                $statementParts['fromTable'],
                $statementParts['whereClause'],
                '',
                $statementParts['orderBy'],
                $statementParts['limit']
            );

            return $sql;
        } else {
            $queryBuilder = $this->queryParser->convertQueryToDoctrineQueryBuilder($query);
            $fromParts = $queryBuilder->getQueryPart('from');

            $queryParameters = $queryBuilder->getParameters();
            $params = [];
            foreach ($queryParameters as $key => $value) {
                // prefix array keys with ':'
                $params[':' . $key] = (is_numeric($value)) ? $value : "'" . $value . "'"; //all non numeric values have to be quoted
                unset($params[$key]);
            }

            if ($demand->getLocation()) {
                $distanceField = $this->geolocationService->getDistanceSqlField($demand, $fromParts[0]['table']);
                $queryBuilder->addSelectLiteral($distanceField);
                $queryBuilder->orderBy('distance', 'asc');
            }

            $itemsPerPage = (int)$this->configuration['itemsPerPage'];
            $queryBuilder->setMaxResults($itemsPerPage);
            if ($this->currentPage > 1) {
                $queryBuilder->setFirstResult($itemsPerPage * ($this->currentPage - 1));
            }
            // replace placeholders with real values
            $query = strtr($queryBuilder->getSQL(), $params);
            return $query;
        }
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
