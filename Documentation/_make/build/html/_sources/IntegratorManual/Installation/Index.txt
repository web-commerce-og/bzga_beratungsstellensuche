.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _installation:

.. _installationInstallation:

Installation
============
Diese Erweiteurng wird wie jede andere Erweiterung des TYPO3 CMS installiert:

#. Wechseln Sie zum Modul **Extension Manager**.

#. Installieren Sie die Erweiterung über den Upload-Button im Extension Manager.

#. Führen Sie nach der Installation das mitgelieferte Update-Skript der Erweiterung innerhalb des Extension Manager aus.

.. _installationStatus:

Status
------

#. Wechseln Sie zum Modul **Berichte**. Wählen Sie im oberen Select-Menü den Punkt **Status Report** aus. Hier sollte unter dem key *bzgaberatungsstellensuche* alles grün sein.
   Dies ist wichtig, damit externe Services wie der XML-Importer oder die GoogleMaps-Api angesprochen werden können.


.. _installationTask:

Planer-Task
-----------
Möchten Sie die XML-Schittstelle von bzga-rat.de nutzen, dann legen Sie im Modul **Planer** einen neuen **Task** an.

#. Wechseln Sie zum Modul **Planer**.

#. Legen Sie einen neuen **Task** mit folgenden Einstellungen an:

    * Klasse: Extbase-CommandController-Task
    * Typ: wiederkehrend
    * Start: 13:32 26-10-2016 (z.B.)
    * Ende: 13:32 26-10-2099 (z.B.)
    * Häufigkeit: \*\/15 \* \* \* \* (alle 15 Minuten, z.B.)
    * CommandController Command: BzgaBeratungsstellensuche Import: ImportFromUrl

#. Als Url geben Sie bitte die Ihnen vorliegende Url zur XML-Datei an und im Feld **pid** tragen Sie den Ordner zum Abspeichern der Beratungsstellen ein.

.. _installationTypoScript:

TypoScript
----------

Für die korrekte Verwendung der Erweiterung müssen Sie zunächst das mitgelieferte Basis TypoScript inkludieren.

#. Wechseln Sie zur **Root-Seite** Ihrer Installation.

#. Wechseln Sie zum Modul **Template** und wählen Sie im Auswahl-Menü *Info/Modify* aus.

#. Klicken Sie den Link **Edit the whole template record** und wechseln Sie zum Reiter *Enthält*.

#. Wählen Sie **Beratungsstellensuche (bzga_beratungsstellensuche)** im Feld *Statische Templates einschließen (aus Erweiterungen):* aus.