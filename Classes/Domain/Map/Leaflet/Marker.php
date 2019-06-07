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

use Bzga\BzgaBeratungsstellensuche\Domain\Map\MarkerInterface;
use Bzga\BzgaBeratungsstellensuche\Domain\Map\PopUpInterface;
use Netzmacht\LeafletPHP\Definition\Type\ImageIcon;
use Netzmacht\LeafletPHP\Definition\UI\Marker as LeafletMarker;

final class Marker implements MarkerInterface
{
    /**
     * @var LeafletMarker
     */
    private $marker;

    public function __construct(string $identifier, Coordinate $coordinate)
    {
        $this->marker = new LeafletMarker($identifier, $coordinate->getCoordinate());
    }

    /**
     * @return LeafletMarker
     */
    public function getMarker(): LeafletMarker
    {
        return $this->marker;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->marker->setOptions($options);
    }

    /**
     * @param string $iconPath
     */
    public function addIconFromPath(string $iconPath)
    {
        $this->marker->setIcon(new ImageIcon('icon', $iconPath));
    }

    /**
     * @param PopUpInterface $popUp
     * @param string $content
     */
    public function addPopUp(PopUpInterface $popUp, string $content)
    {
        $this->marker->bindPopup($popUp->getPopUp());
        $this->marker->setPopupContent($content);
    }
}
