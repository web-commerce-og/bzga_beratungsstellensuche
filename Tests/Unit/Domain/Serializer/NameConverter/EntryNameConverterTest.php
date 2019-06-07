<?php

namespace Bzga\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;

use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class EntryNameConverterTest extends AbstractNameConverterTest
{

    /**
     */
    protected function setUp()
    {
        $dispatcher = $this->getMockBuilder(Dispatcher::class)->getMock();
        $dispatcher->method('dispatch')->willReturn(['extendedMapNames' => []]);
        $this->subject = new EntryNameConverter([], true, $dispatcher);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
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
