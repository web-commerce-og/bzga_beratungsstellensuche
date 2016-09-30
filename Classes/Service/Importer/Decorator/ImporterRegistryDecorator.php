<?php


namespace BZgA\BzgaBeratungsstellensuche\Service\Importer\Decorator;

use BZgA\BzgaBeratungsstellensuche\Service\Importer\AbstractImporter;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\ImporterInterface;
use TYPO3\CMS\Core\Registry;

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
     * @return mixed|void
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