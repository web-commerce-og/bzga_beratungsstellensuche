<?php

namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;

use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;

class EntryNameConverterTest extends AbstractNameConverterTest
{

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new EntryNameConverter();
    }


    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('index', 'external_id'),
            array('titel', 'title'),
            array('untertitel', 'subtitle'),
            array('ansprechpartner', 'contact_person'),
            array('mapx', 'latitude'),
            array('mapy', 'longitude'),
            array('bundesland', 'state'),
            array('kurztext', 'teaser'),
            array('plz', 'zip'),
            array('ort', 'city'),
            array('logo', 'image'),
            array('konfession', 'religious_denomination'),
            array('pndberatunglangsons', 'pnd_other_language'),
            array('strasse', 'street'),
            array('mapok', 'map'),
            array('telefon', 'telephone'),
            array('fax', 'telefax'),
            array('email', 'email'),
            array('link', 'link'),
            array('traeger', 'institution'),
            array('website', 'website'),
            array('beratertelefon', 'hotline'),
            array('hinweistext', 'notice'),
            array('mutterundkind', 'mother_and_child'),
            array('mutterundkindtext', 'mother_and_child_notice'),
            array('beratungsschein', 'consulting_agreement'),
            array('angebot', 'description'),
            array('verband', 'association'),
            array('kontaktemail', 'contact_email'),
            array('suchcontent', 'keywords'),
            array('beratungsart', 'categories'),
            array('pndberatunglang', 'pnd_languages'),
        );
    }

}
