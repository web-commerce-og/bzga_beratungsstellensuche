<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


class PndConsultingManager extends AbstractManager
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\PndConsultingRepository
     * @inject
     */
    protected $repository;

    /**
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Repository\PndConsultingRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

}