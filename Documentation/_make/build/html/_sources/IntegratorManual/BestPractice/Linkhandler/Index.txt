.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _linkhandler:

Linkhandler Konfiguration
-------------------------
Sollte die Extension **linkhandler** in Ihrer Installation verwendet werden, wird automatisch von der Erweiterung bzga_beratungsstellensuche
ein Basis-Setup in der TSConfig inkludiert.

Die Extension **linkhandler** ermöglicht die Verlinkung von beliebigen Datensätzen innerhalb von Textfeldern.
Nähere Information entnehmen Sie bitte hier: https://github.com/Intera/typo3-extension-linkhandler

Dieses Setup befindet sich in der Datei: ``EXT:bzga_beratungsstellensuche/Configuration/TsConfig/Page/mod.linkhandler.txt``

Damit die Verlinkungen auch im Frontend wirksam werden, muss ein weiteres **statisches TypoScript-Template** in Ihrer Root-Seite inkludiert werden.
Dieses Template heißt: **Beratungsstellensuche - Linkhandler**