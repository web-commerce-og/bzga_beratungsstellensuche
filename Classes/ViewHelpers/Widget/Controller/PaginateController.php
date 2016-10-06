<?php


namespace BZgA\BzgaBeratungsstellensuche\ViewHelpers\Widget\Controller;

use TYPO3\CMS\Fluid\ViewHelpers\Widget\Controller\PaginateController as CorePaginateController;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

class PaginateController extends CorePaginateController
{

    /**
     * @var Demand
     */
    protected $demand;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
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
                $query->setOffset((int)($itemsPerPage * ($this->currentPage - 1)));
            }
            $sql = $this->createSqlFromQuery($query, $this->demand);
            $modifiedObjects = $query->statement($sql)->execute();
        }
        $this->view->assign('contentArguments', array(
            $this->widgetConfiguration['as'] => $modifiedObjects,
        ));
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
        $databaseConnection = $this->getDatabaseConnection();

        list($hash, $parameters) = $this->queryParser->preparseQuery($query);
        $statementParts = $this->queryParser->parseQuery($query);

        $statementParts['limit'] = ((int)$query->getLimit() ?: null);
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
                $subParameters = array();
                foreach ($parameter as $subParameter) {
                    $subParameters[] = $databaseConnection->fullQuoteStr($subParameter, $tableNameForEscape);
                }
                $parameter = implode(',', $subParameters);
            } elseif ($parameter === null) {
                $parameter = 'NULL';
            } elseif (is_bool($parameter)) {
                return ($parameter === true ? 1 : 0);
            } else {
                $parameter = $databaseConnection->fullQuoteStr((string)$parameter, $tableNameForEscape);
            }

            $statementParts['where'] = str_replace($parameterPlaceholder, $parameter, $statementParts['where']);
        }

        $statementParts = array(
            'selectFields' => implode(' ', $statementParts['keywords']).' '.implode(',', $statementParts['fields']),
            'fromTable' => implode(' ', $statementParts['tables']).' '.implode(' ', $statementParts['unions']),
            'whereClause' => (!empty($statementParts['where']) ? implode('', $statementParts['where']) : '1')
                .(!empty($statementParts['additionalWhereClause'])
                    ? ' AND '.implode(' AND ', $statementParts['additionalWhereClause'])
                    : ''
                ),
            'orderBy' => (!empty($statementParts['orderings']) ? implode(', ', $statementParts['orderings']) : ''),
            'limit' => ($statementParts['offset'] ? $statementParts['offset'].', ' : '')
                .($statementParts['limit'] ? $statementParts['limit'] : ''),
        );

        if ($demand->getLocation()) {
            $distanceField = $this->geolocationService->getDistanceSqlField($demand,$statementParts['fromTable']);
            $statementParts['selectFields'] = $distanceField.','.$statementParts['selectFields'];
            $statementParts['orderBy'] = 'distance ASC';
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
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}