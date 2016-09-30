<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;


class CategoryManager extends AbstractManager
{

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $repository;

    /**
     * @return \BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }


}