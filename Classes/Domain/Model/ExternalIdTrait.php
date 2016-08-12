<?php

namespace BZgA\BzgaBeratungsstellensuche\Domain\Model;

use Symfony\Component\Serializer\Annotation\Groups;

trait ExternalIdTrait
{
    /**
     * @var int
     * @validate NotEmpty
     */
    protected $externalId;

    /**
     * @Groups({"exportPublic"})
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
