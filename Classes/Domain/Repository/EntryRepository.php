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

class EntryRepository extends AbstractBaseRepository
{

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

        if($demand->getCategories()->count() > 0) {
            $categoryConstraints = array();
            foreach($demand->getCategories() as $category) {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
            if(!empty($categoryConstraints)) {
                $constraints[] = $query->logicalOr($categoryConstraints);
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

        return $query->execute();

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
