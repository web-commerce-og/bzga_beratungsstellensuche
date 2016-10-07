<?php

namespace BZgA\BzgaBeratungsstellensuche\Hooks;


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