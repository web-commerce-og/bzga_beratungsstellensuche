.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _solr:

Solr Konfiguration
------------------

Sollte die Extension **solr** in Ihrer Installation verwendet werden, können Sie ein weiteres **statisches TypoScript-Template**
auf Ihrer Root-Seite inkludieren.

Dieses Template heißt: **Beratungsstellensuche - Solr**

Das Zusammenspiel mit solr beim XML-Import funktioniert einwandfrei, da beim Import-Vorgang die DataHandler-API genutzt wird und somit solr
bei Einfügen/Aktualisieren oder Löschen einer Beratungsstelle darüber in Kenntnis gesetzt wird.

.. note:: Sollten Sie neben solr auch die Erweiterung **solrgeo** nutzen, müssen Sie die Integration mit der Erweiterung **bzga_beratungsstellensuche** selbständig vornehmen. Wir freuen uns über ein Feedback bei erfolgreicher Integration.