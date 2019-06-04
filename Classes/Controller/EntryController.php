<?php


namespace Bzga\BzgaBeratungsstellensuche\Controller;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use Bzga\BzgaBeratungsstellensuche\Domain\Model\Entry;
use Bzga\BzgaBeratungsstellensuche\Events;
use Bzga\BzgaBeratungsstellensuche\Utility\Utility;
use SJBR\StaticInfoTables\Domain\Model\Country;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;

/**
 * @author Sebastian Schreiber
 */
class EntryController extends ActionController
{
    const GERMANY_ISOCODENUMBER = 276;

    /**
     * @var string
     */
    const STYLESHEET_INCLUDE = '<link rel="stylesheet" type="text/css" media="%1$s" href="%2$s" />';

    /**
     * @var string
     */
    const JAVASCRIPT_INCLUDE = '<script type="text/javascript" src="%1$s"></script>';

    /**
     * @var string
     */
    const TYPE_JS = 'js';

    /**
     * @var string
     */
    const TYPE_CSS = 'css';

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Repository\KilometerRepository
     * @inject
     */
    protected $kilometerRepository;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Service\SessionService
     * @inject
     */
    protected $sessionService;

    /**
     * @var \Bzga\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * The response which will be returned by this action controller
     *
     * @var \TYPO3\CMS\Extbase\Mvc\Web\Response
     * @api
     */
    protected $response;

    /**
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
            $propertyMappingConfiguration->setTypeConverterOption(
                PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                true
            );
            $propertyMappingConfiguration->setTypeConverterOption(
                PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED,
                true
            );
            $propertyMappingConfiguration->forProperty('categories')->allowAllProperties();
            $propertyMappingConfiguration->allowCreationForSubProperty('categories');
            $propertyMappingConfiguration->allowModificationForSubProperty('categories');
            $this->emitInitializeActionSignal(['propertyMappingConfiguration' => $propertyMappingConfiguration]);
        }
    }

    /**
     */
    public function initializeFormAction()
    {
        $this->resetDemand();
        $this->addDemandRequestArgumentFromSession();
    }

    /**
     * @param Demand $demand
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
     * @param Demand $demand
     *
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws InvalidQueryException
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
     */
    public function initializeShowAction()
    {
        $this->addDemandRequestArgumentFromSession();
    }

    /**
     * @param Entry $entry
     * @param Demand $demand
     *
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function showAction(Entry $entry = null, Demand $demand = null)
    {
        if (!$entry instanceof Entry) {
            // @TODO: Add possibility to hook into here.
            $this->redirect('list', null, null, [], $this->settings['listPid'], 0, 404);
        }
        $assignedViewValues = compact('entry', 'demand');
        $assignedViewValues = $this->emitActionSignal(Events::SHOW_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    /**
     * @return array
     */
    private function findCountryZonesForGermany()
    {
        if (false === GeneralUtility::inList($this->settings['formFields'], 'countryZonesGermany')) {
            return [];
        }
        $country = new Country();
        $country->setIsoCodeNumber(self::GERMANY_ISOCODENUMBER);

        return $this->countryZoneRepository->findByCountryOrderedByLocalizedName($country);
    }

    /**
     * @param array $signalArguments
     *
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    private function emitInitializeActionSignal(array $signalArguments)
    {
        $this->signalSlotDispatcher->dispatch(static::class, Events::INITIALIZE_ACTION_SIGNAL, $signalArguments);
    }

    /**
     * @param $signalName
     * @param array $assignedViewValues
     *
     * @return mixed
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    private function emitActionSignal($signalName, array $assignedViewValues)
    {
        $signalArguments = [];
        $signalArguments['extendedVariables'] = [];

        $additionalViewValues = $this->signalSlotDispatcher->dispatch(static::class, $signalName, $signalArguments);

        return array_merge($assignedViewValues, $additionalViewValues['extendedVariables']);
    }

    /**
     */
    private function addDemandRequestArgumentFromSession()
    {
        $demand = $this->sessionService->restoreFromSession();
        if ($demand) {
            $this->request->setArgument('demand', $demand);
        }
    }

    /**
     * @throws InvalidArgumentNameException
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
            $typoScriptFrontendController = $this->getTypoScriptFrontendController();
            $key = $this->extensionName . md5($data);
            if (!isset($typoScriptFrontendController->register[$key])) {
                $typoScriptFrontendController->register[$key] = $data;
                $this->response->addAdditionalHeaderData($data);
            }
        }
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    private function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
