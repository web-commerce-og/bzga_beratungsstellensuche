<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer;

use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\CategoryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\EntryNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\PndConsultingNormalizer;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer\ReligionNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Encoder\EntryXmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Serializer as BaseSerializer;

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
            $reader = new AnnotationReader();
            AnnotationReader::addGlobalIgnoredName('validate');
            AnnotationReader::addGlobalIgnoredName('inject');

            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader($reader));
            $normalizers = array(
                new PndConsultingNormalizer($classMetadataFactory),
                new ReligionNormalizer($classMetadataFactory),
                new EntryNormalizer($classMetadataFactory),
                new CategoryNormalizer($classMetadataFactory),
            );
        }
        if (empty($encoders)) {
            $encoders = array(
                new EntryXmlEncoder('beratungsstellen'),
            );
        }
        parent::__construct($normalizers, $encoders);
    }

}