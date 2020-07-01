<?php


namespace Bzga\BzgaBeratungsstellensuche\Property;

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
use Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * @author Sebastian Schreiber
 */
class PropertyMapper implements TypeConverterInterface
{

    /**
     * @var TypeConverterInterface[]
     */
    private $typeConverters = [];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function injectObjectManager(ObjectManagerInterface $objectManager): void
    {
        $this->objectManager = $objectManager;
        $this->initializeTypeConverters();
    }

    /**
     * @param mixed $source
     */
    public function supports($source, string $type = TypeConverterInterface::CONVERT_BEFORE): bool
    {
        foreach ($this->typeConverters as $typeConverter) {
            if (true === $typeConverter->supports($source, $type) && $this->converterSupportsType(
                $typeConverter,
                    $type
            )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $source
     *
     * @return mixed
     */
    public function convert($source, array $configuration = null)
    {
        /** @var $typeConverter TypeConverterInterface */
        foreach ($this->typeConverters as $typeConverter) {
            if (true === $typeConverter->supports($source)) {
                return $typeConverter->convert($source, $configuration);
            }
        }

        return $source;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getRegisteredTypeConverters(): array
    {
        return ExtensionManagementUtility::getRegisteredTypeConverters();
    }

    private function converterSupportsType(TypeConverterInterface $typeConverter, string $type): bool
    {
        $interfaces = class_implements($typeConverter);
        switch ($type) {
            case TypeConverterInterface::CONVERT_AFTER:
                $className = TypeConverterAfterInterface::class;
                break;
            default:
                $className = TypeConverterBeforeInterface::class;
                break;
        }
        return in_array($className, $interfaces, true) ? true : false;
    }

    private function initializeTypeConverters(): void
    {
        foreach ($this->getRegisteredTypeConverters() as $typeConverterClassName) {
            $this->typeConverters[] = $this->objectManager->get($typeConverterClassName);
        }
    }
}
