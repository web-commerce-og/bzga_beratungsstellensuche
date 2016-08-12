<?php


namespace BZgA\BzgaBeratungsstellensuche\Service;


class CacheService
{

    /**
     * @var \TYPO3\CMS\Extbase\Service\CacheService
     * @inject
     */
    protected $cacheService;

    /**
     * @return void
     */
    public function clearCache()
    {
        $this->cacheService->clearPageCache();
    }

}