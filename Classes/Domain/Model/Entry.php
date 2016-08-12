<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Symfony\Component\Serializer\Annotation\Groups;

class Entry extends AbstractEntity implements GeopositionInterface
{
    use GeopositionTrait;
    use DummyTrait;
    use ExternalIdTrait;

    /**
     * Name der Beratungsstelle.
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * @var string
     */
    protected $subtitle;

    /**
     * Link zur Beratungsstelle.
     *
     * @var string
     */
    protected $link;

    /**
     * Kurzer Aufmacher zur Beratungsstelle.
     *
     * @var string
     */
    protected $teaser;

    /**
     * PLZ der Beratungsstelle.
     *
     * @var string
     */
    protected $zip;

    /**
     * Ort der Beratungsstelle.
     *
     * @var string
     */
    protected $city;

    /**
     * Straße der Beratungsstelle.
     *
     * @var string
     */
    protected $street;

    /**
     * Bundesland der Beratungsstelle.
     *
     * @var \SJBR\StaticInfoTables\Domain\Model\CountryZone
     */
    protected $state;

    /**
     * Karte in Ordnung?
     *
     * @var string
     */
    protected $map;

    /**
     * Suchinhalt.
     *
     * @var string
     */
    protected $description;

    /**
     * Konfession.
     *
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion
     */
    protected $religiousDenomination;

    /**
     * @var float
     */
    protected $distance = null;

    /**
     * @var string
     */
    protected $contactPerson;

    /**
     * @var string
     */
    protected $contactEmail;

    /**
     * @var string
     */
    protected $telephone;

    /**
     * @var string
     */
    protected $telefax;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $institution;

    /**
     * @var string
     */
    protected $association;

    /**
     * @var string
     */
    protected $hotline;

    /**
     * @var string
     */
    protected $notice;

    /**
     * @var bool
     */
    protected $motherAndChild = false;

    /**
     * @var string
     */
    protected $motherAndChildNotice;

    /**
     * @var bool
     */
    protected $consultingAgreement = false;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var string
     */
    protected $website;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    protected $categories;

    /**
     * @var string
     */
    protected $iconFileName;

    /**
     * @var string
     */
    protected $uriBuilder = null;

    /**
     * @var array
     */
    protected $demand;

    /**
     * @var int
     */
    protected $country = 1;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $owner;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var bool
     */
    protected $pndConsulting = false;

    /**
     * @var bool
     */
    protected $pndConsultants = false;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SJBR\StaticInfoTables\Domain\Model\Language>
     */
    protected $pndLanguages;

    /**
     * @var string
     */
    protected $pndOtherLanguage;

    /**
     * @var string
     */
    protected $allLanguages = null;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Service\GeoLocationService
     * @inject
     */
    protected $geoLocationService;

