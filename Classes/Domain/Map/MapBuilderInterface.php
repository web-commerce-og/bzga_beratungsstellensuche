<?php
declare(strict_types = 1);

namespace Bzga\BzgaBeratungsstellensuche\Domain\Map;

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

interface MapBuilderInterface
{
    public function build(MapInterface $map): string;

    public function createMap(string $mapId): MapInterface;

    public function createMarker(string $identifier, CoordinateInterface $coordinate): MarkerInterface;

    public function createCoordinate(float $latitude, float $longitude): CoordinateInterface;

    public function createPopUp(string $identifier): PopUpInterface;

    public function createMarkerCluster(string $identifier, MapInterface $map): MarkerClusterInterface;
}
