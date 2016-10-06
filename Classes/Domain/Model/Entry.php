<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use SJBR\StaticInfoTables\Domain\Model\Language;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Entry extends AbstractEntity implements GeopositionInterface
{
    use GeopositionTrait;

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
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\BZgA\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    protected $categories;

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
    protected $pndAllLanguages = null;

    /**
     */
    public function __construct()
    {
        $this->categories = new ObjectStorage();
        $this->pndLanguages = new ObjectStorage();
    }

    /**
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
        if (null === $this->pndAllLanguages) {
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
                $this->pndAllLanguages = implode(', ', $allLanguages);
            } else {
                $this->pndAllLanguages = '';
            }
        }

        return $this->pndAllLanguages;
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
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone $state
     */
    public function getState()
    {
        return $this->state;
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
     * Returns the description.
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
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
     * Returns the religiousDenomination.
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
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}
