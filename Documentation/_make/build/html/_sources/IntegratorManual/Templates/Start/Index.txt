.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt


Templates ändern oder bearbeiten
================================

.. warning:: Die Erweiterung basiert auf der Template Engine **fluid**. Grundlegende Kenntnisse in Aufbau und Verwendung von fluid werden vorausgesetzt.



Den Pfad zu den Templates im setup anpassen
-------------------------------------------
Sie sollten niemals die Original-Templates der Erweiterung verändern. Diese werden bei einem Update ansonsten überschrieben.

Wie bei allen Extensions die auf extbase basieren, befinden sich die Template im Ordner ``Resources/Private/``.

Sollten Sie ein Template anpassen, kopieren Sie sich das entsprechende Template an die gewünschte Stelle.
Die Templates können dabei in einer eigenen Erweiterung liegen oder einfach im fileadmin (nicht zu empfehlen).

Der Einfachheit halber gehen wir im folgenden Beispiel davon aus, dass die Templates im fileadmin-Ordner abgelegt werden.

Der folgende Teil würde im **setup** Bereich Ihres TypoScripts stehen:

.. code-block:: typoscript

		plugin.tx_news {
			view {
				templateRootPaths >
				templateRootPaths {
					0 = EXT:bzga_beratungsstellensuche/Resources/Private/Templates/
					1 = fileadmin/templates/ext/bzga_beratungsstellensuche/Templates/
				}
				partialRootPaths >
				partialRootPaths {
					0 = EXT:bzga_beratungsstellensuche/Resources/Private/Partials/
					1 = fileadmin/templates/ext/bzga_beratungsstellensuche/Partials/
				}
				layoutRootPaths >
				layoutRootPaths {
					0 = EXT:bzga_beratungsstellensuche/Resources/Private/Layouts/
					1 = fileadmin/templates/ext/bzga_beratungsstellensuche/Layouts/
				}
			}
		}

Mit diesem TypoScript wird zunächst im fileadmin nachgesehen ob ein angefordertes Template, Partial oder Layout existiert.
Sollte dies nicht der Fall sein, kommt es zu einem Fallback auf das Original.

.. hint:: Bitte achten Sie auf die Plural-Schreibweise. Also z.B nicht templateRootPath sondern templateRootPaths.

Den Pfad zu den Templates über die constants anpassen
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Es gibt auch die Möglichkeit die Pfade über die  **constants** zu setzen.

.. code-block:: typoscript

	plugin.tx_news {
		view {
			templateRootPath = fileadmin/templates/ext/news/Templates/
			partialRootPath = fileadmin/templates/ext/news/Partials/
			layoutRootPath = fileadmin/templates/ext/news/Layouts/
		}
	}

