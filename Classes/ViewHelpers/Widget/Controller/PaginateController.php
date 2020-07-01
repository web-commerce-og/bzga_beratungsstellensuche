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
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator;
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
     * @var GeolocationServiceCacheDecorator
     */
    protected $geolocationService;

    /**
     * @var Typo3DbQueryParser
     */
    protected $queryParser;

    public function initializeAction(): void
    {
        parent::initializeAction();
        $this->demand = $this->widgetConfiguration['demand'];
    }

    public function injectGeolocationService(GeolocationServiceCacheDecorator $geolocationService): void
    {
        $this->geolocationService = $geolocationService;
    }

    public function injectQueryParser(Typo3DbQueryParser $queryParser): void
    {
        $this->queryParser = $queryParser;
    }

    public function indexAction($currentPage = 1): void
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

    private function createSqlFromQuery(QueryInterface $query, Demand $demand): string
    {
        $queryBuilder = $this->queryParser->convertQueryToDoctrineQueryBuilder($query);
        $fromParts = $queryBuilder->getQueryPart('from');

        $queryParameters = $queryBuilder->getParameters();
        $params = [];
        foreach ($queryParameters as $key => $value) {
            // prefix array keys with ':'
            $params[':' . $key] = is_numeric($value) ? $value : "'" . $value . "'"; //all non numeric values have to be quoted
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
