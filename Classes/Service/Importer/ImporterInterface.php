<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Importer;

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

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
interface ImporterInterface
{

    /**
     * @param $file
     * @param int $pid
     * @return mixed
     */
    public function importFromFile($file, $pid = 0);

    /**
     * @param $url
     * @param int $pid
     * @return mixed
     */
    public function importFromUrl($url, $pid = 0);

    /**
     * @param $content
     * @param int $pid
     * @return mixed
     */
    public function import($content, $pid = 0);

}