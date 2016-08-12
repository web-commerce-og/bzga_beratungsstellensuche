<?php


namespace BZga\BzgaBeratungsstellensuche\Tests\Unit\Service\Exporter;

use Bzga\BzgaBeratungsstellensuche\Service\Exporter\Exporter;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class ExporterTest extends UnitTestCase
{

    /**
     * @var Exporter
     */
    protected $subject;

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->signalSlotDispatcher = $this->getMock(Dispatcher::class);
        $this->subject = new Exporter();
        $this->inject($this->subject, 'signalSlotDispatcher', $this->signalSlotDispatcher);

    }

    /**
     * @test
     */
    public function export()
    {
        $this->markTestSkipped('Not implemented yet');
    }

}
