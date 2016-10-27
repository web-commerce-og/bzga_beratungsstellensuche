.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _hooksSignals:

Signals & Hooks
===============
Einige bereitgestellte Hooks und Signals können zur Anpassung der Erweiterung genutzt werden.
Nach Bedarf können gerne weitere Hooks und Signals bereitgestellt werden. Setzen Sie sich diesbezüglich gerne mit uns in Verbindung: :ref:`support`.

.. only:: html

	.. contents::
		:local:
		:depth: 1

.. _hooksSignalsSignals:

Signals
-------
Alle bereitgestellten Signals können in der Datei ``Classes/Events.php`` der Erweiterung bzga_beratungsstellensuche eingesehen werden und sind ausreichend kommentiert.

Beispiel
^^^^^^^^
Als Beispiel möchten wir für die Formularansicht eine weitere Variable der View hinzufügen.

.. code-block:: php

		<?php
		// Dies ist der Code-Abschnitt in der Datei Classes/Controller/EntryController.php
		$assignedViewValues = array(
			'demand' => $demand,
			'kilometers' => $kilometers,
			'categories' => $categories,
			'countryZonesGermany' => $countryZonesGermany
		);
		$this->emitActionSignal(Events::FORM_ACTION_SIGNAL, $assignedViewValues)

Um dieses Signal zu nutzen, erstellen Sie einen Slot in Ihrer eigenen Erweiterung. Dafür wird ein Eintrag in Ihrer ``ext_localconf.php`` benötigt:

.. code-block:: php

	<?php
	/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

	// Extend form view
	$signalSlotDispatcher->connect(
		\BZgA\BzgaBeratungsstellensuche\Controller\EntryController::class,
		\BZgA\BzgaBeratungsstellensuche\Events::FORM_ACTION_SIGNAL,
		\YOUR_VENDOR\YOUR_EXTKEY\Slots\EntryController::class,
		'formAction'
	);

Ihr Slot könnte dann wie folgt aussehen:

.. code-block:: php

	<?php

	namespace YOUR_VENDOR\YOUR_EXTKEY\Slots;
	class EntryController
	{
		public function listAction($variables)
		{
			$variables = array_merge($variables, array('myVariable' => 'myValue'));
			return array(
			'extendedVariables' => $variables,
			);
		}
	}

.. hint:: Schauen Sie sich bitte die Datei Classes/Events.php in Ruhe an. Sie werden darüber schnell erfahren, welche Teile in der Erweiterung flexibel erweitert werden können.

Hooks
-----
Es gibt aktuell verschiedenen Stellen an denen statt Signals Hooks zum Einsatz kommen.
Für die Erweiterung der Flexforms haben wir bereits zwei Hooks im Einsatz gesehen. Siehe hierzu: :ref:`extendFlexforms`.

Für das Frontend gibt es weitere interessante Hooks.

Domain/Repository/EntryRepository.php findDemanded
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Dieser Hook ermöglicht die Abfrage zur Filterung der Einträge um seine eigene Logik zu erweitern.

Beispiel
""""""""
Dieses Beispiel ergänzt die Abfrage um eine weitere Bedingung, so dass nur Einträge angezeigt werden, die das Keyword "unabhängig" enthalten.


Als Erstes muss der Hook in der Datei ``ext_localconf.php`` erstellt werden:

.. code-block:: php

	<?php

	$GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['Domain/Repository/EntryRepository.php']['findDemanded'][$_EXTKEY]
		= 'YOUR_VENDOR\\YOUR_EXTKEY\\Hooks\\Repository->modify';

Jetzt erstellen Sie die Datei ``Classes/Hooks/Repository.php``:

.. code-block:: php

	<?php

	namespace YOUR_VENDOR\YOUR_EXTKEY\Hooks;

	use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
	use TYPO3\CMS\Extbase\Persistence\QueryInterface;

	class Repository
	{
		public function modify(array $params)
		{
			$query = $params['query'];
			/* @var $query QueryInterface */
			$constraints = &$params['constraints'];
			/* @var $constraints array */
			$demand = $params['demand'];
			/* @var $demand Demand */
			$constraints[] = $query->like('keywords', '%unabhängig%');
		}
	}

.. hint:: Um die bereitgestellten Hooks ausfindig zu machen, können Sie bspw. einmal folgenden Befehl ausführen: **grep -n -C 5 "\['EXT'\]\['bzga_beratungsstellensuche'\]" -r bzga_beratungsstellensuche/Classes/**
.. hint:: Bitte passen Sie den Vendor-Präfix und den Extension-Key an ihre Gegebenheiten an.
