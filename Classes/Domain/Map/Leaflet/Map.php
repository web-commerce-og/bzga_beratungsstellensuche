<?php
declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Domain\Map\Leaflet;

/*
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

use Bzga\BzgaBeratungsstellensuche\Domain\Map\CoordinateInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MapInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerInterface;
use Netzmacht\LeafletPHP\Definition\Map as LeafletMap;

final class Map implements MapInterface
{
    /**
     * @var LeafletMap
     */
    private $map;

    public function __construct(LeafletMap $map)
    {
        $this->map = $map;
    }

    public function getMap(): LeafletMap
    {
        return $this->map;
    }

    /**
     * @param mixed $value
     */
    public function setOption(string $key, $value): void
    {
        $this->map->setOption($key, $value);
    }

    public function setCenter(CoordinateInterface $coordinate): void
    {
        $this->map->setCenter($coordinate->getCoordinate());
    }

    public function addMarker(MarkerInterface $marker): void
    {
        /** @var \Netzmacht\LeafletPHP\Definition\UI\Marker $leafletMarker */
        $leafletMarker = $marker->getMarker();
        $leafletMarker->addTo($this->map);
        $this->map->addLayer($leafletMarker);
    }
}
