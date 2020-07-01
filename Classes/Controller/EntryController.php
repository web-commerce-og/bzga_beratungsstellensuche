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
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository;
use Bzga\BzgaBeratungsstellensuche\Domain\Repository\KilometerRepository;
use Bzga\BzgaBeratungsstellensuche\Events;
use Bzga\BzgaBeratungsstellensuche\Service\SessionService;
use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * @author Sebastian Schreiber
 */
class EntryController extends ActionController
{
    /**
     * @var int
     */
    public const GERMANY_ISOCODENUMBER = 276;

    /**
     * @var string
     */
    const TYPE_JS = 'js';

    /**
     * @var string
     */
    const TYPE_CSS = 'css';

    /**
     * @var EntryRepository
     */
    protected $entryRepository;

    /**
     * @var KilometerRepository
     */
    protected $kilometerRepository;

    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CountryZoneRepository
     */
    protected $countryZoneRepository;

    /**
     * The response which will be returned by this action controller
     *
     * @var Response
     * @api
     */
    protected $response;

    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function injectCountryZoneRepository(CountryZoneRepository $countryZoneRepository): void
    {
        $this->countryZoneRepository = $countryZoneRepository;
    }

    public function injectEntryRepository(EntryRepository $entryRepository): void
    {
        $this->entryRepository = $entryRepository;
    }

    public function injectKilometerRepository(KilometerRepository $kilometerRepository): void
    {
        $this->kilometerRepository = $kilometerRepository;
    }

    public function injectSessionService(SessionService $sessionService): void
    {
        $this->sessionService = $sessionService;
    }

    public function initializeAction(): void
    {
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

    public function initializeFormAction(): void
    {
        $this->resetDemand();
        $this->addDemandRequestArgumentFromSession();
    }

    public function formAction(Demand $demand = null): void
    {
        if (!$demand instanceof Demand) {
            $demand = $this->objectManager->get(Demand::class);
        }
        $countryZonesGermany = $this->findCountryZonesForGermany();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $random = random_int(0, 1000);
        $assignedViewValues = compact('demand', 'kilometers', 'categories', 'countryZonesGermany', 'random');
        $assignedViewValues = $this->emitActionSignal(Events::FORM_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    public function initializeListAction(): void
    {
        $this->resetDemand();
        if (!$this->request->hasArgument('demand')) {
            $this->addDemandRequestArgumentFromSession();
        } else {
            $this->sessionService->writeToSession($this->request->getArgument('demand'));
        }
    }

    public function listAction(Demand $demand = null): void
    {
        if (!$demand instanceof Demand) {
            $demand = $this->objectManager->get(Demand::class);
        }

        if (!$demand->hasValidCoordinates()) {
            $this->redirect('form', 'Entry', 'bzga_beratungsstellensuche', ['demand' => $demand], $this->settings['backPid']);
        }

        $entries = $this->entryRepository->findDemanded($demand);
        $countryZonesGermany = $this->findCountryZonesForGermany();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $assignedViewValues = compact('entries', 'demand', 'kilometers', 'categories', 'countryZonesGermany');
        $assignedViewValues = $this->emitActionSignal(Events::LIST_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    public function initializeShowAction(): void
    {
        $this->addDemandRequestArgumentFromSession();
    }

    public function showAction(Entry $entry = null, Demand $demand = null): void
    {
        if (!$entry instanceof Entry) {
            // @TODO: Add possibility to hook into here.
            $this->redirect('list', null, null, [], $this->settings['listPid'], 0, 404);
        }

        $assignedViewValues = compact('entry', 'demand');
        $assignedViewValues = $this->emitActionSignal(Events::SHOW_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    public function autocompleteAction(string $q): void
    {
        $this->view->assign('entries', $this->entryRepository->findByQuery($q));
        $this->view->assign('q', $q);
    }

    private function findCountryZonesForGermany(): array
    {
        if (false === GeneralUtility::inList($this->settings['formFields'], 'countryZonesGermany')) {
            return [];
        }
        $country = new Country();
        $country->setIsoCodeNumber(self::GERMANY_ISOCODENUMBER);

        return $this->countryZoneRepository->findByCountryOrderedByLocalizedName($country);
    }

    private function emitInitializeActionSignal(array $signalArguments): void
    {
        $this->signalSlotDispatcher->dispatch(static::class, Events::INITIALIZE_ACTION_SIGNAL, $signalArguments);
    }

    private function emitActionSignal(string $signalName, array $assignedViewValues): array
    {
        $signalArguments = [];
        $signalArguments['extendedVariables'] = [];

        $additionalViewValues = $this->signalSlotDispatcher->dispatch(static::class, $signalName, $signalArguments);

        return array_merge($assignedViewValues, $additionalViewValues['extendedVariables']);
    }

    private function addDemandRequestArgumentFromSession(): void
    {
        $demand = $this->sessionService->restoreFromSession();
        if ($demand) {
            $this->request->setArgument('demand', $demand);
        }
    }

    private function resetDemand(): void
    {
        if ($this->request->hasArgument('reset')) {
            $this->sessionService->cleanUpSession();
            $this->request->setArgument('demand', null);
        }
    }
}
