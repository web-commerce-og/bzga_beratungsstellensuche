<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter;


class ReligionNameConverter extends AbstractMappingNameConverter
{
    /**
     * @return void
     */
    protected function emitMapNamesSignal()
    {
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_MapNames, array($this, $this->mapNames));
    }


}