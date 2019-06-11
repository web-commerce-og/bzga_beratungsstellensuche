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
use Netzmacht\LeafletPHP\Value\LatLng;

final class Coordinate implements CoordinateInterface
{
    /**
     * @var LatLng
     */
    private $coordinate;

    public function __construct(float $latitude, float $longitude)
    {
        $this->coordinate = new LatLng($latitude, $longitude);
    }

    /**
     * @return LatLng
     */
    public function getCoordinate(): LatLng
    {
        return $this->coordinate;
    }
}
