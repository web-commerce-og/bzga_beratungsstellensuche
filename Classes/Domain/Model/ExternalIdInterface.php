<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;


interface ExternalIdInterface
{

    /**
     * @return int
     */
    public function getExternalId();

    /**
     * @param int $externalId
     */
    public function setExternalId($externalId);

}