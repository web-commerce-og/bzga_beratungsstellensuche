<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;

use TYPO3\CMS\Core\Utility\MathUtility;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use BZgA\BzgaBeratungsstellensuche\Service\Geolocation\GeolocationService;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;

class EntryRepository extends AbstractBaseRepository
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser
     * @inject
     */
    protected $queryParser;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
     * @inject
     */
    protected $geolocationService;

    /**
     * @param Demand $demand
     * @return array|QueryResultInterface
     */
    public function findDemanded(Demand $demand)
    {
        $query = $this->createQuery();

        $constraints = $this->createCoordsConstraints($demand, $query, $demand->getKilometers());


        if ($demand->isMotherAndChild()) {
            $constraints[] = $query->equals('motherAndChild', 1);
        }

        if ($demand->isConsultingAgreement()) {
            $constraints[] = $query->equals('consultingAgreement', 1);
        }

        if ($demand->getReligion()) {
            $constraints[] = $query->equals('religiousDenomination', $demand->getReligion());
        }

        if ($demand->isPndConsulting()) {
            $constraints[] = $query->equals('pndConsulting', 1);
            $constraints[] = $query->equals('pndConsultants', 1);
        }

        if ($keywords = $demand->getKeywords()) {
            $searchFields = GeneralUtility::trimExplode(',', $demand->getSearchFields(), true);
            $searchConstraints = array();

            if (count($searchFields) === 0) {
                throw new \UnexpectedValueException('No search fields defined', 1318497755);
            }

            $keywordsArray = GeneralUtility::trimExplode(' ', $keywords);
            foreach ($keywordsArray as $keyword) {
                foreach ($searchFields as $field) {
                    $searchConstraints[] = $query->like($field, '%'.$keyword.'%');
                }
            }

            if (count($searchConstraints)) {
                $constraints[] = $query->logicalOr($searchConstraints);
            }
        }

        // Call hook functions for additional constraints
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Domain/Repository/EntryRepository.php']['findDemanded'])) {
            $params = array(
                'demand' => $demand,
                'query' => $query,
                'constraints' => &$constraints,
            );
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Domain/Repository/EntryRepository.php']['findDemanded'] as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }


        if (!empty($constraints) && is_array($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $sql = $this->createSqlFromQuery($query, $demand);

        return $query->statement($sql)->execute();
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

        // Limit and offset are not cached to allow caching of pagebrowser queries.
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
            $distanceField = $this->geolocationService->getDistanceSqlField(
                $demand->getLatitude(),
                $demand->getLongitude(),
                $demand->getKilometers()
            );
            $statementParts['selectFields'] = $distanceField.','.$statementParts['selectFields'];
            $statementParts['orderBy'] = 'distance';
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
     * @param GeopositionInterface $userLocation
     * @param QueryInterface $query
     * @param int $radius
     * @return array
     */
    private function createCoordsConstraints(
        GeopositionInterface $userLocation,
        QueryInterface $query,
        $radius = GeolocationService::DEFAULT_RADIUS
    ) {

        if (!$userLocation->getLatitude() || !$userLocation->getLatitude()) {
            return array();
        }

        $earthRadius = GeolocationService::EARTH_RADIUS;

        $lowestLat = (double)$userLocation->getLatitude() - rad2deg($radius / $earthRadius);
        $highestLat = (double)$userLocation->getLatitude() + rad2deg($radius / $earthRadius);
        $lowestLng = (double)$userLocation->getLongitude() - rad2deg($radius / $earthRadius);
        $highestLng = (double)$userLocation->getLongitude() + rad2deg($radius / $earthRadius);

        return array(
            $query->greaterThanOrEqual('latitude', $lowestLat),
            $query->lessThanOrEqual('latitude', $highestLat),
            $query->greaterThanOrEqual('longitude', $lowestLng),
            $query->lessThanOrEqual('longitude', $highestLng),
        );
    }


    /**
     * Here we delete all relations of an entry, this is not possible with juse the convenient remove method of this repository class
     *
     * @param $uid
     */
    public function deleteByUid($uid)
    {
        if (MathUtility::canBeInterpretedAsInteger($uid)) {


            $databaseConnection = $this->getDatabaseConnection();

            $fileReferences = $databaseConnection->exec_SELECTgetRows('uid, uid_local', self::SYS_FILE_REFERENCE,
                'table_local = "sys_file" AND fieldname = "image" AND tablenames = "tx_bzgaberatungsstellensuche_domain_model_entry" AND uid_foreign = '.$uid);

            foreach ($fileReferences as $fileReference) {
                $falFile = ResourceFactory::getInstance()->getFileObject($fileReference['uid_local']);
                $falFile->getStorage()->deleteFile($falFile);
                $databaseConnection->exec_DELETEquery(self::SYS_FILE_REFERENCE, 'uid = '.$fileReference['uid']);
            }

            $databaseConnection->exec_DELETEquery(self::ENTRY_TABLE, 'uid = '.$uid);
            $databaseConnection->exec_DELETEquery(
                self::ENTRY_CATEGORY_MM_TABLE,
                'uid_local ='.$uid
            );
            $databaseConnection->exec_DELETEquery(
                self::LANGUAGE_ENTRY_MM_TABLE,
                'uid_local ='.$uid
            );

        }
    }
}
