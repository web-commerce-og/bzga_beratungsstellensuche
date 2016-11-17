<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;

use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;

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
        return [
            ['index', 'external_id'],
            ['titel', 'title'],
            ['untertitel', 'subtitle'],
            ['ansprechpartner', 'contact_person'],
            ['mapy', 'latitude'],
            ['mapx', 'longitude'],
            ['bundesland', 'state'],
            ['kurztext', 'teaser'],
            ['plz', 'zip'],
            ['ort', 'city'],
            ['logo', 'image'],
            ['strasse', 'street'],
            ['telefon', 'telephone'],
            ['fax', 'telefax'],
            ['email', 'email'],
            ['link', 'link'],
            ['traeger', 'institution'],
            ['website', 'website'],
            ['beratertelefon', 'hotline'],
            ['hinweistext', 'notice'],
            ['angebot', 'description'],
            ['verband', 'association'],
            ['kontaktemail', 'contact_email'],
            ['suchcontent', 'keywords'],
            ['beratungsart', 'categories'],
        ];
    }
}
