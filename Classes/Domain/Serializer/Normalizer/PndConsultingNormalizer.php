<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer;


use BZgA\BzgaBeratungsstellensuche\Domain\Model\PndConsulting;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\PndConsultingNameConverter;

class PndConsultingNormalizer extends GetSetMethodNormalizer
{

    /**
     * CategoryNormalizer constructor.
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     */
    public function __construct(ClassMetadataFactory $classMetadataFactory = null)
    {
        parent::__construct($classMetadataFactory, new PndConsultingNameConverter());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof PndConsulting;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === PndConsulting::class;
    }

}