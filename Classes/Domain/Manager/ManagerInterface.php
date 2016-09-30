<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\AbstractBaseRepository;

interface ManagerInterface
{

    /**
     * @param AbstractEntity $entity
     * @return mixed
     */
    public function create(AbstractEntity $entity);

    /**
     * @return AbstractBaseRepository
     */
    public function getRepository();

    /**
     * @param $externalId
     * @return mixed
     */
    public function findOneByExternalId($externalId);


}