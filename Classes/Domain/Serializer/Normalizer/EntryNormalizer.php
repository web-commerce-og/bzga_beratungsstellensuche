<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer;


use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\Religion;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use BZgA\BzgaBeratungsstellensuche\Utility\Utility;
use SJBR\StaticInfoTables\Domain\Model\Language;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class EntryNormalizer extends GetSetMethodNormalizer
{
    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\ReligionRepository
     * @inject
     */
    protected $religionRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository
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
    }


    /**
     * @param array|object $data
     * @return array
     */
    protected function prepareForDenormalization($data)
    {
        $stateCallback = function ($externalId) {
            return $this->countryZoneRepository->findOneByExternalId($externalId);
        };

        $religionCallback = function ($religionInputId) {
            return $this->religionRepository->findOneByExternalId($religionInputId);
        };

        $pndLanguagesCallback = function () {
            $array = func_get_args();
            $objectStorage = new ObjectStorage();
            if (is_array($array[0])) {
                foreach ($array[0] as $key => $item) {
                    if (is_array($item)) {
                        foreach ($item as $id) {
                            $objectStorage->attach($this->languageRepository->findOneByExternalId($id));
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
                            $objectStorage->attach($this->categoryRepository->findOneByExternalId($id));
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
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Entry::class;
    }


}