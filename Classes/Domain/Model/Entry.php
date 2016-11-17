<?php

namespace Bzga\BzgaBeratungsstellensuche\Domain\Model;

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
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @author Sebastian Schreiber
 */
class Entry extends AbstractEntity implements GeopositionInterface, MapWindowInterface
{
    use GeopositionTrait;

    /**
     * @var string
     */
    protected $subtitle;

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
     * Suchinhalt.
     *
     * @var string
     */
    protected $description;

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
    protected $hotline;

    /**
     * @var string
     */
    protected $notice;

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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Bzga\BzgaBeratungsstellensuche\Domain\Model\Category>
     * @lazy
     */
    protected $categories;

    /**
     * @var string
     */
    protected $institution;

    /**
     * @var string
     */
    protected $association;

    /**
     * Entry constructor.
     * @param string $title
     */
    public function __construct($title = '')
    {
        parent::__construct($title);
        $this->categories = new ObjectStorage();
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Bzga\BzgaBeratungsstellensuche\Domain\Model\Category>
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Bzga\BzgaBeratungsstellensuche\Domain\Model\Category> $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param \Bzga\BzgaBeratungsstellensuche\Domain\Model\Category $category
     */
    public function attachCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * @param \Bzga\BzgaBeratungsstellensuche\Domain\Model\Category $category
     */
    public function detachCategory(Category $category)
    {
        $this->categories->detach($category);
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
     * @return string
     */
    public function getAddress()
    {
        $address = '';
        if ($this->zip) {
            $address .= $this->zip;
        }
        if ($this->city) {
            $address .= ' ' . $this->city;
        }
        if ($this->street) {
            $address .= ', ' . $this->street;
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
     * @param array $parameters
     * @param string $template
     * @return string
     */
    public function getInfoWindow(
        array $parameters = [],
        $template = '<p><strong>%1$s</strong><br>%2$s<br>%3$s %4$s</p>'
    ) {
        $title = isset($parameters['detailLink']) ? sprintf('<a href="%2$s">%1$s</a>', $this->getTitle(),
            $parameters['detailLink']) : $this->getTitle();

        return sprintf(
            $template,
            $title,
            $this->getStreet(),
            $this->getZip(),
            $this->getCity()
        );
    }
}
