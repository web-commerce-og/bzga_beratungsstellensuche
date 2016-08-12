<?php


namespace BZgA\BzgaBeratungsstellensuche\Domain\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\XmlEncoder as BaseXmlEncoder;

class EntryXmlEncoder extends BaseXmlEncoder
{

    /**
     * @param mixed $data
     * @param string $format
     * @param array $context
     * @return string|\Symfony\Component\Serializer\Encoder\scalar
     */
    public function encode($data, $format, array $context = array())
    {
        // @TODO: We have to manipulate the xml output later
        $xml = parent::encode($data, $format, $context);
        return $xml;
    }

}