<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Exporter;


use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Serializer;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class Exporter
{

    /**
     * @var Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @var Serializer
     * @inject
     */
    protected $serializer;

    /**
     * @return void
     */
    public function export()
    {

    }

}