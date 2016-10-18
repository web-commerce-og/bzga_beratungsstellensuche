<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation;

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

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
interface GeolocationServiceInterface
{

    /**
     * @param Demand $demand
     * @return mixed
     */
    public function findAddressByDemand(Demand $demand);

    /**
     * @param GeoPositionDemandInterface $demandPosition
     * @param string $table
     * @param string $alias
     * @return mixed
     */
    public function getDistanceSqlField(GeopositionDemandInterface $demandPosition, $table, $alias = 'distance');


    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition);


}