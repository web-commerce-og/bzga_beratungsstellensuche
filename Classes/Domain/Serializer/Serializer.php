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

class Serializer extends BaseSerializer
{

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
        parent::__construct($normalizers, $encoders);
    }

}