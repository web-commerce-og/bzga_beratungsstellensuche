<?php


namespace BZgA\BzgaBeratungsstellensuche\Tests\Unit\Domain\Serializer\NameConverter;


use BZgA\BzgaBeratungsstellensuche\Domain\Serializer\NameConverter\CategoryNameConverter;

class CategoryNameConverterTest extends AbstractNameConverterTest
{


    /**
     * @return void
     */
    protected function setUp()
    {
        $this->subject = new CategoryNameConverter();
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array('#text', 'title'),
            array('index', 'external_id'),
        );
    }

}