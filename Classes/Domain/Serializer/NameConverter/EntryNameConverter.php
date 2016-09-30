<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter;


class EntryNameConverter extends AbstractMappingNameConverter
{

    /**
     * Mapping of names, left side incoming names in xml|array, right side name for object
     * @var array
     */
    protected $mapNames = array(
        'index' => 'external_id',
        'titel' => 'title',
        'untertitel' => 'subtitle',
        'ansprechpartner' => 'contact_person',
        'mapx' => 'latitude',
        'mapy' => 'longitude',
        'bundesland' => 'state',
        'kurztext' => 'teaser',
        'plz' => 'zip',
        'ort' => 'city',
        'logo' => 'image',
        'konfession' => 'religious_denomination',
        'pndberatunglangsons' => 'pnd_other_language',
        'strasse' => 'street',
        'mapok' => 'map',
        'telefon' => 'telephone',
        'fax' => 'telefax',
        'email' => 'email',
        'link' => 'link',
        'traeger' => 'institution',
        'website' => 'website',
        'beratertelefon' => 'hotline',
        'hinweistext' => 'notice',
        'mutterundkind' => 'mother_and_child',
        'mutterundkindtext' => 'mother_and_child_notice',
        'beratungsschein' => 'consulting_agreement',
        'angebot' => 'description',
        'verband' => 'association',
        'kontaktemail' => 'contact_email',
        'suchcontent' => 'keywords',
        'beratungsart' => 'categories',
        'pndberatunglang' => 'pnd_languages',
    );


}