    /**
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     */
    public function initStorageObjects()
    {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->pndLanguages = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @Groups({"exportPublic"})
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category> $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Category $category
     */
    public function attachCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Category $category
     */
    public function detachCategory(Category $category)
    {
        $this->categories->detach($category);
    }

    /**
     * @return bool
     */
    public function getPndConsulting()
    {
        return (boolean)$this->pndConsulting;
    }

    /**
     * @param bool $pndConsulting
     */
    public function setPndConsulting($pndConsulting)
    {
        $this->pndConsulting = (boolean)$pndConsulting;
    }

    /**
     * @Groups({"exportPublic"})
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SJBR\StaticInfoTables\Domain\Model\Language>
     */
    public function getPndLanguages()
    {
        return $this->pndLanguages;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SJBR\StaticInfoTables\Domain\Model\Language> $pndLanguages
     */
    public function setPndLanguages($pndLanguages)
    {
        $this->pndLanguages = $pndLanguages;
    }

    /**
     * @param \SJBR\StaticInfoTables\Domain\Model\Language $language
     */
    public function attachPndLanguage(Language $language)
    {
        $this->pndLanguages->attach($language);
    }

    /**
     * @param \SJBR\StaticInfoTables\Domain\Model\Language $language
     */
    public function detachPndLanguage(Language $language)
    {
        $this->pndLanguages->detach($language);
    }

    /**
     * @return bool
     */
    public function getPndConsultants()
    {
        return $this->pndConsultants;
    }

    /**
     * @param bool $pndConsultants
     */
    public function setPndConsultants($pndConsultants)
    {
        $this->pndConsultants = $pndConsultants;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getPndOtherLanguage()
    {
        return $this->pndOtherLanguage;
    }

    /**
     * @param string $pndOtherLanguage
     */
    public function setPndOtherLanguage($pndOtherLanguage)
    {
        $this->pndOtherLanguage = $pndOtherLanguage;
    }

    /**
     * @return string
     */
    public function getPndAllLanguages()
    {
        if (null === $this->allLanguages) {
            if ($this->pndLanguages || $this->pndOtherLanguage) {
                $allLanguages = array();
                foreach ($this->pndLanguages as $pndLanguage) {
                    /* @var $pndLanguage \SJBR\StaticInfoTables\Domain\Model\Language */
                    $allLanguages[] = $pndLanguage->getNameLocalized();
                }
                if ($this->pndOtherLanguage) {
                    $otherLanguages = GeneralUtility::trimExplode(',', $this->pndOtherLanguage);
                    foreach ($otherLanguages as $otherLanguage) {
                        $allLanguages[] = $otherLanguage;
                    }
                }
                sort($allLanguages);
                $this->allLanguages = implode(', ', $allLanguages);
            } else {
                $this->allLanguages = '';
            }
        }

        return $this->allLanguages;
    }

    /**
     * @return bool
     */
    public function getHasPndConsulting()
    {
        return $this->pndConsultants && $this->pndConsulting ? true : false;
    }

    /**
     * @return string
     */
    public function getListOfCategories()
    {
        $listOfCategories = array();
        if ($this->categories->count() > 0) {
            $categories = $this->categories->toArray();
            foreach ($categories as $category) {
                /* @var $category \BZgA\BzgaBeratungsstellensuche\Domain\Model\Category */
                $listOfCategories[] = $category->getEtbId();
            }
        }

        return implode(',', $listOfCategories);
    }

    /**
     * Get type of entry. This is static. All entries are of type 142.
     *
     * @return int
     */
    public function getType()
    {
        return 142;
    }

    /**
     * Returns the title.
     * @Groups({"exportPublic"})
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Returns the link.
     * @Groups({"exportPublic"})
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Sets the link.
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns the teaser.
     * @Groups({"exportPublic"})
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Sets the teaser.
     *
     * @param string $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Returns the zip.
     * @Groups({"exportPublic"})
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip.
     *
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the city.
     * @Groups({"exportPublic"})
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city.
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the street.
     * @Groups({"exportPublic"})
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street.
     *
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the state.
     * @Groups({"exportPublic"})
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone $state
     */
    public function getState()
    {
        return $this->state instanceof CountryZone ? $this->state->getNameLocalized() : '';
    }

    /**
     * Sets the state.
     *
     * @param \SJBR\StaticInfoTables\Domain\Model\CountryZone $state
     */
    public function setState(CountryZone $state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param int $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        if (null !== $this->distance) {
            $this->distance = $this->geoLocationService->calculateDistance(
                $this->geoLocationService->getDemand(),
                $this
            );
        }

        return $this->distance;
    }

    /**
     * Returns the description.
     * @Groups({"exportPublic"})
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the description.
     *
     * @return string $description
     */
    public function getDescriptionEtb()
    {
        $description = strip_tags($this->description, '<br><br />');
        $description = str_replace(array('<br>', '<br />'), array('+++br+++'), $description);

        return $description;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the zipCity.
     *
     * @return string $zipCity
     */
    public function getZipCity()
    {
        return $this->zipCity;
    }

    /**
     * Sets the zipCity.
     *
     * @param string $zipCity
     */
    public function setZipCity($zipCity)
    {
        $this->zipCity = $zipCity;
    }

    /**
     * Returns the religiousDenomination.
     * @Groups({"exportPublic"})
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion $religiousDenomination
     */
    public function getReligiousDenomination()
    {
        return $this->religiousDenomination;
    }

    /**
     * Sets the religiousDenomination.
     *
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion $religiousDenomination
     */
    public function setReligiousDenomination(
        Religion $religiousDenomination
    ) {
        $this->religiousDenomination = $religiousDenomination;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        $address = '';
        if ($this->zip) {
            $address .= $this->zip;
        }
        if ($this->city) {
            $address .= ' '.$this->city;
        }
        if ($this->street) {
            $address .= ', '.$this->street;
        }

        return $address;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param string $contactPerson
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getTelefax()
    {
        return $this->telefax;
    }

    /**
     * @param string $telefax
     */
    public function setTelefax($telefax)
    {
        $this->telefax = $telefax;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * @param string $institution
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param string $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getHotline()
    {
        return $this->hotline;
    }

    /**
     * @param string $hotline
     */
    public function setHotline($hotline)
    {
        $this->hotline = $hotline;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param string $notice
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    }

    /**
     * @Groups({"exportPublic"})
     * @return bool
     */
    public function getMotherAndChild()
    {
        return $this->motherAndChild;
    }

    /**
     * @param bool $motherAndChild
     */
    public function setMotherAndChild($motherAndChild)
    {
        $this->motherAndChild = (boolean)$motherAndChild;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getMotherAndChildNotice()
    {
        return $this->motherAndChildNotice;
    }

    /**
     * @param string $motherAndChildNotice
     */
    public function setMotherAndChildNotice($motherAndChildNotice)
    {
        $this->motherAndChildNotice = $motherAndChildNotice;
    }

    /**
     * @Groups({"exportPublic"})
     * @return bool
     */
    public function getConsultingAgreement()
    {
        return (boolean)$this->consultingAgreement;
    }

    /**
     * @param bool $consultingAgreement
     */
    public function setConsultingAgreement($consultingAgreement)
    {
        $this->consultingAgreement = (boolean)$consultingAgreement;
    }

    /**
     * @Groups({"exportPublic"})
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
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @Groups({"exportPublic"})
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @Groups({"exportPublic"})
     * @return bool
     */
    public function getMap()
    {
        return $this->latitude && $this->longitude ? true : false;
    }
}
