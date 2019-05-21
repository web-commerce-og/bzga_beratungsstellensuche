<?php

namespace Bzga\BzgaBeratungsstellensuche\Domain\Serializer\Normalizer;

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
use Bzga\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\BaseMappingNameConverter;
use Bzga\BzgaBeratungsstellensuche\Events;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer as BaseGetSetMethodNormalizer;

/**
 * @author Sebastian Schreiber
 */
class GetSetMethodNormalizer extends BaseGetSetMethodNormalizer
{

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher;

    /**
     * @var array
     */
    protected $denormalizeCallbacks;

    /**
     * Sets the {@link ClassMetadataFactoryInterface} to use.
     *
     * @param ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param NameConverterInterface|null $nameConverter
     */
    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null
    ) {
        $this->classMetadataFactory = $classMetadataFactory;
        if (null === $nameConverter) {
            $nameConverter = new BaseMappingNameConverter();
        }
        $this->nameConverter = $nameConverter;
        parent::__construct($classMetadataFactory, $nameConverter);
    }

    /**
     * Set normalization callbacks.
     *
     * @param callable[] $callbacks help normalize the result
     *
     * @return self
     *
     * @throws \InvalidArgumentException if a non-callable callback is set
     */
    public function setDenormalizeCallbacks(array $callbacks)
    {
        $callbacks = $this->emitDenormalizeCallbacksSignal($callbacks);
        foreach ($callbacks as $attribute => $callback) {
            if (!is_callable($callback)) {
                throw new \InvalidArgumentException(sprintf(
                    'The given callback for attribute "%s" is not callable.',
                    $attribute
                ));
            }
        }
        $this->denormalizeCallbacks = $callbacks;

        return $this;
    }

    /**
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $allowedAttributes = $this->getAllowedAttributes($class, $context, true);
        $normalizedData = $this->prepareForDenormalization($data);

        $reflectionClass = new \ReflectionClass($class);
        $object = $this->instantiateObject($normalizedData, $class, $context, $reflectionClass, $allowedAttributes);

        $classMethods = get_class_methods($object);
        foreach ($normalizedData as $attribute => $value) {
            if ($this->nameConverter) {
                $attribute = $this->nameConverter->denormalize($attribute);
            }

            $allowed = $allowedAttributes === false || in_array($attribute, $allowedAttributes);
            $ignored = in_array($attribute, $this->ignoredAttributes);

            if ($allowed && !$ignored) {
                $setter = 'set' . ucfirst($attribute);

                if (in_array($setter, $classMethods) && !$reflectionClass->getMethod($setter)->isStatic()) {
                    if (isset($this->denormalizeCallbacks[$attribute])) {
                        $value = call_user_func($this->denormalizeCallbacks[$attribute], $value);
                    }
                    if (null !== $value) {
                        $object->$setter($value);
                    }
                }
            }
        }

        return $object;
    }

    /**
     * @param array $callbacks
     * @return array
     */
    protected function emitDenormalizeCallbacksSignal(array $callbacks)
    {
        $signalArguments = [];
        $signalArguments['extendedCallbacks'] = [];

        $additionalCallbacks = $this->signalSlotDispatcher->dispatch(
            static::class,
            Events::DENORMALIZE_CALLBACKS_SIGNAL,
            $signalArguments
        );

        if (isset($additionalCallbacks['extendedCallbacks']) && $additionalCallbacks['extendedCallbacks']) {
            $callbacks = array_merge($callbacks, $additionalCallbacks['extendedCallbacks']);
        }

        return $callbacks;
    }
}
