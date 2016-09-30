<?php


namespace BZgA\BzgaBeratungsstellensuche\Persistence\Mapper;


class DataMap
{

    /**
     * @var array
     */
    private $cachedTableNames = array();
    
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapFactory
     */
    private $dataMapFactory;

    public function __construct(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapFactory $dataMapFactory)
    {
        $this->dataMapFactory = $dataMapFactory;
    }

    /**
     * @param $className
     * @return mixed
     */
    public function getTableNameByClassName($className)
    {
        if (!isset($this->cachedTableNames[$className])) {
            $dataMap = $this->dataMapFactory->buildDataMap($className);
            $this->cachedTableNames[$className] = $dataMap->getTableName();
        }

        return $this->cachedTableNames[$className];
    }

}