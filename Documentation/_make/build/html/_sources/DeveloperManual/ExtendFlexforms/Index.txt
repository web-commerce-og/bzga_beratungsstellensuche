.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _extendFlexforms:

Flexforms erweitern
-------------------
Einige Felder der Flexform-Konfiguration können mit Hilfe von Hooks erweitert werden.

.. only:: html

   .. contents::
        :local:
        :depth: 1

.. _extendFlexformsForms:

Formularfelder ergänzen
^^^^^^^^^^^^^^^^^^^^^^^
Dieses Feld ist dazu da, damit der Redakteur bei der Konfiguration die Möglichkeit hat, die Anzeige der Formularfelder gezielt und individuell steuern zu können.
Als Entwickler können wir hier, wenn alle Vorbereitungen wie Template und Datenbanklogik dafür vorbereitet wurden, neue Felder für die Auswahl ergänzen.
Dafür muss in der ext_localconf.php oder LocalConfiguration.php folgender Aufruf erstellt werden:

.. code-block:: php

   <?php
   \BZgA\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::addAdditionalFormField(array('LLL:EXT:your_extension_key/path_to_locallang_file:label', 'fieldname'));

.. _extendFlexformsLayout:

Layoutauswahl ergänzen
^^^^^^^^^^^^^^^^^^^^^^
Sollte es für die Frontenddarstellung unterschiedliche Layoutausgaben geben, kann dies über die Bereitstellung von Layouttemplates ermöglicht werden.
Um weitere Layouts bereitzustellen, muss folgende Angabe in der ext_localconf.php oder LocalConfiguration.php gemacht werden:

.. code-block:: php

    <?php
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['bzga_beratungsstellensuche']['templateLayouts']['myext'] = array('My Title', 'my value');

Im Template erfolgt der Zugriff auf die Angabe folgendermaßen:
:code:`{settings.templateLayout}` und kann beispielsweise in einer Fallunterscheidung (Condition) verwendet werden.
