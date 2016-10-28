<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer;

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


use BZgA\BzgaBeratungsstellensuche\Domain\Model\Entry;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\EntryNameConverter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use BZgA\BzgaBeratungsstellensuche\Domain\Model\ValueObject\ImageLink;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class EntryNormalizer extends GetSetMethodNormalizer
{
    /**
     * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
     * @inject
     */
    protected $countryZoneRepository;

    /**
     * @var \BZgA\BzgaBeratungsstellensuche\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

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

        $categoriesCallback = function () {
            return self::convertToObjectStorage($this->categoryRepository, func_get_args());
        };

        $logoCallback = function ($logo) {
            return new ImageLink($logo);
        };

        $this->setDenormalizeCallbacks(
            array(
                'state' => $stateCallback,
                'categories' => $categoriesCallback,
                'image' => $logoCallback,
            )
        );

        return parent::prepareForDenormalization($data);
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $array
     * @param string $method
     * @return ObjectStorage
     */
    public static function convertToObjectStorage(
        RepositoryInterface $repository,
        array $array,
        $method = 'findOneByExternalId'
    ) {
        $objectStorage = new ObjectStorage();
        if (is_array($array[0])) {
            foreach ($array[0] as $key => $item) {
                if (is_array($item)) {
                    foreach ($item as $id) {
                        $object = $repository->{$method}($id);
                        if ($object !== null) {
                            $objectStorage->attach($object);
                        }
                    }
                }
            }
        }

        return $objectStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Entry::class;
    }


}