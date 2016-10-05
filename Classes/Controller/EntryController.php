<?php


namespace BZgA\BzgaBeratungsstellensuche\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;

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
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Dto\Demand|null $demand
     */
    public function listAction(Demand $demand = null)
    {
        if (!$demand instanceof Demand) {
            $demand = $this->objectManager->get(Demand::class);
        }
        $entries = $this->entryRepository->findDemanded($demand);
        $religions = $this->religionRepository->findAll();
        $kilometers = $this->kilometerRepository->findKilometersBySettings($this->settings);
        $this->view->assignMultiple(compact('entries', 'demand', 'religions', 'kilometers'));
    }

    /**
     * @param \BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry|null $entry
     */
    public function showAction(Entry $entry = null)
    {
        // @TODO: What HTTP-Status do we have to send here at best? 404
        if (!$entry instanceof Entry) {
            $this->redirect('list');
        }

        $this->view->assign('entry', $entry);
    }

}