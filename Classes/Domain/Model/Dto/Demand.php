<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionTrait;

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
    protected $searchFields = 'title,description,keywords';

    /**
     * @var string
     */
    protected $location;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion
     */
    protected $religion;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    protected $categories;

    /**
     * @var integer
     */
    protected $kilometers = 10;

    /**
     * @var boolean
     */
    protected $motherAndChild = false;

    /**
     * @var boolean
     */
    protected $consultingAgreement = false;

    /**
     * @var boolean
     */
    protected $pndConsulting;

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
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion $religion
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;
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
     * @return boolean
     */
    public function isMotherAndChild()
    {
        return $this->motherAndChild;
    }

    /**
     * @param boolean $motherAndChild
     */
    public function setMotherAndChild($motherAndChild)
    {
        $this->motherAndChild = $motherAndChild;
    }

    /**
     * @return boolean
     */
    public function isConsultingAgreement()
    {
        return $this->consultingAgreement;
    }

    /**
     * @param boolean $consultingAgreement
     */
    public function setConsultingAgreement($consultingAgreement)
    {
        $this->consultingAgreement = $consultingAgreement;
    }

    /**
     * @return boolean
     */
    public function isPndConsulting()
    {
        return $this->pndConsulting;
    }

    /**
     * @param boolean $pndConsulting
     */
    public function setPndConsulting($pndConsulting)
    {
        $this->pndConsulting = $pndConsulting;
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