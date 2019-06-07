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

use Bzga\BzgaBeratungsstellensuche\Domain\Map\PopUpInterface;

final class PopUp implements PopUpInterface
{

    /**
     * @var \Netzmacht\LeafletPHP\Definition\UI\Popup
     */
    private $popUp;

    /**
     * PopUp constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->popUp = new \Netzmacht\LeafletPHP\Definition\UI\Popup($identifier);
    }

    /**
     * @return \Netzmacht\LeafletPHP\Definition\UI\Popup
     */
    public function getPopUp(): \Netzmacht\LeafletPHP\Definition\UI\Popup
    {
        return $this->popUp;
    }
}
