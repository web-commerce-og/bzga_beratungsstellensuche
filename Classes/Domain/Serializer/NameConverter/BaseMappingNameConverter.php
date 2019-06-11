<?php


namespace Bzga\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter;

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
use Bzga\BzgaBeratungsstellensuche\Events;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * @author Sebastian Schreiber
 */
class BaseMappingNameConverter extends CamelCaseToSnakeCaseNameConverter
{

    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * Mapping of names, left side incoming names in xml|array, right side name for object
     * @var array
     */
    protected $mapNames = [
        '#text' => 'title',
        'index' => 'external_id',
    ];

    /**
     * @var array
     */
    protected $mapNamesFlipped = [];

    /**
     * EntryNameConverter constructor.
     *
     * @param array|null $attributes
     * @param bool $lowerCamelCase
     * @param Dispatcher|object|null $signalSlotDispatcher
     */
    public function __construct(array $attributes = null, $lowerCamelCase = true, Dispatcher $signalSlotDispatcher = null)
    {
        parent::__construct($attributes, $lowerCamelCase);

        if (null === $signalSlotDispatcher) {
            $signalSlotDispatcher = GeneralUtility::makeInstance(ObjectManager::class)->get(Dispatcher::class);
        }

        $this->signalSlotDispatcher = $signalSlotDispatcher;

        $this->emitMapNamesSignal();
        $this->mapNamesFlipped();
    }

    /**
     * @param array $mapNames
     */
    public function addAdditionalMapNames(array $mapNames)
    {
        ArrayUtility::mergeRecursiveWithOverrule($this->mapNames, $mapNames);
        $this->mapNamesFlipped();
    }

    /**
     */
    private function mapNamesFlipped()
    {
        $this->mapNamesFlipped = array_flip($this->mapNames);
    }

    /**
     * @param array|string|null $propertyName
     * @return mixed|string|null
     */
    public function denormalize($propertyName)
    {
        if (isset($this->mapNames[$propertyName])) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($this->mapNames[$propertyName]);
        }

        return $propertyName;
    }

    /**
     */
    protected function emitMapNamesSignal()
    {
        $signalArguments = [];
        $signalArguments['extendedMapNames'] = [];

        $mapNames = $this->signalSlotDispatcher->dispatch(static::class, Events::SIGNAL_MAP_NAMES, $signalArguments);
        $this->addAdditionalMapNames($mapNames['extendedMapNames']);
    }
}
