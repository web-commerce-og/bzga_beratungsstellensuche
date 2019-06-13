<?php

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

use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use Bzga\BzgaBeratungsstellensuche\Events;
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator;
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\GeolocationService;
use InvalidArgumentException;
use RuntimeException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * @author Sebastian Schreiber
 */
class EntryRepository extends AbstractBaseRepository
{

    /**
     * @var GeolocationServiceCacheDecorator
     *
     */
    protected $geolocationService;

    /**
     * @var Typo3DbQueryParser
     *
     */
    protected $queryParser;

    /**
     * @param GeolocationServiceCacheDecorator $geolocationService
     */
    public function injectGeolocationService(GeolocationServiceCacheDecorator $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * @param Typo3DbQueryParser $queryParser
     */
    public function injectQueryParser(Typo3DbQueryParser $queryParser)
    {
        $this->queryParser = $queryParser;
    }

    /**
     * @param string $q
     *
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByQuery(string $q)
    {
        $query = $this->createQuery();
        return $query->matching($query->logicalOr([
            $query->like('zip', $q.'%', false),
            $query->like('city', $q.'%', false),
        ]))->execute();
    }

    /**
     * @param Demand $demand
     *
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     * @throws \UnexpectedValueException
     */
    public function findDemanded(Demand $demand)
    {
        $query = $this->createQuery();
        $constraints = $this->createCoordsConstraints($demand, $query, $demand->getKilometers());

        if ($keywords = $demand->getKeywords()) {
            $searchFields = GeneralUtility::trimExplode(',', $demand->getSearchFields(), true);
            $searchConstraints = [];

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

        if ($demand->getCategories()->count() > 0) {
            $categoryConstraints = [];
            foreach ($demand->getCategories() as $category) {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
            if ( ! empty($categoryConstraints)) {
                $constraints[] = $query->logicalOr($categoryConstraints);
            }
        }

        if ($demand->getCountryZone()) {
            $constraints[] = $query->equals('state', $demand->getCountryZone());
        }

        // Call hook functions for additional constraints
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Domain/Repository/EntryRepository.php']['findDemanded'])) {
            $params = [
                'demand' => $demand,
                'query' => $query,
                'constraints' => &$constraints,
            ];
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Domain/Repository/EntryRepository.php']['findDemanded'] as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }

        if ( ! empty($constraints) && is_array($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // Bug. Counting is wrong in TYPO3 Version 8 Doctrine, if we do not use custom statement here. Why?
        if ( ! method_exists(Typo3DbQueryParser::class, 'preparseQuery')) {
            $queryBuilder = $this->queryParser->convertQueryToDoctrineQueryBuilder($query);
            $queryParameters = $queryBuilder->getParameters();
            $params = [];
            foreach ($queryParameters as $key => $value) {
                // prefix array keys with ':'
                $params[':'.$key] = is_numeric($value) ? $value : "'".$value."'"; //all non numeric values have to be quoted
                unset($params[$key]);
            }

            // replace placeholders with real values
            return $query->statement(strtr($queryBuilder->getSQL(), $params))->execute();
        }

        return $query->execute();
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws InvalidArgumentException
     * @throws InvalidSlotReturnException
     * @throws InvalidSlotException
     */
    public function truncateAll()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::ENTRY_TABLE);
        $entries = $queryBuilder->select('uid')->from(self::ENTRY_TABLE)->execute()->fetchAll();
        foreach ($entries as $entry) {
            $this->deleteByUid($entry['uid']);
        }

        $this->signalSlotDispatcher->dispatch(
            static::class,
            Events::TABLE_TRUNCATE_ALL_SIGNAL
        );
    }

    /**
     * @param GeopositionInterface $userLocation
     * @param QueryInterface $query
     * @param int $radius
     *
     * @return array
     * @throws InvalidQueryException
     */
    private function createCoordsConstraints(
        GeopositionInterface $userLocation,
        QueryInterface $query,
        $radius = GeolocationService::DEFAULT_RADIUS
    ): array {
        if ( ! $userLocation->getLatitude() || ! $userLocation->getLongitude()) {
            return [];
        }

        $earthRadius = GeolocationService::EARTH_RADIUS;

        $lowestLat = (double)$userLocation->getLatitude() - rad2deg($radius / $earthRadius);
        $highestLat = (double)$userLocation->getLatitude() + rad2deg($radius / $earthRadius);
        $lowestLng = (double)$userLocation->getLongitude() - rad2deg($radius / $earthRadius);
        $highestLng = (double)$userLocation->getLongitude() + rad2deg($radius / $earthRadius);

        return [
            $query->greaterThanOrEqual('latitude', $lowestLat),
            $query->lessThanOrEqual('latitude', $highestLat),
            $query->greaterThanOrEqual('longitude', $lowestLng),
            $query->lessThanOrEqual('longitude', $highestLng),
        ];
    }

    /**
     * Here we delete all relations of an entry, this is not possible with the convenient remove method of this repository class
     *
     * @param int $uid
     *
     * @throws IllegalObjectTypeException
     * @throws InvalidArgumentException
     * @throws InvalidSlotReturnException
     * @throws InvalidSlotException
     */
    public function deleteByUid($uid)
    {

        /** @var FileRepository $fileRepository */
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);

        /** @var FileReference[] $fileReferences */
        $fileReferences = $fileRepository->findByRelation(self::ENTRY_TABLE, 'image', $uid);
        foreach ($fileReferences as $fileReference) {
            try {
                $fileDeleted = $fileReference->getOriginalFile()->delete();
            } catch (RuntimeException $e) {
            }
        }

        // @cascade remove not working the expected way
        $this->getDatabaseConnectionForTable(self::ENTRY_CATEGORY_MM_TABLE)->delete(self::ENTRY_CATEGORY_MM_TABLE, ['uid_local' => (int)$uid]);

        $entry = $this->findByIdentifier($uid);
        if ($entry instanceof Entry) {
            $this->remove($entry);
            $this->persistenceManager->persistAll();
        }

        $this->signalSlotDispatcher->dispatch(
            static::class,
            Events::REMOVE_ENTRY_FROM_DATABASE_SIGNAL,
            ['uid' => $uid]
        );
    }
}
