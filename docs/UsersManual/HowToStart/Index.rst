.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _howToStart:

Der Anfang
==========

.. only:: html

.. contents::
        :local:
        :depth: 1

.. _howToStartCreateRecords:

Datensätze erstellen oder importieren
-------------------------------------
Um überhaupt Beratungsstellen im Frontend darstellen zu können, müssen diese zunächst im Backend vorhanden sein.

#. Erstellen Sie einen neuen Seitenordner oder verwenden Sie einen bestehenden.

#. Sie können jetzt entweder neue Datensätze auf die bekannte Art und Weise im Backend manuell anlegen oder Sie nutzen die bereitgestellte XML-Schnittstelle zum Import der Beratungsstellen über die Website http://www.bzga-rat.de/adm.

#. Für die Verwendung der XML-Schnittstelle müssen Sie zunächst einen Task im Planer anlegen. Sehe hierzu auch: :ref:`installationTask`.

.. _howToStartAddPlugin:

Die Plugins einbinden und einrichten
------------------------------------
Für die Suche und Darstellung der Listen- und Detailansicht der Beratungsstellen müssen Sie das entsprechende Plugin auf den jeweiligen Seiten hinterlegen.

Detailseite
^^^^^^^^^^^

#. Legen Sie einen neue Seite "Detail" (oder einem frei wählbaren anderen Namen) im Seitenbaum an. Die Seite sollte als im Menü verborgen angelegt werden.
   Auf dieser Seite hinterlegen Sie das Plugin "Beratungsstellen". Unter dem "Reiter" Einstellungen wählen Sie im Feld Ansicht "Detailansicht" aus.

#. Unter dem Reiter "Erweiterte Einstellungen" können Sie optional eine Seite für zurück und eine Seite für die Detailansicht anderer Beratungsstellen hinterlegen.
   Diese beiden Einstellungen können aber auch global über die TypoScript Eigenschaften definiert werden und müssen nicht im Plugin selbst hinterlegt werden.
   Sieher hierzu:

#. Unter dem Reiter "Template" kann optional ein hinterlegtes Layout ausgewählt werden. Nähere Informationen hierzu befinden Sich im Bereich: :ref:`extendFlexformsLayout`.

#. Speichern Sie nach erfolgreicher Konfiguration das Plugin-Inhaltselement ab.
   
Listenansicht
^^^^^^^^^^^^^

#. Legen Sie einen neue Seite mit der Bezeichnung "Liste" (oder einem frei wählbaren anderen Namen) im Seitenbaum an und hinterlegen Sie dort ebenfalls ein Plugin vom Typ "Beratungsstellen".

#. Die bereits vorausgewählte Ansicht "Listenansicht" kann bestehen bleiben.

#. Sie können hier optional im Feld "Startingpoint" den Ordner angeben in dem die Beratungsstellen abgespeichert wurden. Dies kann aber auch global über die TypoScript-Eigenschaften definiert werden.

#. Unter dem Reiter "Erweiterte Einstellungen" können Sie zusätzliche einige lokale Angaben im Plugin vornehmen. Diese Angaben können aber auch global über die TypoScript-Eigenschaften gesetzt werden.

#. Speichern Sie nach erfolgreicher Konfiguration das Plugin-Inhaltselement ab.

Formularansicht
^^^^^^^^^^^^^^^

Sollten Sie zusätzlich zur Listen- und Detailansicht eine vorgeschaltete Formularansichtsseite benötigen, dann führen Sie bitte folgende Schritte aus:

#. Legen Sie eine neue Seite mit der Bezeichnung "Formular" (oder einem frei wählbaren anderen Namen) im Seitenbaum an und hinterlegen Sie dort ein Plugin vom Typ "Beratungsstellen".

#. Unter dem "Reiter" Einstellungen wählen Sie im Feld Ansicht "Formular" aus. Alle möglichen Konfigurationsoptionen sollten bereits aus den vorher beschriebenen Ansichten bekannt sein.


.. hint:: Es wird vermutlich gewünscht sein, dass sich auf allen Seiten Ihres Webauftrittes ein Suchformular für die Beratungsstellen befindet. Für eine mögliche Lösung dieser Anforderung schauen Sie sich bitte den Bereich :ref:`integrationWithTypoScriptPlugin` an.
