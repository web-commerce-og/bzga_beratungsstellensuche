<?php
declare(strict_types=1);


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

    private $markerCluster;

    public function __construct(string $identifier)
    {
        $this->markerCluster = new MarkerClusterGroup($identifier);
    }


    /**
     * @return mixed
     */
    public function getMarkerCluster()
    {
        return $this->markerCluster;
    }

    /**
     * @param MarkerInterface $marker
     *
     * @return mixed
     */
    public function addMarker(MarkerInterface $marker)
    {
        $this->markerCluster->addLayer($marker->getMarker());
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->markerCluster->setOptions($options);
    }
}
