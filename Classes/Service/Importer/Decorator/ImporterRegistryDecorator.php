<?php


namespace Bzga\BzgaBeratungsstellensuche\Service\Importer\Decorator;

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
use Bzga\BzgaBeratungsstellensuche\Service\Importer\AbstractImporter;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\ImporterInterface;
use TYPO3\CMS\Core\Registry;

/**
 * @author Sebastian Schreiber
 */
class ImporterRegistryDecorator extends AbstractImporter
{

    /**
     * @var string
     */
    const REGISTRY_NAMESPACE = 'tx_bzgaberatungsstellensuche';

    /**
     * @var string
     */
    const REGISTRY_KEY = 'import';

    /**
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * @var Registry
     * @inject
     */
    protected $registry;

    /**
     * ImporterRegistryDecorator constructor.
     * @param ImporterInterface $importer
     */
    public function __construct(ImporterInterface $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @param $content
     * @param int $pid
     * @return void
     */
    public function import($content, $pid = 0)
    {
        # If nothing has changed in the content, we do nothing
        $hash = md5($content);
        if ($hash !== $this->registry->get(self::REGISTRY_NAMESPACE, self::REGISTRY_KEY)) {
            $this->importer->import($content, $pid);
            $this->registry->set(self::REGISTRY_NAMESPACE, self::REGISTRY_KEY, $hash);
        }
    }
}
