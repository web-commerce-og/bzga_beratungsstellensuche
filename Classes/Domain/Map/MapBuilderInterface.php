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
    /**
     * @param MapInterface $map
     *
     * @return string
     */
    public function build(MapInterface $map): string;

    /**
     * @param string $mapId
     *
     * @return MapInterface
     */
    public function createMap(string $mapId): MapInterface;

    /**
     * @param string $identifier
     * @param CoordinateInterface $coordinate
     *
     * @return MarkerInterface
     */
    public function createMarker(string $identifier, CoordinateInterface $coordinate): MarkerInterface;

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return CoordinateInterface
     */
    public function createCoordinate(float $latitude, float $longitude): CoordinateInterface;

    /**
     * @param string $identifier
     *
     * @return PopUpInterface
     */
    public function createPopUp(string $identifier): PopUpInterface;
}
