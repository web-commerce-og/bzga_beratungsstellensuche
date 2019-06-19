.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. _integrationWithTypoScript:

Integration mittels TypoScript
------------------------------

Diese Seite gibt Ihnen einige Beispiele wie Sie die Erweiterung bzga_beratungsstellensuche mittels TypoScript in Ihre Seite integrieren können.

.. only:: html

    .. contents::

.. _integrationWithTypoScriptPlugin:

Plugin mittels TypoScript einbinden
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Möchten Sie zum Beispiel die Formularansicht der Beratungsstellensuche auf jeder Seite Ihres Auftrittes anzeigen, dann benutzen Sie folgendes TypoScript:

.. code-block:: typoscript

	lib.beratungsstellensuche = USER
	lib.beratungsstellensuche {
		userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
		extensionName = BzgaBeratungsstellensuche
		pluginName = Pi1
		vendorName = BZgA

		switchableControllerActions {
			Entry {
				1 = form
			}
		}

		settings < plugin.tx_bzgaberatungsstellensuche.settings
		settings {
			formFields = location,kilometers
			listPid = 12345
		}
	}

	[globalVar = GP:tx_bzgaberatungsstellensuche_pi1|__trustedProperties = /\w+/ ]
		lib.beratungsstellensuche = USER_INT
	[global]

Jetzt können Sie das Objekt lib.beratungsstellensuche an gewünschter Stelle einbinden.


Beratungsstelle in der Breadcrumb aufnehmen
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Möchten Sie die Beratungsstelle in Ihrer Breadcrumb mit aufnehmen, benutzen Sie den folgenden TypoScrip-Code:

.. code-block:: typoscript

	lib.breadcrumb = COA
	lib.breadcrumb {
		stdWrap.wrap = <ul class="breadcrumb">|</ul>

		10 = HMENU
		10 {
			special = rootline
			#special.range =  1

			1 = TMENU
			1 {
				noBlur = 1

				NO = 1
				NO {
					wrapItemAndSub = <li>|</li>
					ATagTitle.field = subtitle // title
					stdWrap.htmlSpecialChars = 1
				}

				CUR <.NO
				CUR {
					wrapItemAndSub = <li class="active">|</li>
					doNotLinkIt = 1
				}
			}
		}

		# Add Beratungsstellen title if on single view
		20 = RECORDS
		20 {
			stdWrap.if.isTrue.data = GP:tx_bzgaberatungsstellensuche_pi1|entry
			dontCheckPid = 1
			tables = tx_bzgaberatungsstellensuche_domain_model_entry
			source.data = GP:tx_bzgaberatungsstellensuche_pi1|entry
			source.intval = 1
			conf.tx_bzgaberatungsstellensuche_domain_model_entry = TEXT
			conf.tx_bzgaberatungsstellensuche_domain_model_entry {
				field = title
				htmlSpecialChars = 1
			}
			stdWrap.wrap = <li>|</li>
			stdWrap.required = 1
		}
	}


Title-Tag für die Detailansicht anpassen
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Möchten Sie in der Detailansicht einer Beratungsstelle das Title-Tag anpassen, können Sie folgende 2 Wege gehen:

Nur mittels TypoScript
""""""""""""""""""""""

.. code-block:: typoscript

	[globalVar = GP:tx_bzgaberatungsstellensuche_pi1|entry > 0]

	config.noPageTitle = 2

	temp.title = RECORDS
	temp.title {
		dontCheckPid = 1
		tables = tx_bzgaberatungsstellensuche_domain_model_entry
		source.data = GP:tx_bzgaberatungsstellensuche_pi1|entry
		source.intval = 1
		conf.tx_bzgaberatungsstellensuche_domain_model_entry = TEXT
		conf.tx_bzgaberatungsstellensuche_domain_model_entry {
			field = title
			htmlSpecialChars = 1
		}
		wrap = <title>|</title>
	}
	page.headerData.1 >
	page.headerData.1 < temp.title

	[global]

ViewHelper benutzen
"""""""""""""""""""

.. code-block:: html

	<bzga:title>{entry.title}</bzga:title>




