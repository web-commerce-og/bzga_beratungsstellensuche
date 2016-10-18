<?php


namespace BZgA\BzgaBeratungsstellensuche\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use BZgA\BzgaBeratungsstellensuche\Events;
use SJBR\StaticInfoTables\Domain\Model\Country;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use BZgA\BzgaBeratungsstellensuche\Utility\Utility;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class EntryController extends ActionController
{

    const STYLESHEET_INCLUDE = '<link rel="stylesheet" type="text/css" media="%1$s" href="%2$s" />';
    const JAVASCRIPT_INCLUDE = '<script type="text/javascript" src="%1$s"></script>';
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\KilometerRepository
     * @inject
     */
    protected $kilometerRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Service\SessionService
     * @inject
     */
    protected $sessionService;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @return void
     */
    public function initializeAction()
    {
        // Add some additional files to the header
        if ($this->settings['additionalCssFile']) {
            $this->addHeaderData($this->settings['additionalCssFile']);
        }
        if ($this->settings['additionalJsFile']) {
            $this->addHeaderData($this->settings['additionalJsFile'], self::TYPE_JS);
        }

        if ($this->arguments->hasArgument('demand')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('demand')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
            $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, true);
            $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED, true);
            $propertyMappingConfiguration->forProperty('categories')->allowAllProperties();
            $propertyMappingConfiguration->allowCreationForSubProperty('categories');
            $propertyMappingConfiguration->allowModificationForSubProperty('categories');
            $this->emitInitializeActionSignal(array('propertyMappingConfiguration' => $propertyMappingConfiguration));
        }
    }


    /**
     * @return void
     */
    public function initializeFormAction()
    {
        $this->resetDemand();
        $this->addDemandRequestArgumentFromSession();
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @return void
     */
    public function formAction(Demand $demand = null)
    {
        if (!$demand instanceof Demand) {
            $demand = $this->objectManager->get(Demand::class);
        }
        $countryZonesGermany = $this->findCountryZonesForGermany();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $assignedViewValues = compact('demand', 'kilometers', 'categories', 'countryZonesGermany');
        $assignedViewValues = $this->emitActionSignal(Events::FORM_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    /**
     * @return void
     */
    public function initializeListAction()
    {
        $this->resetDemand();
        if (!$this->request->hasArgument('demand')) {
            $this->addDemandRequestArgumentFromSession();
        } else {
            $this->sessionService->writeToSession($this->request->getArgument('demand'));
        }
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @return void
     */
    public function listAction(Demand $demand = null)
    {
        if (!$demand instanceof Demand) {
            $demand = $this->objectManager->get(Demand::class);
        }
        $entries = $this->entryRepository->findDemanded($demand);
        $countryZonesGermany = $this->findCountryZonesForGermany();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $assignedViewValues = compact('entries', 'demand', 'kilometers', 'categories', 'countryZonesGermany');
        $assignedViewValues = $this->emitActionSignal(Events::LIST_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    /**
     * @return void
     */
    public function initializeShowAction()
    {
        $this->addDemandRequestArgumentFromSession();
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry $entry
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand $demand
     * @return void
     */
    public function showAction(Entry $entry = null, Demand $demand = null)
    {
        if (!$entry instanceof Entry) {
            $this->redirect('list', null, null, array(), $this->settings['listPid'], 0, 404);
        }
        $assignedViewValues = compact('entry', 'demand');
        $assignedViewValues = $this->emitActionSignal(Events::SHOW_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    /**
     * @return array
     */
    protected function findCountryZonesForGermany()
    {
        if (GeneralUtility::inList($this->settings['formFields'], 'countryZonesGermany')) {
            $country = new Country();
            $country->setIsoCodeNumber(276);

            return $this->countryZoneRepository->findByCountryOrderedByLocalizedName($country);
        }
    }

    /**
     * @param $signalArguments
     * @return void
     */
    protected function emitInitializeActionSignal($signalArguments)
    {
        $this->signalSlotDispatcher->dispatch(static::class, Events::INITIALIZE_ACTION_SIGNAL, $signalArguments);
    }

    /**
     * @param $signalName
     * @param array $assignedViewValues
     * @return mixed
     */
    protected function emitActionSignal($signalName, array $assignedViewValues)
    {
        $signalArguments = array();
        $signalArguments['extendedVariables'] = array();

        $additionalViewValues = $this->signalSlotDispatcher->dispatch(static::class, $signalName, $signalArguments);

        return array_merge($assignedViewValues, $additionalViewValues['extendedVariables']);
    }

    /**
     * @return void
     */
    private function addDemandRequestArgumentFromSession()
    {
        $demand = $this->sessionService->restoreFromSession();
        if ($demand) {
            $this->request->setArgument('demand', $demand);
        }
    }

    /**
     * @return void
     */
    private function resetDemand()
    {
        if ($this->request->hasArgument('reset')) {
            $this->sessionService->cleanUpSession();
            $this->request->setArgument('demand', null);
        }
    }

    /**
     * @param string $data
     * @param string("js", "css") $type
     * @param string $media
     * @return void
     */
    private function addHeaderData($data, $type = self::TYPE_CSS, $media = 'all')
    {
        $pathToFile = GeneralUtility::getFileAbsFileName($data);
        if (file_exists($pathToFile)) {
            $data = Utility::stripPathSite($pathToFile);
            switch ($type) {
                case self::TYPE_CSS:
                    $data = sprintf(self::STYLESHEET_INCLUDE, $media, $data);
                    break;
                case self::TYPE_JS:
                    $data = sprintf(self::JAVASCRIPT_INCLUDE, $data);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('The provided type %s is not allowed', $type));
                    break;
            }
            $key = $this->extensionName.md5($data);
            if (!isset($GLOBALS['TSFE']->register[$key])) {
                $GLOBALS['TSFE']->register[$key] = $data;
                $this->response->addAdditionalHeaderData($data);
            }
        }
    }

}