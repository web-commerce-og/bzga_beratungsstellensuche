<?php

namespace BZgA\BzgaBeratungsstellensuche\Hooks;

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
class RealUrlAutoConfiguration
{

    /**
     * Generates additional RealURL configuration and merges it with provided configuration
     *
     * @param array $params Default configuration
     * @return array Updated configuration
     */
    public function addConfig($params)
    {

        return array_merge_recursive($params['config'], array(
                'postVarSets' => array(
                    '_DEFAULT' => array(
                        'beratungsstelle' => array(
                            array(
                                'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[entry]',
                                'lookUpTable' => array(
                                    'table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => array(
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }
}