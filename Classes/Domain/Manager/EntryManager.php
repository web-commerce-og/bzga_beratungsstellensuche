<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;



class EntryManager extends AbstractManager
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $repository;

    /**
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }


}