<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


class ReligionManager extends AbstractManager
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository
     * @inject
     */
    protected $repository;

    /**
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }



}