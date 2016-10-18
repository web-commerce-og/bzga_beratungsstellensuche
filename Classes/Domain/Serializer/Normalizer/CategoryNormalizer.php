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

use BZgA\BzgaBeratungsstellensuche\Domain\Model\Category;
use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\CategoryNameConverter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class CategoryNormalizer extends GetSetMethodNormalizer
{

    /**
     * CategoryNormalizer constructor.
     * @param null|\Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface $classMetadataFactory
     */
    public function __construct(ClassMetadataFactory $classMetadataFactory = null)
    {
        parent::__construct($classMetadataFactory, new CategoryNameConverter());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Category::class;
    }

}