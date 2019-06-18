<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Manager;

use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * @author Sebastian Schreiber
 */
class EntryManager extends AbstractManager
{

    /**
     * @var EntryRepository
     */
    protected $repository;

    /**
     * @return EntryRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function injectRepository(EntryRepository $repository)
    {
        $this->repository = $repository;
    }
}
