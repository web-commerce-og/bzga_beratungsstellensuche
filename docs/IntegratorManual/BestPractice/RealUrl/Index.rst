.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _realurl:

RealURL Konfiguration
---------------------

Um die Urls mittels der Erweiterung **RealURL** ansprechender zu gestalten können Sie die im Folgenden aufgezeigten Konfigurationen in Ihrem
Projekt integrieren.

Basis Setup
^^^^^^^^^^^

Die einfachste Art und Weise besteht darin, den folgenden Code in den *postVarSets/_DEFAULT* Abschnitt zu kopieren:

.. code-block:: php

	// EXT:bzga_beratungsstelle start
	'beratungsstelle' => array(
		array(
			'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[action]',
		),
		array(
			'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[controller]',
		),
		array(
			'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[entry]',
			'lookUpTable' => array(
				'table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
				'id_field' => 'uid',
				'alias_field' => 'title',
				'addWhereClause' => ' AND NOT deleted',
				'useUniqueCache' => 1,
				'useUniqueCache_conf' => array(
					'strtolower' => 1,
					'spaceCharacter' => '-',
				),
				'languageGetVar' => 'L',
				'languageExceptionUids' => '',
				'languageField' => 'sys_language_uid',
				'transOrigPointerField' => 'l10n_parent',
				'autoUpdate' => 1,
				'expireDays' => 180,
			),
		),
	),
	// EXT:bzga_beratungsstelle end


Erweitertes Beispiel
^^^^^^^^^^^^^^^^^^^^

Eine weitere Möglichkeit besteht darin, dass Sie die Konfiguration in den Abschnitt *fixedPostVars/_DEFAULT* anlegen:

.. code-block:: php

	<?php

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'][_DEFAULT] = array(
		'fixedPostVars' => array(
			'entryDetailConfiguration' => array(
				array(
					'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[action]',
					'valueMap' => array(
						'show' => '',
					),
					'noMatch' => 'bypass'
				),
				array(
					'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[controller]',
					'valueMap' => array(
						'Entry' => '',
					),
					'noMatch' => 'bypass'
				),
				array(
					'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[entry]',
					'lookUpTable' => array(
						'table' => 'tx_bzgaberatungsstellensuche_domain_model_entry',
						'id_field' => 'uid',
						'alias_field' => 'title',
						'addWhereClause' => ' AND NOT deleted',
						'useUniqueCache' => 1,
						'useUniqueCache_conf' => array(
							'strtolower' => 1,
							'spaceCharacter' => '-'
						),
						'languageGetVar' => 'L',
						'languageExceptionUids' => '',
						'languageField' => 'sys_language_uid',
						'transOrigPointerField' => 'l10n_parent',
						'autoUpdate' => 1,
						'expireDays' => 180,
					)
				)
			),
			'70' => 'entryDetailConfiguration',
		),
		'postVarSets' => array(
			'_DEFAULT' => array(
				'controller' => array(
					array(
						'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[action]',
						'noMatch' => 'bypass'
					),
					array(
						'GETvar' => 'tx_bzgaberatungsstellensuche_pi1[controller]',
						'noMatch' => 'bypass'
					)
				),
			),
		),

	);