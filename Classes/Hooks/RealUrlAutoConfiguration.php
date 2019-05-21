<?php

namespace Bzga\BzgaBeratungsstellensuche\Hooks;

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
        return array_merge_recursive(
            $params['config'],
            [
                'postVarSets' => [
                    '_DEFAULT' => [
                        'beratungsstelle' => [
                            [
                                'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[action]',
                            ],
                            [
                                'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[controller]',
                            ],
                            [
                                'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[entry]',
                                'lookUpTable' => [
                                    'table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'addWhereClause' => ' AND NOT deleted',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-',
                                    ],
                                    'languageGetVar' => 'L',
                                    'languageExceptionUids' => '',
                                    'languageField' => 'sys_language_uid',
                                    'transOrigPointerField' => 'l10n_parent',
                                    'autoUpdate' => 1,
                                    'expireDays' => 180,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
