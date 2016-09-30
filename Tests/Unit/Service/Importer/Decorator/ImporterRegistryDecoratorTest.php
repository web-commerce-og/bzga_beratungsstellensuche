<?php


namespace BZga\BzgaBeratungsstellensuche\Tests\Unit\Service\Importer\Decorator;


use BZgA\BzgaBeratungsstellensuche\Service\Importer\Decorator\ImporterRegistryDecorator;
use Bzga\BzgaBeratungsstellensuche\Service\Importer\ImporterInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Tests\UnitTestCase;

class ImporterRegistryDecoratorTest extends UnitTestCase
{

    /**
     * @var ImporterRegistryDecorator
     */
    protected $subject;

    /**
     * @var ImporterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $importer;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->importer = $this->getMock(ImporterInterface::class);
        $this->subject = new ImporterRegistryDecorator($this->importer);
        $this->registry = $this->getMock(Registry::class);
        $this->inject($this->subject, 'registry', $this->registry);
    }

    /**
     * @test
     * @dataProvider contentDataProvider
     */
    public function importWithAlreadyImportedContent($content)
    {
        $hash = md5($content);
        $this->registry->expects($this->once())->method('get')->willReturn($hash);
        $this->registry->expects($this->never())->method('set');
        $this->importer->expects($this->never())->method('import');

        $this->subject->import($content);
    }

    /**
     * @test
     * @dataProvider contentDataProvider
     * @param $content
     */
    public function importUpdatedContent($content)
    {
        $hash = md5('other content');
        $this->registry->expects($this->once())->method('get')->willReturn($hash);
        $this->registry->expects($this->once())->method('set');
        $this->importer->expects($this->once())->method('import');
        $this->subject->import($content);
    }


    /**
     * @return array
     */
    public function contentDataProvider()
    {
        return array(
            array('some fake content'),
        );
    }

}
