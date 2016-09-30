<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

trait ExternalIdTrait
{
    /**
     * @var int
     * @validate NotEmpty
     */
    protected $externalId;

    /**
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }
}
