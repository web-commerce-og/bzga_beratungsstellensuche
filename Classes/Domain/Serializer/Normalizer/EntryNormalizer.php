<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer;


use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use BZgA\BzgaBeratungsstellensuche\Utility\Utility;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\CountryZoneRepository;
use SJBR\StaticInfoTables\Domain\Model\Language;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class EntryNormalizer extends GetSetMethodNormalizer
{
    /**
     * @var CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @var ReligionRepository
     * @inject
     */
    protected $religionRepository;

    /**
     * @var CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var LanguageRepository
     * @inject
     */
    protected $languageRepository;


    /**
     * EntryNormalizer constructor.
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     */
    public function __construct(ClassMetadataFactory $classMetadataFactory = null)
    {
        parent::__construct($classMetadataFactory, new EntryNameConverter());

        $categoriesCallback = function ($storage) {
            $array = array();
            foreach ($storage as $item) {
                /* @var $item Category */
                $array[] = $item->getExternalId();
            }

            return $array;
        };


        $religionCallback = function (Religion $religion = null) {
            if ($religion instanceof Religion) {
                return $religion->getExternalId();
            }
        };

        $pndLanguagesCallback = function ($storage) {
            $array = array();
            foreach ($storage as $item) {
                /* @var $item Language */
                $array[] = Utility::mapToIndexFromStaticLanguagesTable($item->getIsoCodeA2());
            }

            return $array;
        };

        $this->setCallbacks(
            array(
                'categories' => $categoriesCallback,
                'religiousDenomination' => $religionCallback,
                'pndLanguages' => $pndLanguagesCallback,
            )
        );
    }


    /**
     * @param array|object $data
     * @return array
     */
    protected function prepareForDenormalization($data)
    {
        $stateCallback = function ($stateInputId) {
            try {
                $znCode = Utility::mapToStaticCountryZoneTable($stateInputId);
                if ($this->countryZoneRepository instanceof CountryZoneRepository) {
                    return $this->countryZoneRepository->findOneByZnCodeFromGermany($znCode);
                }
            } catch (\InvalidArgumentException $e) {
                return;
            }
        };

        $religionCallback = function ($religionInputId) {
            if ($this->religionRepository instanceof ReligionRepository) {
                return $this->religionRepository->findOneByExternalId($religionInputId);
            }
        };

        $pndLanguagesCallback = function ($array) {
            $array = func_get_args();
            $objectStorage = new ObjectStorage();
            if (is_array($array[0])) {
                foreach ($array[0] as $key => $item) {
                    if (is_array($item)) {
                        foreach ($item as $id) {
                            try {
                                $iso2Code = Utility::mapToStaticLanguagesTable($id);
                                if ($this->languageRepository instanceof LanguageRepository) {
                                    $objectStorage->attach($this->languageRepository->findOneByIsoCodes($iso2Code));
                                }
                            } catch (\InvalidArgumentException $e) {

                            }
                        }
                    }
                }
            }

            return $objectStorage;
        };

        $categoriesCallback = function () {
            $array = func_get_args();
            $objectStorage = new ObjectStorage();
            if (is_array($array[0])) {
                foreach ($array[0] as $key => $item) {
                    if (is_array($item)) {
                        foreach ($item as $id) {
                            if ($this->categoryRepository instanceof CategoryRepository) {
                                $objectStorage->attach($this->categoryRepository->findOneByExternalId($id));
                            }
                        }
                    }
                }
            }

            return $objectStorage;
        };

        $this->setDenormalizeCallbacks(
            array(
                'state' => $stateCallback,
                'religiousDenomination' => $religionCallback,
                'pndLanguages' => $pndLanguagesCallback,
                'categories' => $categoriesCallback,
            )
        );

        return parent::prepareForDenormalization($data);
    }

    /**
     * @param object $object
     * @param null $format
     * @param array $context
     * @return array|\Symfony\Component\Serializer\Normalizer\scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $attributes = parent::normalize($object, $format, $context);

        return $attributes;
    }

    /**
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {

        $entry = parent::denormalize($data, $class, $format, $context);

        /* @var $entry Entry */
        return $entry;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof Entry;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Entry::class;
    }


}