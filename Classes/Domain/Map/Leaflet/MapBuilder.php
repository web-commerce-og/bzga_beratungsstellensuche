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
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MapBuilderInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MapInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerClusterInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\PopUpInterface;
use Netzmacht\JavascriptBuilder\Builder;
use Netzmacht\JavascriptBuilder\Encoder\ChainEncoder;
use Netzmacht\JavascriptBuilder\Encoder\JavascriptEncoder;
use Netzmacht\JavascriptBuilder\Encoder\MultipleObjectsEncoder;
use Netzmacht\JavascriptBuilder\Flags;
use Netzmacht\JavascriptBuilder\Output;
use Netzmacht\JavascriptBuilder\Symfony\EventDispatchingEncoder;
use Netzmacht\LeafletPHP\Definition\Map as LeafletMap;
use Netzmacht\LeafletPHP\Definition\Raster\TileLayer;
use Netzmacht\LeafletPHP\Encoder\ControlEncoder;
use Netzmacht\LeafletPHP\Encoder\GroupEncoder;
use Netzmacht\LeafletPHP\Encoder\MapEncoder;
use Netzmacht\LeafletPHP\Encoder\RasterEncoder;
use Netzmacht\LeafletPHP\Encoder\TypeEncoder;
use Netzmacht\LeafletPHP\Encoder\UIEncoder;
use Netzmacht\LeafletPHP\Encoder\VectorEncoder;
use Netzmacht\LeafletPHP\Leaflet;
use Netzmacht\LeafletPHP\Plugins\FullScreen\FullScreenControl;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class MapBuilder implements MapBuilderInterface
{

    /**
     * @param EventDispatcher $dispatcher
     *
     * @return callable
     */
    private function mapFactory(EventDispatcher $dispatcher): callable
    {
        return static function (Output $output) use ($dispatcher) {
            $encoder = new ChainEncoder();
            $encoder
                ->register(new MultipleObjectsEncoder())
                ->register(new EventDispatchingEncoder($dispatcher))
                ->register(new JavascriptEncoder($output, JSON_UNESCAPED_SLASHES));

            return $encoder;
        };
    }

    /**
     * @return EventDispatcher
     */
    private function dispatcherFactory(): EventDispatcher
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new ControlEncoder());
        $dispatcher->addSubscriber(new GroupEncoder());
        $dispatcher->addSubscriber(new MapEncoder());
        $dispatcher->addSubscriber(new RasterEncoder());
        $dispatcher->addSubscriber(new TypeEncoder());
        $dispatcher->addSubscriber(new UIEncoder());
        $dispatcher->addSubscriber(new VectorEncoder());

        return $dispatcher;
    }

    /**
     * @param MapInterface $map
     *
     * @return string
     */
    public function build(MapInterface $map): string
    {
        $dispatcher = $this->dispatcherFactory();
        $mapBuilder = new Leaflet(new Builder($this->mapFactory($dispatcher)), $dispatcher, [], JSON_UNESCAPED_SLASHES ^ Flags::BUILD_STACK);

        return $mapBuilder->build($map->getMap());
    }

    /**
     * @param string $mapId
     *
     * @return MapInterface
     */
    public function createMap(string $mapId): MapInterface
    {
        $map = new LeafletMap($mapId, $mapId);
        $map->setZoom(17);
        $map->setOption('fullscreenControl', true);

        $layer = new TileLayer('OpenStreetMap_Mapnik', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
        $layer->setAttribution('&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors');
        $layer->addTo($map);
        $map->addLayer($layer);

        $fullScreenControl = new FullScreenControl('fullscreen');
        $map->addControl($fullScreenControl);

        return new Map($map);
    }

    /**
     * @param string $identifier
     * @param CoordinateInterface $coordinate
     *
     * @return MarkerInterface
     */
    public function createMarker(string $identifier, CoordinateInterface $coordinate): MarkerInterface
    {
        return new Marker($identifier, $coordinate);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return CoordinateInterface
     */
    public function createCoordinate(float $latitude, float $longitude): CoordinateInterface
    {
        return new Coordinate($latitude, $longitude);
    }

    /**
     * @param string $identifier
     *
     * @return PopUpInterface
     */
    public function createPopUp(string $identifier): PopUpInterface
    {
        return new PopUp($identifier);
    }

    /**
     * @param string $identifier
     *
     * @param MapInterface $map
     *
     * @return MarkerClusterInterface
     */
    public function createMarkerCluster(string $identifier, MapInterface $map): MarkerClusterInterface
    {
        $markerCluster = new MarkerCluster($identifier);
        $markerCluster->getMarkerCluster()->addTo($map->getMap());
        $map->getMap()->addLayer($markerCluster->getMarkerCluster());
        return $markerCluster;
    }
}
