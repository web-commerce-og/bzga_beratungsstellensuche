<?php declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Service\Geolocation;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;

/**
 * @author Sebastian Schreiber
 */
interface GeolocationServiceInterface
{

    /**
     * @return mixed
     */
    public function findAddressByDemand(Demand $demand);

    /**
     * @return mixed
     */
    public function getDistanceSqlField(GeoPositionDemandInterface $demandPosition, string $table, string $alias = 'distance');

    /**
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition);
}
