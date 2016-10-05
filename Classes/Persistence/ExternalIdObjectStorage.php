<?php


namespace BZgA\BzgaBeratungsstellensuche\Persistence;

use BZgA\BzgaBeratungsstellensuche\Domain\Model\ExternalIdInterface;

class ExternalIdObjectStorage extends \SplObjectStorage
{

    /**
     * @param ExternalIdInterface $object
     * @return int
     */
    public function getHash(ExternalIdInterface $object)
    {
        return $object->getExternalId();
    }

}