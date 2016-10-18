<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Persistence\Mapper;


use BZgA\BzgaBeratungsstellensuche\Persistence\Mapper\DataMap;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapFactory;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMap as CoreDataMap;

class DataMapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DataMap
     */
    private $subject;

    /**
     * @var DataMapFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataMapFactory;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->dataMapFactory = $this->getMock(DataMapFactory::class);
        $this->subject = new DataMap($this->dataMapFactory);
    }

    /**
     * @test
     */
    public function getTableNameByClassNameCalledOnceForSameClassName()
    {
        $expectedTableName = 'tablename';
        $dataMap = $this->getMock(CoreDataMap::class, array(), array(), '', false);
        $this->dataMapFactory->expects($this->once())->method('buildDataMap')->willReturn($dataMap);
        $dataMap->expects($this->once())->method('getTableName')->willReturn($expectedTableName);
        for ($i = 0; $i <= 5; $i++) {
            $tableName = $this->subject->getTableNameByClassName(__CLASS__);
        }
        $this->assertSame($expectedTableName, $tableName);

    }

}
