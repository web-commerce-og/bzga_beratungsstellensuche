<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Geolocation\Decorator;

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

use BZgA\BzgaBeratungsstellensuche\Service\Geolocation\GeolocationServiceInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as CacheInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeopositionInterface;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\GeoPositionDemandInterface;
use BZgA\BzgaBeratungsstellensuche\Factories\CacheFactory;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class GeolocationServiceCacheDecorator implements GeolocationServiceInterface
{
    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var GeolocationServiceInterface
     */
    protected $geolocationService;

    /**
     * GeolocationServiceCacheDecorator constructor.
     * @param \BZgA\BzgaBeratungsstellensuche\Service\Geolocation\GeolocationService $geolocationService
     */
    public function __construct(GeolocationServiceInterface $geolocationService)
    {
        $this->geolocationService = $geolocationService;
        $this->cache = CacheFactory::createInstance();
    }

    /**
     * @param Demand $demand
     */
    public function findAddressByDemand(Demand $demand)
    {
        $cacheIdentifier = sha1($demand->getLocation());
        if (false === $this->cache->has($cacheIdentifier)) {
            $address = $this->geolocationService->findAddressByDemand($demand);
            $this->cache->set($cacheIdentifier, serialize($address));
        }

        $address = unserialize($this->cache->get($cacheIdentifier));

        return $address;
    }


    /**
     * @param GeoPositionDemandInterface $demandPosition
     * @param string $table
     * @param string $alias
     * @return mixed
     */
    public function getDistanceSqlField(GeopositionDemandInterface $demandPosition, $table, $alias = 'distance')
    {
        return $this->geolocationService->getDistanceSqlField($demandPosition, $table, $alias);
    }

    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition)
    {
        return $this->geolocationService->calculateDistance($demandPosition, $locationPosition);
    }


}