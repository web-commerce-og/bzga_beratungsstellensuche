<?php


namespace BZgA\BzgaBeratungsstellensuche\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use BZgA\BzgaBeratungsstellensuche\Events;

class EntryController extends ActionController
{
    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository
     * @inject
     */
    protected $religionRepository;

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
     * @return void
     */
    public function initializeAction()
    {
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
        $religions = $this->religionRepository->findAll();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $assignedViewValues = compact('demand', 'religions', 'kilometers', 'categories');
        $assignedViewValues = $this->emitActionSignal(Events::FORM_ACTION_SIGNAL, $assignedViewValues);
        $this->view->assignMultiple($assignedViewValues);
    }

    /**
     * @return void
     */
    public function initializeListAction()
    {
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
        $religions = $this->religionRepository->findAll();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $categories = $this->categoryRepository->findAll();
        $assignedViewValues = compact('entries', 'demand', 'religions', 'kilometers', 'categories');
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

}