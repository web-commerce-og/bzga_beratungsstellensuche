<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter;

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


use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use BZgA\BzgaBeratungsstellensuche\Events;

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
class BaseMappingNameConverter extends CamelCaseToSnakeCaseNameConverter
{


    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * Mapping of names, left side incoming names in xml|array, right side name for object
     * @var array
     */
    protected $mapNames = array(
        '#text' => 'title',
        'index' => 'external_id',
    );

    /**
     * @var array
     */
    protected $mapNamesFlipped = array();

    /**
     * EntryNameConverter constructor.
     * @param array|null $attributes
     * @param bool $lowerCamelCase
     */
    public function __construct(array $attributes = null, $lowerCamelCase = true)
    {
        parent::__construct($attributes, $lowerCamelCase);
        // @TODO Working with DI
        if (!$this->signalSlotDispatcher instanceof Dispatcher) {
            $this->signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        }
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
     * @return void
     */
    private function mapNamesFlipped()
    {
        $this->mapNamesFlipped = array_flip($this->mapNames);
    }

    /**
     * @param string $propertyName
     * @return mixed|string
     */
    public function denormalize($propertyName)
    {
        if (isset($this->mapNames[$propertyName])) {
            $propertyName = $this->mapNames[$propertyName];
            $propertyName = parent::denormalize($propertyName);
        }

        return $propertyName;
    }

    /**
     * @return void
     */
    protected function emitMapNamesSignal()
    {
        $signalArguments = array();
        $signalArguments['extendedMapNames'] = array();

        $mapNames = $this->signalSlotDispatcher->dispatch(static::class, Events::SIGNAL_MapNames, $signalArguments);
        $this->addAdditionalMapNames($mapNames['extendedMapNames']);
    }


}