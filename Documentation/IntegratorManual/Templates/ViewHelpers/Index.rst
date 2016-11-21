.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt


ViewHelpers
-----------

.. only:: html

Die Erweiterung liefert einige nützliche **ViewHelpers** mit, die entweder bereits in den bestehenden Templates verwendet werden oder von Ihnen
in Ihren Anpassungen genutzt werden könnten. Es sind hier nicht alle entwickelten ViewHelper aufgeführt, da einige lediglich für "interne Zwecke" genutzt werden.

Um die ViewHelper zu nutzen müssen Sie zunächst in den jeweiligen fluid-Dateien den namespace über folgende Anweisung importieren:

.. code-block:: html

    {namespace bzga=Bzga\BzgaBeratungsstellensuche\ViewHelpers}


MapViewHelper
=============
Der MapViewHelper generiert eine Karte auf Basis von GoogleMaps. Die Darstellung der Karte kann über Hooks und über individuell TS-Settings angepasst werden.

Klasse: ``Classes/ViewHelpers/Widget/MapViewHelper.php``

.. code-block:: html

    <bzga:widget.map demand="{demand}" entry="{entry}" settings="{settings}"/>

PaginateViewHelper
==================
Für den Seitenbrowser wurde der in fluid enthaltene PaginateViewHelper erweitert. Bitte also in allen Fällen im Kontext der Beratungsstellen diesen ViewHelper nutzen.

Klasse: ``Classes/ViewHelpers/Widget/PaginateViewHelper.php``

.. code-block:: html

    <bzga:widget.paginate as="paginatedEntries" demand="{demand}" objects="{entries}">
        <f:for each="{paginatedEntries}" as="entry">
            {entry}
        </f:for>
    </bzga:widget.paginate>

RoundViewHelper
===============
Der RoundViewHelper findet aktuell gemeinsam mit dem DistanceViewHelper Verwendung und rundet ein Float-Wert nach einem definierten Präzisionswert.

Klasse: ``Classes/ViewHelpers/Math/RoundViewHelper.php``

.. code-block:: html

    <bzga:math.round precision="2">{float}</bzga:math.round>

DistanceViewHelper
==================
Der DistanceViewHelper berechnet die Entfernung von zwei Objekten vom Typ GeopositionInterface.

Klasse: ``Classes/ViewHelpers/DistanceViewHelper.php``

.. code-block:: html

    <bzga:distance demandPosition="{demand}" location="{entry}" />

ImplodeViewHelper
=================
Der Implode-ViewHelper kann dazu verwendet werden, aus einem Array oder Traversable einen string zu generieren. Die Objekte innerhalb des Arrays oder Traversable müssen die __toString Methode implementieren.

Klasse: ``Classes/ViewHelpers/ImplodeViewHelper.php``

.. code-block:: html

    <bzga:implode glue=",">{array}</bzga:implode>

TranslateViewHelper
===================
Der TranslateViewHelper ist eine Erweiterung des fluid eigenen TranslateViewHelper und dient dazu die Lokalisierungslabels aus allen "registrierten Erweiterungen" automatisch zu ziehen.
Eine Erweiterung kann mittels des folgenden Aufrufs in der Datei ``ext_localconf.php`` registriert werden:

.. code-block:: php

    <?php

    \Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility::registerExtensionKey($_EXTKEY, $priority);

Klasse: ``Classes/ViewHelpers/TranslateViewHelper.php``

Bitte verwenden Sie in allen Dateien im Kontext der Beratungsstellensuche diesen TranslateViewHelper.

.. code-block:: html

    <bzga:translate key="telephone" />

TitleViewHelper
===============
Der TitleViewHelper ermöglicht das Überschreiben des Title-Tag auf der Webseite.

Klasse: ``Classes/ViewHelpers/TitleViewHelper.php``

.. code-block:: html

    <bzga:title>My new title</bzga:title>



