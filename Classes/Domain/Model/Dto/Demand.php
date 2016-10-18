<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionTrait;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class Demand extends AbstractValueObject implements GeoPositionDemandInterface
{

    use GeopositionTrait;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var array
     */
    protected $searchFields = 'title,teaser,subtitle,description,keywords';

    /**
     * @var string
     */
    protected $location;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    protected $categories;

    /**
     * @var integer
     */
    protected $kilometers = 10;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Model\CountryZone
     */
    protected $countryZone;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Service\Geolocation\Decorator\GeolocationServiceCacheDecorator
     * @inject
     */
    protected $geolocationService;

    /**
     * Demand constructor.
     */
    public function __construct()
    {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }


    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }


    /**
     * @return integer
     */
    public function getKilometers()
    {
        return $this->kilometers;
    }

    /**
     * @param integer $kilometers
     */
    public function setKilometers($kilometers)
    {
        $this->kilometers = $kilometers;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return array
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * @param array $searchFields
     */
    public function setSearchFields($searchFields)
    {
        $this->searchFields = $searchFields;
    }

    /**
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone
     */
    public function getCountryZone()
    {
        return $this->countryZone;
    }

    /**
     * @param \SJBR\StaticInfoTables\Domain\Model\CountryZone $countryZone
     */
    public function setCountryZone($countryZone)
    {
        $this->countryZone = $countryZone;
    }

    /**
     * Returns the longitude.
     * @return float $longitude
     */
    public function getLongitude()
    {
        if (empty($this->longitude)) {
            $this->updateLatitudeLongitude();
        }


        return $this->longitude;
    }

    /**
     * Returns the latitude.
     * @return float $latitude
     */
    public function getLatitude()
    {
        if (empty($this->latitude)) {
            $this->updateLatitudeLongitude();
        }

        return $this->latitude;
    }

    /**
     * @return void
     */
    private function updateLatitudeLongitude()
    {
        $address = $this->geolocationService->findAddressByDemand($this);
        if ($address instanceof \Geocoder\Model\Address) {
            $this->latitude = $address->getLatitude();
            $this->longitude = $address->getLongitude();
        }
    }


}