<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Geolocation\Decorator;

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
use Bzga\BzgaBeratungsstellensuche\Factories\CacheFactory;
use Bzga\BzgaBeratungsstellensuche\Service\Geolocation\GeolocationServiceInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface as CacheInterface;

/**
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
     *
     * @param GeolocationServiceInterface $geolocationService
     * @param CacheFactory $cacheFactory
     */
    public function __construct(GeolocationServiceInterface $geolocationService, CacheFactory $cacheFactory)
    {
        $this->geolocationService = $geolocationService;
        $this->cache              = $cacheFactory->createInstance();
    }

    /**
     * @param Demand $demand
     *
     * @return \Geocoder\Model\Address|null
     */
    public function findAddressByDemand(Demand $demand)
    {
        $cacheIdentifier = sha1($demand->getAddressToGeocode());

        if ($this->cache->has($cacheIdentifier)) {
            return unserialize($this->cache->get($cacheIdentifier));
        }

        $address = $this->geolocationService->findAddressByDemand($demand);
        $this->cache->set($cacheIdentifier, serialize($address));

        return $address;
    }

    /**
     * @param GeoPositionDemandInterface $demandPosition
     * @param string $table
     * @param string $alias
     *
     * @return mixed
     */
    public function getDistanceSqlField(GeopositionDemandInterface $demandPosition, $table, $alias = 'distance')
    {
        return $this->geolocationService->getDistanceSqlField($demandPosition, $table, $alias);
    }

    /**
     * @param GeopositionInterface $demandPosition
     * @param GeopositionInterface $locationPosition
     *
     * @return mixed
     */
    public function calculateDistance(GeopositionInterface $demandPosition, GeopositionInterface $locationPosition)
    {
        return $this->geolocationService->calculateDistance($demandPosition, $locationPosition);
    }
}
