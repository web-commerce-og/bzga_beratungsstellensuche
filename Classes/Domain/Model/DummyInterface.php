<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;


interface DummyInterface
{

    /**
     * @return bool
     */
    public function getIsDummyRecord();

    /**
     * @param bool $isDummyRecord
     */
    public function setIsDummyRecord($isDummyRecord);

}