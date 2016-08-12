<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Repository;

use SJBR\StaticInfoTables\Domain\Model\CountryZone;

class CountryZoneRepository
{

    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @param $znCode
     * @return object|CountryZone
     * @internal param Country $country
     */
    public function findOneByZnCodeFromGermany($znCode)
    {
        $query = $this->countryZoneRepository->createQuery();
        $query->matching(
            $query->logicalAnd(
                array(
                    $query->equals('countryIsoCodeA3', 'DEU'),
                    $query->equals('znCode', $znCode),
                )
            )
        );

        return $query->execute()->getFirst();
    }

}