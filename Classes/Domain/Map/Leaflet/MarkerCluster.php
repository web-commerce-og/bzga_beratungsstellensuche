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

use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerClusterInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerInterface;
use Netzmacht\LeafletPHP\Plugins\MarkerCluster\MarkerClusterGroup;

final class MarkerCluster implements MarkerClusterInterface
{
    /**
     * @var \Netzmacht\LeafletPHP\Plugins\MarkerCluster\MarkerClusterGroup
     */
    private $markerCluster;

    public function __construct(string $identifier)
    {
        $this->markerCluster = new MarkerClusterGroup($identifier);
    }

    public function getMarkerCluster(): MarkerClusterGroup
    {
        return $this->markerCluster;
    }

    public function addMarker(MarkerInterface $marker): void
    {
        $this->markerCluster->addLayer($marker->getMarker());
    }

    public function setOptions(array $options): void
    {
        $this->markerCluster->setOptions($options);
    }
}
