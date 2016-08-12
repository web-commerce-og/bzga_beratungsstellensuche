<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

trait DummyTrait
{
    /**
     * @var bool
     */
    protected $isDummyRecord = false;

    /**
     * @return bool
     */
    public function getIsDummyRecord()
    {
        return $this->isDummyRecord;
    }

    /**
     * @param bool $isDummyRecord
     */
    public function setIsDummyRecord($isDummyRecord)
    {
        $this->isDummyRecord = $isDummyRecord;
    }
}
