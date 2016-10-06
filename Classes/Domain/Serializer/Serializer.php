<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer;

use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\PndConsultingNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer as BaseSerializer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use BZgA\BzgaBeratungsstellensuche\Events;

class Serializer extends BaseSerializer
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * Serializer constructor.
     * @param array $normalizers
     * @param array $encoders
     */
    public function __construct(array $normalizers = array(), array $encoders = array())
    {
        if (empty($normalizers)) {

            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /* @var $objectManager ObjectManager */
            $normalizers = array(
                $objectManager->get(PndConsultingNormalizer::class),
                $objectManager->get(ReligionNormalizer::class),
                $objectManager->get(EntryNormalizer::class),
                $objectManager->get(CategoryNormalizer::class),
            );
        }
        if (empty($encoders)) {
            $encoders = array(
                new XmlEncoder('beratungsstellen'),
            );
        }

        // @TODO Working with DI
        if (!$this->signalSlotDispatcher instanceof Dispatcher) {
            $this->signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        }

        $normalizers = $this->emitAdditionalNormalizersSignal($normalizers);


        parent::__construct($normalizers, $encoders);
    }

    /**
     * @param array $normalizers
     * @return array
     */
    private function emitAdditionalNormalizersSignal(array $normalizers)
    {
        $signalArguments = array();
        $signalArguments['extendedNormalizers'] = array();

        $additionalNormalizers = $this->signalSlotDispatcher->dispatch(static::class,
            Events::ADDITIONAL_NORMALIZERS_SIGNAL, $signalArguments);

        return array_merge($normalizers, $additionalNormalizers['extendedNormalizers']);
    }

}