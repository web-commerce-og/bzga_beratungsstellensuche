<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter;

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

/**
 * @author Sebastian Schreiber
 */
class EntryNameConverter extends BaseMappingNameConverter
{

    /**
     * Mapping of names, left side incoming names in xml|array, right side name for object
     * @var array
     */
    protected $mapNames = [
        'index' => 'external_id',
        'titel' => 'title',
        'untertitel' => 'subtitle',
        'ansprechpartner' => 'contact_person',
        'mapy' => 'latitude',
        'mapx' => 'longitude',
        'bundesland' => 'state',
        'kurztext' => 'teaser',
        'plz' => 'zip',
        'ort' => 'city',
        'logo' => 'image',
        'strasse' => 'street',
        'telefon' => 'telephone',
        'fax' => 'telefax',
        'email' => 'email',
        'website' => 'website',
        'beratertelefon' => 'hotline',
        'hinweistext' => 'notice',
        'angebot' => 'description',
        'kontaktemail' => 'contact_email',
        'suchcontent' => 'keywords',
        'beratungsart' => 'categories',
        'verband' => 'association',
        'traeger' => 'institution',
    ];
}
